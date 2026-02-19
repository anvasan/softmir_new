<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <a href="<?php echo home_url('/'); ?>" class="site-logo">
                <?php if (has_custom_logo()):
    $logo_id = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($logo_id, 'full');
?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="site-logo-img">
                <?php
else: ?>
                    Soft<span>Mir</span>
                <?php
endif; ?>
            </a>

            <button class="mobile-toggle" onclick="this.closest('.site-header').querySelector('.main-nav').classList.toggle('open')" aria-label="Меню">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            </button>

            <nav class="main-nav">
                <?php
wp_nav_menu([
    'theme_location' => 'primary',
    'container' => false,
    'fallback_cb' => function () {
        echo '<ul>';
        echo '<li><a href="' . home_url('/') . '">Главная</a></li>';
        echo '<li><a href="' . get_post_type_archive_link('software') . '">Каталог</a></li>';
        echo '<li><a href="' . home_url('/blog/') . '">Блог</a></li>';
        echo '<li><a href="' . home_url('/about/') . '">О нас</a></li>';
        echo '<li><a href="' . home_url('/contacts/') . '">Контакты</a></li>';
        echo '</ul>';
    },
]);
?>
            </nav>

            <div class="header-auth">
                <?php if (is_user_logged_in()):
    $current_user = wp_get_current_user();
    $avatar_url = function_exists('softmir_get_user_avatar_url') ? softmir_get_user_avatar_url() : get_avatar_url($current_user->ID, ['size' => 40]);
?>
                    <div class="user-menu">
                        <button class="user-menu-toggle" id="user-menu-toggle" aria-label="Меню пользователя">
                            <img src="<?php echo esc_url($avatar_url); ?>" alt="" class="user-menu-avatar">
                            <span class="user-menu-name"><?php echo esc_html($current_user->display_name); ?></span>
                            <svg class="user-menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="user-dropdown" id="user-dropdown">
                            <div class="user-dropdown-header">
                                <img src="<?php echo esc_url($avatar_url); ?>" alt="" class="user-dropdown-avatar">
                                <div>
                                    <div class="user-dropdown-name"><?php echo esc_html($current_user->display_name); ?></div>
                                    <div class="user-dropdown-email"><?php echo esc_html($current_user->user_email); ?></div>
                                </div>
                            </div>
                            <div class="user-dropdown-links">
                                <a href="<?php echo home_url('/profile/'); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Личный кабинет
                                </a>
                                <?php if (current_user_can('manage_options')): ?>
                                <a href="<?php echo admin_url(); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                    Админ-панель
                                </a>
                                <?php
    endif; ?>
                                <hr>
                                <a href="<?php echo esc_url(softmir_logout_url()); ?>" class="user-dropdown-logout">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Выйти
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
else: ?>
                    <a href="<?php echo home_url('/login/'); ?>" class="header-login-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Войти
                    </a>
                <?php
endif; ?>
            </div>
        </div>
    </div>
</header>
