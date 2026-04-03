<!DOCTYPE html>
<html lang="<?= get_current_lang() ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- SEO -->
    <title><?= clean($title ?? config('site_name')) ?></title>
    <meta name="description" content="<?= clean($meta_description ?? config('description')) ?>">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link rel="canonical" href="<?= $canonical ?? base_url($_SERVER['REQUEST_URI']) ?>">

    <!-- OpenGraph / Social -->
    <meta property="og:type" content="<?= $og_type ?? 'website' ?>">
    <meta property="og:title" content="<?= clean($title ?? config('site_name')) ?>">
    <meta property="og:description" content="<?= clean($meta_description ?? config('description')) ?>">
    <meta property="og:url" content="<?= $canonical ?? base_url($_SERVER['REQUEST_URI']) ?>">
    <meta property="og:site_name" content="<?= clean(config('site_name')) ?>">
    <?php if (!empty($og_image)): ?>
    <meta property="og:image" content="<?= base_url($og_image) ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= clean($title ?? config('site_name')) ?>">
    <meta name="twitter:description" content="<?= clean($meta_description ?? config('description')) ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= config('favicon') ?? '/assets/img/favicon.ico' ?>">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0D9488">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Performance: preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://unpkg.com">

    <!-- Google Search Console -->
    <?php if (config('google_verification')): ?>
    <meta name="google-site-verification" content="<?= config('google_verification') ?>">
    <?php endif; ?>

    <!-- Styles -->
    <link rel="stylesheet" href="<?= base_url('/assets/css/style.css') ?>?v=<?= filemtime(__DIR__.'/../assets/css/style.css') ?>">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>

    <!-- Leaflet CSS (maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

    <!-- Structured Data -->
    <?= $schema ?? site_schema() ?>

    <!-- Dynamic Theme Overrides -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
    <?php if (config('theme_primary')): ?>
    <style>
        :root {
            --primary: <?= clean(config('theme_primary')) ?>;
        }
    </style>
    <?php endif; ?>
</head>
<body class="<?= auth_check() ? 'is-logged-in' : '' ?>">
    <!-- Dev Toolbar (Admin only) -->
    <?php if (auth_has_role('admin')): ?>
        <?php include __DIR__ . '/admin/_dev_toolbar.php'; ?>
    <?php endif; ?>
    <!-- Sister Sites Bar -->
    <?php $sisters = config('sister_sites'); if (!empty($sisters)): ?>
    <div class="sister-sites hidden-mobile">
        <span><?= __('sister_sites') ?>:</span>
        <?php foreach ($sisters as $site): ?>
        <a href="<?= $site['url'] ?>" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="<?= $site['icon'] ?? 'globe' ?>" style="width:16px;height:16px;"></i> <?= clean($site['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <a href="/" class="site-logo">
                <?php if (config('logo') && file_exists(__DIR__ . '/../' . ltrim(config('logo'), '/'))): ?>
                <img src="<?= config('logo') ?>" alt="<?= clean(config('site_name')) ?>">
                <?php endif; ?>
                <span><?= clean(config('site_name')) ?></span>
            </a>

            <!-- Desktop Navigation -->

            <!-- Desktop-only actions (hidden on mobile) -->
            <div class="nav-actions-desktop hidden-mobile">
                <?php if (count($langs ?? []) > 1): ?>
                <div class="lang-switcher">
                    <?php foreach ($langs as $code => $label): ?>
                    <a href="<?= lang_switch_url($code) ?>"
                       class="<?= get_current_lang() === $code ? 'active' : '' ?>"
                       title="<?= clean($label) ?>"><?= strtoupper($code) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <button class="theme-toggle btn-icon" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
                    <i data-lucide="sun" class="sun-icon" style="display:none;width:18px;height:18px;"></i>
                    <i data-lucide="moon" class="moon-icon" style="width:18px;height:18px;"></i>
                </button>

                <a href="/submit" class="btn btn-primary btn-sm"><?= __('nav_submit') ?></a>

                <?php if (auth_check()): ?>
                    <?php if (auth_has_role('admin', 'manager')): ?>
                    <a href="/admin" class="btn btn-ghost btn-sm"><?= __('nav_admin') ?></a>
                    <?php endif; ?>
                    <a href="/logout" class="btn btn-ghost btn-sm"><?= __('nav_logout') ?></a>
                <?php else: ?>
                    <a href="/login" class="btn btn-ghost btn-sm"><?= __('nav_login') ?></a>
                <?php endif; ?>
            </div>

            <!-- Hamburger (mobile only) -->
            <button class="menu-toggle" onclick="document.getElementById('nav-main').classList.toggle('open'); document.getElementById('nav-overlay').classList.toggle('open'); this.setAttribute('aria-expanded', document.getElementById('nav-main').classList.contains('open'))" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav-main">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </button>

            <!-- Desktop Navigation -->
            <nav class="nav-main" id="nav-main" aria-label="Main navigation">
                <a href="/directory" <?= ($path ?? '') === 'directory' ? 'class="active"' : '' ?>><?= __('nav_directory') ?></a>
                <a href="/tourism" <?= ($path ?? '') === 'tourism' ? 'class="active"' : '' ?>><?= __('nav_tourism') ?></a>
                <a href="/map" <?= ($path ?? '') === 'map' ? 'class="active"' : '' ?>><?= __('nav_map') ?></a>
                <a href="/community" <?= ($path ?? '') === 'community' ? 'class="active"' : '' ?>><?= __('nav_community') ?></a>
                <a href="/essential-services" <?= ($path ?? '') === 'essential-services' ? 'class="active"' : '' ?>><?= __('nav_essential') ?></a>
                <a href="/contact" <?= ($path ?? '') === 'contact' ? 'class="active"' : '' ?>><?= __('nav_contact') ?></a>

                <!-- Mobile-only: divider + actions inside drawer -->
                <div class="nav-divider"></div>
                <div class="nav-mobile-actions">
                    <div style="display:flex; gap:8px;">
                        <?php $langs = config('languages'); if (is_array($langs) && count($langs) > 1): ?>
                        <div class="lang-switcher">
                            <?php foreach ($langs as $code => $label): ?>
                            <a href="<?= lang_switch_url($code) ?>"
                               class="<?= get_current_lang() === $code ? 'active' : '' ?>"
                               title="<?= clean($label) ?>"><?= strtoupper($code) ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <button class="theme-toggle btn-icon" style="background:var(--bg-surface); border:1px solid var(--border-base); height:36px; width:36px; border-radius:50%;" title="Toggle Theme">
                            <i data-lucide="sun" class="sun-icon" style="display:none;width:18px;height:18px;margin:auto;"></i>
                            <i data-lucide="moon" class="moon-icon" style="width:18px;height:18px;margin:auto;"></i>
                        </button>
                    </div>
                    <a href="/submit" class="btn btn-primary"><?= __('nav_submit') ?></a>
                    <?php if (auth_check()): ?>
                        <?php if (auth_has_role('admin', 'manager')): ?>
                        <a href="/admin" class="btn btn-ghost"><?= __('nav_admin') ?></a>
                        <?php endif; ?>
                        <a href="/logout" class="btn btn-ghost"><?= __('nav_logout') ?></a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-ghost"><?= __('nav_login') ?></a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Drawer Overlay -->
    <div class="nav-overlay" id="nav-overlay" onclick="document.getElementById('nav-main').classList.remove('open'); this.classList.remove('open'); document.querySelector('.menu-toggle').setAttribute('aria-expanded','false');"></div>

    <!-- Flash Messages -->
    <?php $flashes = get_flashes(); if (!empty($flashes)): ?>
    <div class="container" style="padding-top: var(--space-4);">
        <?php foreach ($flashes as $f): ?>
        <div class="flash flash-<?= $f['type'] ?>"><?= clean($f['message']) ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main id="main-content">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3><?= clean(config('site_name')) ?></h3>
                    <p><?= clean(config('description')) ?></p>
                    <!-- Social Links -->
                    <?php $social = config('social'); if (!empty(array_filter($social ?? []))): ?>
                    <div class="flex gap-4">
                        <?php foreach ($social as $platform => $url): ?>
                            <?php if ($url): ?>
                            <a href="<?= clean($url) ?>" target="_blank" rel="noopener" title="<?= ucfirst($platform) ?>">
                                <?= ucfirst($platform) ?>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="footer-col">
                    <h4><?= __('nav_directory') ?></h4>
                    <a href="/directory"><?= __('all_categories') ?></a>
                    <a href="/tourism"><?= __('nav_tourism') ?></a>
                    <a href="/map"><?= __('nav_map') ?></a>
                    <a href="/submit"><?= __('nav_submit') ?></a>
                </div>
                <div class="footer-col">
                    <h4><?= __('nav_community') ?></h4>
                    <a href="/community/blog"><?= __('nav_blog') ?></a>
                    <a href="/community/vloggers"><?= __('nav_vloggers') ?></a>
                    <a href="/community/projects"><?= __('nav_projects') ?></a>
                </div>
                <div class="footer-col">
                    <h4><?= __('nav_about') ?></h4>
                    <a href="/about"><?= __('nav_about') ?></a>
                    <a href="/contact"><?= __('nav_contact') ?></a>
                    <a href="/essential-services"><?= __('nav_essential') ?></a>
                </div>
            </div>
            <div class="footer-bottom">
                <span><?= __('copyright', ['year' => date('Y'), 'site_name' => config('site_name')]) ?></span>
                <span><?= __('powered_by') ?></span>
            </div>
            <div class="footer-disclaimer">
                <?= clean(config('disclaimers')['general'] ?? '') ?>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <!-- Site JS -->
    <script src="<?= asset('assets/js/app.js') ?>"></script>
    <script>document.addEventListener('DOMContentLoaded',function(){if(window.lucide)lucide.createIcons();});</script>

    <!-- Google Analytics -->
    <?php if (config('google_analytics')): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= config('google_analytics') ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= config('google_analytics') ?>');
    </script>
    <?php endif; ?>
</body>
</html>
