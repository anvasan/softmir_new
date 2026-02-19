<?php
/**
 * Register Block Patterns
 */

function softmir_register_block_patterns()
{
    register_block_pattern_category(
        'softmir',
        array('label' => __('SoftMir', 'softmir'))
    );

    register_block_pattern(
        'softmir/advantages',
        array(
        'title' => __('Наши преимущества (3 колонки)', 'softmir'),
        'description' => _x('Секция с заголовком и 3 карточками преимуществ.', 'Block pattern description', 'softmir'),
        'categories' => array('softmir'),
        'content' => '<!-- wp:group {"align":"full","className":"section section-alt","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull section section-alt"><!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container"><!-- wp:heading {"textAlign":"center","className":"section-title"} -->
<h2 class="wp-block-heading has-text-align-center section-title">Почему выбирают нас</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"section-subtitle"} -->
<p class="has-text-align-center section-subtitle">SoftMir — ваш надёжный проводник в мире бизнес-ПО</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"advantages-grid"} -->
<div class="wp-block-columns advantages-grid"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"advantage-card"} -->
<div class="wp-block-group advantage-card"><!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size advantage-icon">🔍</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">Экспертный анализ</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Каждый продукт тщательно проверен нашими специалистами. Мы анализируем функции, цены и отзывы.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"advantage-card"} -->
<div class="wp-block-group advantage-card"><!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size advantage-icon">⚡</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">Быстрое внедрение</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Помогаем с настройкой и интеграцией. Более 100 успешных проектов за плечами.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"advantage-card"} -->
<div class="wp-block-group advantage-card"><!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size advantage-icon">🎯</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">Персональный подбор</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Учитываем специфику вашего бизнеса, бюджет и требования для идеального выбора.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
    )
    );
    register_block_pattern(
        'softmir/business-navigator',
        array(
        'title' => __('Навигатор по бизнесу (Сетка 4 колонки)', 'softmir'),
        'description' => _x('Секция для выбора сегмента бизнеса.', 'Block pattern description', 'softmir'),
        'categories' => array('softmir'),
        'content' => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"800"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-center has-xx-large-font-size" style="font-style:normal;font-weight:800">Навигатор по бизнесу</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.25rem"}}} -->
<p class="has-text-align-center" style="font-size:1.25rem">Выберите ваш сегмент для подбора идеального ПО</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"border":{"width":"1px","radius":"12px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"color":{"background":"#ffffff"}},"borderColor":"contrast"} -->
<div class="wp-block-column has-border-color has-contrast-border-color has-background" style="background-color:#ffffff;border-width:1px;border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"3rem"}}} -->
<p class="has-text-align-center" style="font-size:3rem">🏠</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontStyle":"normal","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:1.5rem;font-style:normal;font-weight:700">Micro / ФОП</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Решения для старта: просто, доступно и функционально.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link wp-element-button" style="border-radius:6px">Перейти</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"border":{"width":"1px","radius":"12px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"color":{"background":"#ffffff"}},"borderColor":"contrast"} -->
<div class="wp-block-column has-border-color has-contrast-border-color has-background" style="background-color:#ffffff;border-width:1px;border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"3rem"}}} -->
<p class="has-text-align-center" style="font-size:3rem">🏢</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontStyle":"normal","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:1.5rem;font-style:normal;font-weight:700">SME</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Оптимизация процессов и масштабирование бизнеса.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link wp-element-button" style="border-radius:6px">Перейти</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"border":{"width":"1px","radius":"12px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"color":{"background":"#ffffff"}},"borderColor":"contrast"} -->
<div class="wp-block-column has-border-color has-contrast-border-color has-background" style="background-color:#ffffff;border-width:1px;border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"3rem"}}} -->
<p class="has-text-align-center" style="font-size:3rem">🔧</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontStyle":"normal","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:1.5rem;font-style:normal;font-weight:700">Integrators</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Инструменты для внедрения и поддержки клиентов.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link wp-element-button" style="border-radius:6px">Перейти</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"border":{"width":"1px","radius":"12px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"color":{"background":"#ffffff"}},"borderColor":"contrast"} -->
<div class="wp-block-column has-border-color has-contrast-border-color has-background" style="background-color:#ffffff;border-width:1px;border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"3rem"}}} -->
<p class="has-text-align-center" style="font-size:3rem">🏭</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontStyle":"normal","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:1.5rem;font-style:normal;font-weight:700">Enterprise</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Мощные ERP и CRM для крупных корпораций.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link wp-element-button" style="border-radius:6px">Перейти</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
    )
    );
}
add_action('init', 'softmir_register_block_patterns');
