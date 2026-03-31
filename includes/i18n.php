<?php
/**
 * CityDirectory — Internationalization (i18n)
 * File-based translation system with placeholder support.
 */

function i18n_load(?string $lang = null): array {
    static $strings = [];
    if (empty($strings)) {
        $lang = $lang ?? get_current_lang();
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/../lang/' . config('default_language') . '.php';
        }
        $strings = file_exists($file) ? require $file : [];
    }
    return $strings;
}

/**
 * Translate a string key, with placeholder replacement.
 * Usage: __('hero_title') or __('hero_title', ['city' => 'Tampakan'])
 */
function __(string $key, array $params = []): string {
    $strings = i18n_load();
    $str = $strings[$key] ?? $key;

    // Replace :placeholder with values
    foreach ($params as $k => $v) {
        $str = str_replace(':' . $k, htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'), $str);
    }
    return $str;
}

/**
 * Get current language from cookie/query/default.
 */
function get_current_lang(): string {
    $available = array_keys(config('languages') ?? ['en' => 'English']);

    // 1. Check query string (explicit language switch)
    if (!empty($_GET['lang']) && in_array($_GET['lang'], $available)) {
        $lang = $_GET['lang'];
        setcookie('lang', $lang, time() + (365 * 24 * 3600), '/', '', true, true);
        return $lang;
    }

    // 2. Check saved cookie
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], $available)) {
        return $_COOKIE['lang'];
    }

    // 3. Auto-detect from browser/phone/OS language (Accept-Language header)
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        // Map common language codes to our available languages
        $lang_map = [
            'fil' => 'tl',  // Filipino → Tagalog
            'tl'  => 'tl',
            'ceb' => 'ceb', // Cebuano/Bisaya
            'en'  => 'en',
        ];

        // Parse Accept-Language: e.g. "fil,en-US;q=0.9,en;q=0.8,ceb;q=0.7"
        $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        preg_match_all('/([a-z]{2,3})(?:-[A-Za-z]{2})?(?:;q=([0-9.]+))?/', $accept, $matches);

        if (!empty($matches[1])) {
            $langs = [];
            foreach ($matches[1] as $i => $code) {
                $q = !empty($matches[2][$i]) ? (float)$matches[2][$i] : 1.0;
                $langs[$code] = $q;
            }
            arsort($langs); // Sort by quality/preference

            foreach ($langs as $code => $q) {
                // Direct match
                if (in_array($code, $available)) return $code;
                // Mapped match (e.g. fil → tl)
                if (isset($lang_map[$code]) && in_array($lang_map[$code], $available)) return $lang_map[$code];
            }
        }
    }

    // 4. Fall back to config default
    return config('default_language') ?? 'en';
}

/**
 * Get the URL to switch language.
 */
function lang_switch_url(string $lang): string {
    $params = $_GET;
    $params['lang'] = $lang;
    return '?' . http_build_query($params);
}
