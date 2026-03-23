---
name: Polylang PHP Dictionary Fallback
description: Implementing programmatic string translations in Polylang via a built-in PHP dictionary wrapper. 
---

# Polylang PHP Dictionary Fallback

When building or modifying WordPress themes that use Polylang for localization, users often face the burden of manually entering translation strings into the Polylang `Strings Translations` admin interface. 

You MUST use this approach when the user asks you to "automatically translate" interface strings or when implementing multilingual forms/UIs where we know the exact translation strings in advance.

## The Approach

Instead of relying solely on `pll__()` or `__()` and waiting for the user to translate the strings manually, we implement a **Dictionary Fallback** function.

### 1. Register Strings (Always required for Polylang to recognize them)
First, register the strings so they appear in Polylang's UI. This allows the user to override our built-in translations if they ever want to.

```php
add_action('init', function() {
    if (!function_exists('pll_register_string')) return;
    
    $strings = [
        'my_string_id' => 'Текст на русском (Original)',
    ];
    foreach ($strings as $name => $str) {
        pll_register_string($name, $str, 'My Custom Group', false);
    }
});
```

### 2. Create the Fallback Helper Function
Create a helper function that checks the current language, looks up the string in a hardcoded dictionary, but **優先順位 (prioritizes)** the user's manual translation if one exists in the Polylang database.

```php
function my_theme_t($name, $default) {
    // 1. Check if Polylang translated it manually by the user
    // If the string translated by pll__ is DIFFERENT from the original,
    // the user has translated it manually in the admin UI.
    if (function_exists('pll__')) {
        $pll_trans = pll__($default);
        if ($pll_trans !== $default && !empty($pll_trans)) {
            return $pll_trans;
        }
    }

    // 2. Built-in dictionary fallback
    if (function_exists('pll_current_language')) {
        $lang = pll_current_language('slug');
        $dict = [
            'my_string_id' => [
                'en' => 'English Text',
                'uk' => 'Український текст',
            ],
        ];

        if (isset($dict[$name][$lang])) {
            return $dict[$name][$lang];
        }
    }

    // 3. Fallback to default
    return $default;
}
```

### 3. Use the Helper in the Frontend
Replace standard `esc_html__('Text', 'domain')` calls with our new helper:

```php
// BEFORE
echo esc_html__('Текст на русском (Original)', 'my-theme');

// AFTER
echo esc_html(my_theme_t('my_string_id', 'Текст на русском (Original)'));
```

## Benefits of this Skill
- **Zero Configuration for User:** The interface is translated instantly.
- **Maintains Control:** The user can still go to Polylang "Translations" and override our dictionary manually.
- **Fail-safe:** If Polylang is disabled, it gracefully degrades to the default language string.
