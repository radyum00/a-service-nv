<?php
session_start();
$CONFIG = require __DIR__ . '/data/config.php';
$contentFile = $CONFIG['content_file'];

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Login
if (!empty($_POST['password']) && empty($_SESSION['admin'])) {
    if (hash_equals($CONFIG['password'], $_POST['password'])) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    }
    $error = 'Неверный пароль';
}

$isAdmin = !empty($_SESSION['admin']);

// Save
if ($isAdmin && ($_POST['action'] ?? '') === 'save') {
    $data = json_decode($_POST['content'] ?? '{}', true);
    if (is_array($data)) {
        // Бэкап
        if (file_exists($contentFile)) {
            @copy($contentFile, $contentFile . '.bak');
        }
        file_put_contents($contentFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $saved = true;
    }
}

// Upload image
if ($isAdmin && ($_POST['action'] ?? '') === 'upload') {
    header('Content-Type: application/json');
    if (empty($_FILES['image']['tmp_name'])) { echo json_encode(['error'=>'no file']); exit; }
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','svg','webp','gif'])) { echo json_encode(['error'=>'bad type']); exit; }
    @mkdir(__DIR__ . '/uploads', 0755, true);
    $name = 'uploads/' . bin2hex(random_bytes(8)) . '.' . $ext;
    if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/' . $name)) {
        echo json_encode(['url' => $name]);
    } else {
        echo json_encode(['error' => 'move failed']);
    }
    exit;
}

// Login page
if (!$isAdmin) {
    ?><!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Вход — CMS</title>
<style>
body{font-family:system-ui,sans-serif;background:#f6f8fb;display:grid;place-items:center;min-height:100vh;margin:0}
.login{background:#fff;padding:40px;border-radius:20px;box-shadow:0 20px 50px -20px rgba(16,24,40,.2);width:100%;max-width:380px}
h1{margin:0 0 24px;font-size:22px}
input{width:100%;padding:14px;border:1px solid #d4dce9;border-radius:10px;font-size:15px;margin-bottom:16px;box-sizing:border-box}
button{width:100%;padding:14px;background:#1b7ff2;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer}
button:hover{background:#1468d8}
.err{color:#dc2626;font-size:14px;margin-bottom:12px}
</style></head><body>
<form class="login" method="post">
    <h1>🔒 Админ-панель</h1>
    <?php if (!empty($error)): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <input type="password" name="password" placeholder="Пароль" required autofocus>
    <button type="submit">Войти</button>
</form>
</body></html><?php
    exit;
}

// Load content
$content = json_decode(@file_get_contents($contentFile), true);
if (!is_array($content)) $content = [];
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>CMS — А-Сервис</title>
<style>
*{box-sizing:border-box}
body{font-family:system-ui,-apple-system,sans-serif;background:#f6f8fb;margin:0;color:#343d4d;line-height:1.5}
.top{background:#101828;color:#fff;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:10}
.top h1{margin:0;font-size:16px;font-weight:800}
.top a{color:#b0bed4;font-size:14px;text-decoration:none;margin-left:16px}
.top a:hover{color:#fff}
.wrap{max-width:1000px;margin:0 auto;padding:24px}
.section{background:#fff;border-radius:16px;padding:24px;margin-bottom:20px;box-shadow:0 2px 10px -4px rgba(16,24,40,.1)}
.section h2{margin:0 0 16px;font-size:18px;color:#1b7ff2;border-bottom:1px solid #eaeef5;padding-bottom:10px}
label{display:block;font-size:13px;font-weight:600;color:#516381;margin-bottom:6px}
input[type=text],input[type=url],textarea{width:100%;padding:10px 12px;border:1px solid #d4dce9;border-radius:8px;font-size:14px;font-family:inherit;margin-bottom:14px}
textarea{min-height:80px;resize:vertical}
input:focus,textarea:focus{outline:none;border-color:#1b7ff2;box-shadow:0 0 0 3px rgba(27,127,242,.15)}
.row{display:grid;gap:12px;margin-bottom:12px}
@media(min-width:640px){.row-2{grid-template-columns:1fr 1fr}.row-3{grid-template-columns:2fr 1fr 1fr}}
.card{background:#f6f8fb;border-radius:12px;padding:16px;margin-bottom:12px;position:relative}
.card-title{font-size:13px;font-weight:700;color:#101828;margin-bottom:10px}
.btn{padding:10px 16px;border-radius:8px;border:none;font-weight:700;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
.btn-primary{background:#1b7ff2;color:#fff}
.btn-primary:hover{background:#1468d8}
.btn-danger{background:#fee2e2;color:#b91c1c;padding:6px 10px;font-size:12px}
.btn-danger:hover{background:#fecaca}
.btn-add{background:#ecfdf5;color:#047857;border:1px dashed #6ee7b7;width:100%;padding:12px;margin-top:8px}
.btn-add:hover{background:#d1fae5}
.btn-ghost{background:#fff;border:1px solid #d4dce9;color:#516381;padding:8px 14px;font-size:13px}
.save-bar{position:sticky;bottom:16px;background:#101828;color:#fff;border-radius:12px;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 10px 40px -10px rgba(16,24,40,.4);z-index:5;margin-top:24px}
.save-bar .ok{color:#86efac;font-size:14px}
.save-bar button{background:#f59e0b;color:#101828;border:none;padding:12px 24px;border-radius:10px;font-weight:800;font-size:15px;cursor:pointer}
.save-bar button:hover{background:#fbbf24}
.item-row{display:grid;grid-template-columns:1fr 120px 100px 32px;gap:8px;align-items:start;margin-bottom:8px}
.item-row input{margin:0}
.thumb{width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #d4dce9;margin-top:6px}
.upload-btn{position:relative;overflow:hidden;display:inline-block}
.upload-btn input{position:absolute;inset:0;opacity:0;cursor:pointer}
</style>
</head>
<body>
<div class="top">
    <h1>CMS — А-Сервис</h1>
    <div>
        <a href="index.php" target="_blank">👁 Открыть сайт</a>
        <a href="admin.php?logout=1">🚪 Выйти</a>
    </div>
</div>

<div class="wrap" id="app"></div>

<form id="saveForm" method="post" style="display:none">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="content" id="contentField">
</form>

<div class="wrap" style="padding-top:0">
    <div class="save-bar">
        <div class="ok" id="status">Готово к редактированию</div>
        <button type="button" onclick="save()">💾 Сохранить</button>
    </div>
</div>

<script>
const DATA = <?= json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

const ICONS = ['clock','shield','truck','calendar','wrench','sparkles','washer','star'];
const esc = (s='') => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function field(label, path, value, type='text') {
    const val = esc(value);
    const attr = type === 'textarea' ? '' : ` value="${val}"`;
    const input = type === 'textarea'
        ? `<textarea data-path="${path}">${val}</textarea>`
        : `<input type="${type}" data-path="${path}"${attr}>`;
    return `<label>${label}</label>${input}`;
}

function imageField(label, path, value) {
    return `<label>${label}</label>
        <div style="display:flex;gap:12px;align-items:start">
            <input type="text" data-path="${path}" value="${esc(value)}" placeholder="/uploads/photo.jpg или URL">
            <div class="upload-btn btn btn-ghost" style="white-space:nowrap">
                📁 Загрузить
                <input type="file" accept="image/*" onchange="uploadImage(this, '${path}')">
            </div>
        </div>
        ${value ? `<img src="${esc(value)}" class="thumb">` : ''}`;
}

function renderHero() {
    const h = DATA.hero || {};
    return `<div class="section">
        <h2>🚀 Hero-секция</h2>
        ${field('Надзаголовок (бейдж)', 'hero.eyebrow', h.eyebrow)}
        ${field('Заголовок', 'hero.title', h.title)}
        ${field('Акцент в заголовке', 'hero.titleAccent', h.titleAccent)}
        ${field('Подзаголовок', 'hero.subtitle', h.subtitle, 'textarea')}
        ${field('Кнопка (текст)', 'hero.ctaLabel', h.ctaLabel)}
        ${field('Вторая кнопка (текст)', 'hero.secondaryLabel', h.secondaryLabel)}
        ${field('Телефон (кнопка)', 'hero.phone', h.phone)}
        ${field('Строка под кнопками', 'hero.note', h.note)}
        <label>Короткие преимущества (по одному в строке)</label>
        <textarea data-path="hero.highlights" data-type="array">${esc((h.highlights||[]).join('\n'))}</textarea>
        ${imageField('Картинка', 'hero.image', h.image)}
        ${field('Alt-текст картинки', 'hero.imageAlt', h.imageAlt)}
        ${field('Подпись на картинке (заголовок)', 'hero.imageTitle', h.imageTitle)}
        ${field('Подпись на картинке (подпись)', 'hero.imageText', h.imageText)}
    </div>`;
}

function renderAdvantages() {
    const a = DATA.advantages || {};
    const cards = a.cards || [];
    return `<div class="section">
        <h2>⭐ Преимущества</h2>
        ${field('Надзаголовок', 'advantages.eyebrow', a.eyebrow)}
        ${field('Заголовок', 'advantages.title', a.title)}
        ${field('Описание', 'advantages.description', a.description, 'textarea')}
        <label>Карточки</label>
        <div id="advList">${cards.map((c,i) => advCard(c,i)).join('')}</div>
        <button type="button" class="btn btn-add" onclick="addAdv()">+ Добавить карточку</button>
    </div>`;
}

function advCard(c, i) {
    return `<div class="card" data-adv-card>
        <div class="card-title">Карточка ${i+1}</div>
        <div class="row row-3">
            <div><label>Заголовок</label><input type="text" data-field="title" value="${esc(c.title)}"></div>
            <div><label>Иконка</label>
                <select data-field="icon" style="width:100%;padding:10px;border:1px solid #d4dce9;border-radius:8px">
                    ${ICONS.map(n => `<option value="${n}" ${c.icon===n?'selected':''}>${n}</option>`).join('')}
                </select>
            </div>
            <div><button type="button" class="btn btn-danger" onclick="this.closest('[data-adv-card]').remove()">✕</button></div>
        </div>
        <label>Описание</label>
        <textarea data-field="description">${esc(c.description)}</textarea>
    </div>`;
}

function addAdv() {
    const i = document.querySelectorAll('[data-adv-card]').length;
    document.getElementById('advList').insertAdjacentHTML('beforeend', advCard({icon:'sparkles',title:'',description:''}, i));
}

function renderPrices() {
    const p = DATA.prices || {};
    const groups = p.groups || [];
    return `<div class="section">
        <h2>💰 Прайс-лист</h2>
        ${field('Надзаголовок', 'prices.eyebrow', p.eyebrow)}
        ${field('Заголовок', 'prices.title', p.title)}
        ${field('Описание', 'prices.description', p.description, 'textarea')}
        ${field('Телефон для CTA', 'prices.phone', p.phone)}
        ${field('Надзаголовок CTA', 'prices.ctaEyebrow', p.ctaEyebrow)}
        ${field('Заголовок CTA', 'prices.ctaTitle', p.ctaTitle)}
        ${field('Текст CTA', 'prices.ctaText', p.ctaText, 'textarea')}
        ${field('Кнопка CTA', 'prices.ctaLabel', p.ctaLabel)}
        ${field('Примечание под прайсом', 'prices.note', p.note, 'textarea')}
        <label>Категории услуг</label>
        <div id="priceGroups">${groups.map((g,i)=>priceGroup(g,i)).join('')}</div>
        <button type="button" class="btn btn-add" onclick="addPriceGroup()">+ Добавить категорию</button>
    </div>`;
}

function priceGroup(g, gi) {
    const items = g.items || [];
    return `<div class="card" data-price-group>
        <div class="row row-2" style="grid-template-columns:1fr auto;align-items:end">
            <div><label>Название категории</label><input type="text" data-field="title" value="${esc(g.title)}"></div>
            <button type="button" class="btn btn-danger" onclick="this.closest('[data-price-group]').remove()">✕ Удалить</button>
        </div>
        <label>Услуги</label>
        <div data-price-items>
            ${items.map(it=>priceItem(it)).join('')}
        </div>
        <button type="button" class="btn btn-add" onclick="addPriceItem(this)">+ Добавить услугу</button>
    </div>`;
}

function priceItem(it) {
    return `<div class="item-row" data-price-item>
        <input type="text" data-field="name" placeholder="Название" value="${esc(it.name||'')}">
        <input type="text" data-field="price" placeholder="Цена" value="${esc(it.price||'')}">
        <input type="text" data-field="note" placeholder="Примечание" value="${esc(it.note||'')}">
        <button type="button" class="btn btn-danger" onclick="this.closest('[data-price-item]').remove()">✕</button>
    </div>`;
}

function addPriceGroup() {
    document.getElementById('priceGroups').insertAdjacentHTML('beforeend', priceGroup({title:'', items:[{name:'',price:'',note:''}]}, 0));
}
function addPriceItem(btn) {
    btn.previousElementSibling.insertAdjacentHTML('beforeend', priceItem({}));
}

function renderAbout() {
    const a = DATA.about || {};
    return `<div class="section">
        <h2>🏢 О компании</h2>
        ${field('Надзаголовок', 'about.eyebrow', a.eyebrow)}
        ${field('Заголовок', 'about.title', a.title)}
        ${field('Текст (абзацы через пустую строку)', 'about.text', a.text, 'textarea')}
        <label>Тезисы (по одному в строке)</label>
        <textarea data-path="about.bullets" data-type="array">${esc((a.bullets||[]).join('\n'))}</textarea>
        <label>Цифры (формат: значение | подпись — по одному в строке)</label>
        <textarea data-path="about.facts" data-type="facts">${esc((a.facts||[]).map(f=>`${f.value} | ${f.label}`).join('\n'))}</textarea>
        ${imageField('Картинка', 'about.image', a.image)}
        ${field('Alt-текст', 'about.imageAlt', a.imageAlt)}
        ${field('Подпись', 'about.imageCaption', a.imageCaption)}
    </div>`;
}

function renderContacts() {
    const c = DATA.contacts || {};
    const phones = c.phones || [];
    return `<div class="section">
        <h2>📞 Контакты</h2>
        ${field('Надзаголовок', 'contacts.eyebrow', c.eyebrow)}
        ${field('Заголовок', 'contacts.title', c.title)}
        ${field('Описание', 'contacts.description', c.description, 'textarea')}
        <label>Телефоны</label>
        <div id="phonesList">${phones.map((p,i)=>phoneCard(p,i)).join('')}</div>
        <button type="button" class="btn btn-add" onclick="addPhone()">+ Добавить телефон</button>
        ${field('Режим работы', 'contacts.workHours', c.workHours)}
        ${field('Примечание к режиму', 'contacts.workHoursNote', c.workHoursNote)}
        ${field('Адрес / география', 'contacts.address', c.address)}
        ${field('Ссылка 2ГИС', 'contacts.mapUrl', c.mapUrl, 'url')}
        ${field('Текст кнопки 2ГИС', 'contacts.mapLabel', c.mapLabel)}
        ${field('Ссылка ВК', 'contacts.vkUrl', c.vkUrl, 'url')}
        ${field('Текст кнопки ВК', 'contacts.vkLabel', c.vkLabel)}
        ${field('Заголовок CTA', 'contacts.ctaTitle', c.ctaTitle)}
        ${field('Текст CTA', 'contacts.ctaText', c.ctaText, 'textarea')}
        ${field('Кнопка CTA', 'contacts.ctaLabel', c.ctaLabel)}
    </div>`;
}

function phoneCard(p, i) {
    return `<div class="card" data-phone>
        <div class="row row-3" style="grid-template-columns:1fr 1fr 1fr auto">
            <div><label>Метка</label><input type="text" data-field="label" value="${esc(p.label||'')}"></div>
            <div><label>Телефон</label><input type="text" data-field="phone" value="${esc(p.phone||'')}"></div>
            <div><label>Примечание</label><input type="text" data-field="note" value="${esc(p.note||'')}"></div>
            <div style="align-self:end"><button type="button" class="btn btn-danger" onclick="this.closest('[data-phone]').remove()">✕</button></div>
        </div>
    </div>`;
}
function addPhone() {
    document.getElementById('phonesList').insertAdjacentHTML('beforeend', phoneCard({}, 0));
}

function renderFooter() {
    const f = DATA.footer || {};
    const s = DATA.site || {};
    return `<div class="section">
        <h2>📄 Футер и общие данные</h2>
        ${field('Название компании', 'site.companyName', s.companyName)}
        ${field('Слоган', 'site.tagline', s.tagline)}
        ${field('Город', 'site.city', s.city)}
        ${field('Регион', 'site.region', s.region)}
        <hr style="border:none;border-top:1px solid #eaeef5;margin:16px 0">
        ${field('Название в футере', 'footer.companyName', f.companyName)}
        ${field('Описание в футере', 'footer.tagline', f.tagline, 'textarea')}
        ${field('Телефон в футере', 'footer.phone', f.phone)}
        ${field('Юридическая сноска', 'footer.legalNote', f.legalNote, 'textarea')}
    </div>`;
}

// ==== RENDER ====
document.getElementById('app').innerHTML =
    renderHero() + renderAdvantages() + renderPrices() + renderAbout() + renderContacts() + renderFooter();

// ==== COLLECT & SAVE ====
function getPath(obj, path) {
    return path.split('.').reduce((o,k) => o?.[k], obj);
}
function setPath(obj, path, value) {
    const parts = path.split('.');
    let cur = obj;
    for (let i = 0; i < parts.length - 1; i++) {
        cur[parts[i]] = cur[parts[i]] || {};
        cur = cur[parts[i]];
    }
    cur[parts[parts.length-1]] = value;
}

function collect() {
    const data = JSON.parse(JSON.stringify(DATA));
    // Scalar fields
    document.querySelectorAll('[data-path]').forEach(el => {
        const path = el.dataset.path;
        let val = el.value;
        if (el.dataset.type === 'array') {
            val = val.split('\n').map(s => s.trim()).filter(Boolean);
        } else if (el.dataset.type === 'facts') {
            val = val.split('\n').map(line => {
                const [value, ...rest] = line.split('|');
                return { value: (value||'').trim(), label: rest.join('|').trim() };
            }).filter(f => f.value || f.label);
        }
        setPath(data, path, val);
    });
    // Advantages
    data.advantages.cards = [...document.querySelectorAll('[data-adv-card]')].map(card => ({
        icon: card.querySelector('[data-field="icon"]').value,
        title: card.querySelector('[data-field="title"]').value,
        description: card.querySelector('[data-field="description"]').value,
    }));
    // Prices
    data.prices.groups = [...document.querySelectorAll('[data-price-group]')].map(g => ({
        title: g.querySelector('[data-field="title"]').value,
        items: [...g.querySelectorAll('[data-price-item]')].map(it => ({
            name: it.querySelector('[data-field="name"]').value,
            price: it.querySelector('[data-field="price"]').value,
            note: it.querySelector('[data-field="note"]').value,
        })),
    }));
    // Phones
    data.contacts.phones = [...document.querySelectorAll('[data-phone]')].map(p => ({
        label: p.querySelector('[data-field="label"]').value,
        phone: p.querySelector('[data-field="phone"]').value,
        note: p.querySelector('[data-field="note"]').value,
    }));
    return data;
}

function save() {
    const data = collect();
    document.getElementById('contentField').value = JSON.stringify(data);
    document.getElementById('saveForm').submit();
}

async function uploadImage(input, path) {
    const file = input.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('action', 'upload');
    fd.append('image', file);
    const res = await fetch('admin.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.url) {
        const inp = document.querySelector(`[data-path="${path}"]`);
        if (inp) inp.value = data.url;
        alert('✅ Загружено: ' + data.url + '\nНе забудьте нажать «Сохранить»');
    } else {
        alert('❌ Ошибка: ' + (data.error || 'unknown'));
    }
}

<?php if (!empty($saved)): ?>
document.getElementById('status').textContent = '✅ Сохранено в ' + new Date().toLocaleTimeString('ru');
<?php endif; ?>
</script>
</body>
</html>
