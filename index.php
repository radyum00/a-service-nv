<?php
$content = json_decode(@file_get_contents(__DIR__ . '/data/content.json'), true);
if (!is_array($content)) { http_response_code(500); exit('content.json not found'); }
require __DIR__ . '/icons.php';

function e(?string $s): string { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function tel(?string $p): string { return 'tel:' . preg_replace('/[^\d+]/', '', $p ?? ''); }
function paragraphs(string $text): string {
    $out = '';
    foreach (array_filter(array_map('trim', explode("\n\n", $text))) as $p) {
        $out .= '<p>' . nl2br(e($p)) . '</p>';
    }
    return $out;
}

$site = $content['site'];
$hero = $content['hero'];
$adv  = $content['advantages'];
$pr   = $content['prices'];
$ab   = $content['about'];
$ct   = $content['contacts'];
$ft   = $content['footer'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($site['companyName']) ?> — <?= e($site['tagline']) ?></title>
    <meta name="description" content="<?= e($hero['subtitle']) ?>">
    <meta name="theme-color" content="#142e54">
    <meta property="og:title" content="<?= e($site['companyName']) ?> — <?= e($site['tagline']) ?>">
    <meta property="og:description" content="<?= e($hero['subtitle']) ?>">
    <meta property="og:type" content="website">
    <meta name="geo.region" content="RU-KHM">
    <meta name="geo.placename" content="<?= e($site['city']) ?>">
    <link rel="stylesheet" href="style.css">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": <?= json_encode($site['companyName'], JSON_UNESCAPED_UNICODE) ?>,
      "description": <?= json_encode($hero['subtitle'], JSON_UNESCAPED_UNICODE) ?>,
      "telephone": <?= json_encode($ct['phones'][0]['phone'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
      "address": {
        "@type": "PostalAddress",
        "addressLocality": <?= json_encode($site['city'], JSON_UNESCAPED_UNICODE) ?>,
        "addressRegion": <?= json_encode($site['region'], JSON_UNESCAPED_UNICODE) ?>,
        "addressCountry": "RU"
      },
      "openingHours": "Mo-Su 10:00-20:00",
      "sameAs": [<?= json_encode($ct['mapUrl'], JSON_UNESCAPED_UNICODE) ?>, <?= json_encode($ct['vkUrl'], JSON_UNESCAPED_UNICODE) ?>]
    }
    </script>
</head>
<body>

<!-- ============ HEADER ============ -->
<header class="site-header">
    <div class="container header-inner">
        <a href="#top" class="brand">
            <span class="brand-icon"><?= icon('washer', 22) ?></span>
            <span class="brand-text">
                <span class="brand-name"><?= e($site['companyName']) ?></span>
                <span class="brand-tag"><?= e($site['city']) ?> · <?= e($site['region']) ?></span>
            </span>
        </a>
        <nav class="nav">
            <a href="#benefits">Преимущества</a>
            <a href="#prices">Услуги и цены</a>
            <a href="#about">О компании</a>
            <a href="#contacts">Контакты</a>
        </nav>
        <a href="<?= tel($ct['phones'][0]['phone'] ?? '') ?>" class="btn btn-primary btn-sm header-phone">
            <?= icon('phone', 16) ?>
            <span><?= e($ct['phones'][0]['phone'] ?? '') ?></span>
        </a>
    </div>
</header>

<!-- ============ HERO ============ -->
<section id="top" class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <span class="badge badge-green"><?= e($hero['eyebrow']) ?></span>
            <h1 class="hero-title">
                <?= e($hero['title']) ?> <span class="accent"><?= e($hero['titleAccent']) ?></span>
            </h1>
            <p class="hero-sub"><?= e($hero['subtitle']) ?></p>
            <div class="hero-actions">
                <a href="<?= tel($hero['phone']) ?>" class="btn btn-primary btn-lg">
                    <?= icon('phone', 18) ?> <?= e($hero['ctaLabel']) ?>
                </a>
                <a href="#prices" class="btn btn-secondary btn-lg">
                    <?= e($hero['secondaryLabel']) ?> <?= icon('arrowRight', 16) ?>
                </a>
            </div>
            <ul class="hero-highlights">
                <?php foreach ($hero['highlights'] as $h): ?>
                    <li><?= icon('check', 14) ?> <?= e($h) ?></li>
                <?php endforeach; ?>
            </ul>
            <p class="hero-note"><?= icon('clock', 16) ?> <?= e($hero['note']) ?></p>
        </div>
        <div class="hero-media">
            <div class="hero-card">
                <?php if (!empty($hero['image'])): ?>
                    <img src="<?= e($hero['image']) ?>" alt="<?= e($hero['imageAlt']) ?>" loading="eager">
                <?php else: ?>
                    <div class="hero-placeholder"><?= icon('washer', 96) ?></div>
                <?php endif; ?>
                <div class="hero-caption">
                    <span class="hero-caption-icon"><?= icon('wrench', 20) ?></span>
                    <div>
                        <p class="hero-caption-title"><?= e($hero['imageTitle']) ?></p>
                        <p class="hero-caption-sub"><?= e($hero['imageText']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ADVANTAGES ============ -->
<section id="benefits" class="section section-sand">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow"><?= e($adv['eyebrow']) ?></span>
            <h2><?= e($adv['title']) ?></h2>
            <p><?= e($adv['description']) ?></p>
        </div>
        <div class="adv-grid">
            <?php foreach ($adv['cards'] as $i => $card): ?>
                <article class="adv-card <?= $i === 0 ? 'adv-card-featured' : '' ?>">
                    <span class="adv-icon"><?= icon($card['icon'], 23) ?></span>
                    <h3><?= e($card['title']) ?></h3>
                    <p><?= e($card['description']) ?></p>
                    <span class="adv-num"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ PRICES ============ -->
<section id="prices" class="section section-white">
    <div class="container">
        <div class="price-head">
            <div>
                <span class="eyebrow"><?= e($pr['eyebrow']) ?></span>
                <h2><?= e($pr['title']) ?></h2>
                <p><?= e($pr['description']) ?></p>
            </div>
            <div class="guarantee-pill">
                <?= icon('shield', 20) ?>
                <span><b>Гарантия до 2 месяцев</b><br><small>на работы и запчасти</small></span>
            </div>
        </div>

        <div class="price-groups">
            <?php foreach ($pr['groups'] as $i => $g): ?>
                <details class="price-group" <?= $i < 2 ? 'open' : '' ?>>
                    <summary>
                        <span class="price-num"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="price-group-title">
                            <b><?= e($g['title']) ?></b>
                            <small><?= count($g['items']) ?> услуг</small>
                        </span>
                        <span class="price-toggle"><?= icon('chevronDown', 18) ?></span>
                    </summary>
                    <div class="price-list">
                        <?php foreach ($g['items'] as $item): ?>
                            <div class="price-row">
                                <div>
                                    <p class="price-name"><?= e($item['name']) ?></p>
                                    <?php if (!empty($item['note'])): ?>
                                        <p class="price-note"><?= e($item['note']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <p class="price-value <?= mb_stripos($item['price'], 'бесплатно') !== false ? 'is-free' : '' ?>">
                                    <?= e($item['price']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>

        <p class="price-footnote"><?= icon('info', 16) ?> <?= e($pr['note']) ?></p>

        <div class="price-cta">
            <div>
                <span class="eyebrow eyebrow-light"><?= e($pr['ctaEyebrow']) ?></span>
                <h3><?= e($pr['ctaTitle']) ?></h3>
                <p><?= e($pr['ctaText']) ?></p>
            </div>
            <a href="<?= tel($pr['phone']) ?>" class="btn btn-primary btn-lg">
                <?= icon('phone', 18) ?> <?= e($pr['ctaLabel']) ?>
            </a>
        </div>
    </div>
</section>

<!-- ============ ABOUT ============ -->
<section id="about" class="section section-sand">
    <div class="container about-inner">
        <div class="about-media">
            <div class="about-card">
                <?php if (!empty($ab['image'])): ?>
                    <img src="<?= e($ab['image']) ?>" alt="<?= e($ab['imageAlt']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="about-placeholder"><?= icon('washer', 96) ?></div>
                <?php endif; ?>
                <div class="about-caption">
                    <span><?= icon('mapPin', 18) ?></span>
                    <div>
                        <b><?= e($ab['imageCaption']) ?></b>
                        <small><?= e($site['city']) ?>, <?= e($site['region']) ?></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="about-text">
            <span class="eyebrow"><?= e($ab['eyebrow']) ?></span>
            <h2><?= e($ab['title']) ?></h2>
            <div class="about-body"><?= paragraphs($ab['text']) ?></div>
            <ul class="about-bullets">
                <?php foreach ($ab['bullets'] as $b): ?>
                    <li><?= icon('check', 13) ?> <?= e($b) ?></li>
                <?php endforeach; ?>
            </ul>
            <dl class="about-facts">
                <?php foreach ($ab['facts'] as $f): ?>
                    <div>
                        <dd><?= e($f['value']) ?></dd>
                        <dt><?= e($f['label']) ?></dt>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </div>
</section>

<!-- ============ CONTACTS ============ -->
<section id="contacts" class="section section-dark">
    <div class="container contacts-inner">
        <div>
            <span class="eyebrow eyebrow-light"><?= e($ct['eyebrow']) ?></span>
            <h2><?= e($ct['title']) ?></h2>
            <p class="contacts-desc"><?= e($ct['description']) ?></p>

            <div class="contacts-phones">
                <?php foreach ($ct['phones'] as $p): ?>
                    <a href="<?= tel($p['phone']) ?>" class="phone-card">
                        <span class="phone-icon"><?= icon('phone', 20) ?></span>
                        <span class="phone-info">
                            <small><?= e($p['label']) ?></small>
                            <b><?= e($p['phone']) ?></b>
                            <?php if (!empty($p['note'])): ?><em><?= e($p['note']) ?></em><?php endif; ?>
                        </span>
                        <?= icon('arrowRight', 18) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="contacts-meta">
                <div>
                    <span><?= icon('clock', 18) ?></span>
                    <div><b><?= e($ct['workHours']) ?></b><small><?= e($ct['workHoursNote']) ?></small></div>
                </div>
                <div>
                    <span><?= icon('mapPin', 18) ?></span>
                    <div><b><?= e($ct['address']) ?></b><small>Выезд по городу</small></div>
                </div>
            </div>

            <div class="contacts-links">
                <a href="<?= e($ct['mapUrl']) ?>" target="_blank" rel="noopener" class="btn btn-light">
                    <?= icon('mapPin', 16) ?> <?= e($ct['mapLabel']) ?>
                </a>
                <a href="<?= e($ct['vkUrl']) ?>" target="_blank" rel="noopener" class="btn btn-light">
                    <?= icon('vk', 16) ?> <?= e($ct['vkLabel']) ?>
                </a>
            </div>
        </div>

        <div class="contacts-cta">
            <span class="eyebrow">Заявка на ремонт</span>
            <h3><?= e($ct['ctaTitle']) ?></h3>
            <p><?= e($ct['ctaText']) ?></p>
            <a href="<?= tel($ct['phones'][0]['phone'] ?? '') ?>" class="btn btn-primary btn-lg btn-full">
                <?= icon('phone', 18) ?> <?= e($ct['ctaLabel']) ?>
            </a>
            <ul class="cta-facts">
                <li><?= icon('shield', 16) ?> Гарантия на работы до 2 месяцев</li>
                <li><?= icon('check', 16) ?> Стоимость согласуем до начала ремонта</li>
                <li><?= icon('truck', 16) ?> Выезд мастера по Нижневартовску</li>
            </ul>
        </div>
    </div>
</section>

<!-- ============ FOOTER ============ -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <a href="#top" class="brand">
                <span class="brand-icon"><?= icon('washer', 20) ?></span>
                <span class="brand-text">
                    <span class="brand-name"><?= e($ft['companyName']) ?></span>
                    <span class="brand-tag">Сервисный центр</span>
                </span>
            </a>
            <p class="footer-tag"><?= e($ft['tagline']) ?></p>
            <a href="<?= tel($ft['phone']) ?>" class="footer-phone">
                <?= icon('phone', 16) ?> <?= e($ft['phone']) ?>
            </a>
        </div>
        <nav>
            <b>Разделы</b>
            <a href="#benefits">Преимущества</a>
            <a href="#prices">Услуги и цены</a>
            <a href="#about">О компании</a>
            <a href="#contacts">Контакты</a>
        </nav>
        <div>
            <b>Мы в сети</b>
            <a href="<?= e($ct['mapUrl']) ?>" target="_blank" rel="noopener"><?= icon('externalLink', 14) ?> 2ГИС</a>
            <a href="<?= e($ct['vkUrl']) ?>" target="_blank" rel="noopener"><?= icon('vk', 14) ?> ВКонтакте</a>
            <p class="footer-addr"><?= icon('mapPin', 14) ?> <?= e($site['city']) ?>, <?= e($site['region']) ?></p>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>© <?= date('Y') ?> <?= e($ft['companyName']) ?>. Все права защищены.</p>
        <p><?= e($ft['legalNote']) ?></p>
    </div>
</footer>

</body>
</html>
