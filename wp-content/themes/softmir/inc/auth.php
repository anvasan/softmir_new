<?php
/**
 * SoftMir Custom Authentication System
 * Registration, Login, Logout, Password Reset, Email Verification, Custom Roles
 */

// ========== Custom Roles ==========
function softmir_register_custom_roles()
{
    // Add vendor role for integrators
    if (!get_role('vendor')) {
        add_role('vendor', 'Интегратор (Vendor)', [
            'read' => true,
            'edit_posts' => true,
            'upload_files' => true,
            'delete_posts' => true,
        ]);
    }
}
add_action('init', 'softmir_register_custom_roles');

// ========== Flash Messages ==========
function softmir_flash_set($type, $message)
{
    if (!session_id()) {
        session_start();
    }
    $_SESSION['softmir_flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message,
    ];
}

function softmir_flash_get()
{
    if (!session_id()) {
        session_start();
    }
    if (isset($_SESSION['softmir_flash'])) {
        $flash = $_SESSION['softmir_flash'];
        unset($_SESSION['softmir_flash']);
        return $flash;
    }
    return null;
}

function softmir_flash_html()
{
    $flash = softmir_flash_get();
    if (!$flash)
        return '';
    $type = esc_attr($flash['type']);
    $msg = esc_html($flash['message']);
    return "<div class=\"flash-message flash-{$type}\">{$msg}</div>";
}

// ========== Start Session ==========
function softmir_start_session()
{
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'softmir_start_session', 1);

// ========== Auto-create Auth Pages ==========
function softmir_create_auth_pages()
{
    $pages = [
        'login' => ['title' => 'Вход', 'template' => 'page-login.php'],
        'register' => ['title' => 'Регистрация', 'template' => 'page-register.php'],
        'profile' => ['title' => 'Личный кабинет', 'template' => 'page-profile.php'],
        'reset-password' => ['title' => 'Сброс пароля', 'template' => 'page-reset-password.php'],
    ];

    foreach ($pages as $slug => $data) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title' => $data['title'],
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
            ]);
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', $data['template']);
            }
        }
    }
}
add_action('after_switch_theme', 'softmir_create_auth_pages');

// Run once if pages don't exist
function softmir_maybe_create_auth_pages()
{
    if (get_option('softmir_auth_pages_created'))
        return;
    softmir_create_auth_pages();
    update_option('softmir_auth_pages_created', true);
}
add_action('init', 'softmir_maybe_create_auth_pages', 20);

// ========== Redirect logged-in users from login/register ==========
function softmir_redirect_authenticated()
{
    if (!is_user_logged_in())
        return;
    if (is_page('login') || is_page('register')) {
        wp_redirect(home_url('/profile/'));
        exit;
    }
}
add_action('template_redirect', 'softmir_redirect_authenticated');

// ========== Redirect guests from profile ==========
function softmir_redirect_guests()
{
    if (is_user_logged_in())
        return;
    if (is_page('profile')) {
        softmir_flash_set('warning', 'Пожалуйста, войдите в систему.');
        wp_redirect(home_url('/login/'));
        exit;
    }
}
add_action('template_redirect', 'softmir_redirect_guests');

// ========== Handle Registration ==========
function softmir_handle_register()
{
    if (!isset($_POST['softmir_register_nonce']))
        return;
    if (!wp_verify_nonce($_POST['softmir_register_nonce'], 'softmir_register')) {
        softmir_flash_set('error', 'Ошибка безопасности. Попробуйте снова.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    $username = sanitize_user(trim($_POST['username'] ?? ''));
    $email = sanitize_email(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirm'] ?? '';
    $role = sanitize_text_field($_POST['role'] ?? 'subscriber');

    // Validate
    if (empty($username) || empty($email) || empty($password)) {
        softmir_flash_set('error', 'Все поля обязательны для заполнения.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    if (strlen($username) < 3) {
        softmir_flash_set('error', 'Имя пользователя должно содержать минимум 3 символа.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    if (!is_email($email)) {
        softmir_flash_set('error', 'Введите корректный email адрес.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    if (strlen($password) < 8) {
        softmir_flash_set('error', 'Пароль должен содержать минимум 8 символов.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    if ($password !== $password2) {
        softmir_flash_set('error', 'Пароли не совпадают.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    if (username_exists($username)) {
        softmir_flash_set('error', 'Это имя пользователя уже занято.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    if (email_exists($email)) {
        softmir_flash_set('error', 'Этот email уже зарегистрирован.');
        wp_redirect(home_url('/register/'));
        exit;
    }

    // Only allow safe roles
    $allowed_roles = ['subscriber', 'vendor'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'subscriber';
    }

    // Create user
    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        softmir_flash_set('error', $user_id->get_error_message());
        wp_redirect(home_url('/register/'));
        exit;
    }

    // Set role
    $user = new WP_User($user_id);
    $user->set_role($role);

    // Set display name
    wp_update_user([
        'ID' => $user_id,
        'display_name' => $username,
    ]);

    // Generate email verification token
    $token = wp_generate_password(32, false);
    update_user_meta($user_id, 'softmir_email_token', $token);
    update_user_meta($user_id, 'softmir_email_verified', false);
    update_user_meta($user_id, 'softmir_email_token_time', time());

    // Send verification email
    softmir_send_verification_email($user_id, $email, $token);

    softmir_flash_set('success', 'Регистрация успешна! Проверьте вашу почту для подтверждения email.');
    wp_redirect(home_url('/login/'));
    exit;
}
add_action('init', 'softmir_handle_register');

// ========== Handle Login ==========
function softmir_handle_login()
{
    if (!isset($_POST['softmir_login_nonce']))
        return;
    if (!wp_verify_nonce($_POST['softmir_login_nonce'], 'softmir_login')) {
        softmir_flash_set('error', 'Ошибка безопасности. Попробуйте снова.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    $login = sanitize_text_field(trim($_POST['login'] ?? ''));
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    if (empty($login) || empty($password)) {
        softmir_flash_set('error', 'Заполните все поля.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    // Determine if login is email or username
    if (is_email($login)) {
        $user = get_user_by('email', $login);
    }
    else {
        $user = get_user_by('login', $login);
    }

    if (!$user || !wp_check_password($password, $user->data->user_pass, $user->ID)) {
        softmir_flash_set('error', 'Неверный логин или пароль.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    // Check email verification
    $verified = get_user_meta($user->ID, 'softmir_email_verified', true);
    if (!$verified || $verified === 'false' || $verified === false) {
        // Check if user registered via Google (auto-verified)
        $google_id = get_user_meta($user->ID, 'softmir_google_id', true);
        if (!$google_id) {
            softmir_flash_set('warning', 'Пожалуйста, подтвердите ваш email. <a href="' . esc_url(home_url('/login/?resend_verification=' . $user->ID)) . '">Отправить повторно</a>');
            wp_redirect(home_url('/login/'));
            exit;
        }
    }

    // Log in
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember);
    do_action('wp_login', $user->user_login, $user);

    softmir_flash_set('success', 'Добро пожаловать, ' . esc_html($user->display_name) . '!');
    wp_redirect(home_url('/profile/'));
    exit;
}
add_action('init', 'softmir_handle_login');

// ========== Handle Logout ==========
function softmir_handle_logout()
{
    if (!isset($_GET['softmir_logout']))
        return;
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'softmir_logout')) {
        wp_redirect(home_url('/'));
        exit;
    }
    wp_logout();
    softmir_flash_set('success', 'Вы успешно вышли из системы.');
    wp_redirect(home_url('/'));
    exit;
}
add_action('init', 'softmir_handle_logout');

// ========== Email Verification ==========
function softmir_send_verification_email($user_id, $email, $token)
{
    $verify_url = add_query_arg([
        'softmir_verify_email' => $token,
        'user_id' => $user_id,
    ], home_url('/'));

    $site_name = get_bloginfo('name');
    $subject = "[$site_name] Подтвердите ваш email";
    $message = softmir_get_email_template('verify', [
        'site_name' => $site_name,
        'verify_url' => $verify_url,
        'user_name' => get_userdata($user_id)->display_name,
    ]);

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($email, $subject, $message, $headers);
}

function softmir_handle_email_verification()
{
    if (!isset($_GET['softmir_verify_email']))
        return;

    $token = sanitize_text_field($_GET['softmir_verify_email']);
    $user_id = intval($_GET['user_id'] ?? 0);

    if (!$user_id || !$token) {
        softmir_flash_set('error', 'Неверная ссылка подтверждения.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    $stored_token = get_user_meta($user_id, 'softmir_email_token', true);
    $token_time = get_user_meta($user_id, 'softmir_email_token_time', true);

    if ($token !== $stored_token) {
        softmir_flash_set('error', 'Неверный или устаревший токен подтверждения.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    // Check token expiry (24 hours)
    if (time() - intval($token_time) > 86400) {
        softmir_flash_set('error', 'Ссылка подтверждения истекла. Запросите новую.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    // Mark as verified
    update_user_meta($user_id, 'softmir_email_verified', true);
    delete_user_meta($user_id, 'softmir_email_token');
    delete_user_meta($user_id, 'softmir_email_token_time');

    softmir_flash_set('success', 'Email подтверждён! Теперь вы можете войти.');
    wp_redirect(home_url('/login/'));
    exit;
}
add_action('init', 'softmir_handle_email_verification');

// ========== Resend Verification ==========
function softmir_handle_resend_verification()
{
    if (!isset($_GET['resend_verification']))
        return;

    $user_id = intval($_GET['resend_verification']);
    $user = get_userdata($user_id);

    if (!$user) {
        softmir_flash_set('error', 'Пользователь не найден.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    $verified = get_user_meta($user_id, 'softmir_email_verified', true);
    if ($verified && $verified !== 'false') {
        softmir_flash_set('info', 'Email уже подтверждён.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    // Rate limit: 1 resend per 2 minutes
    $last_sent = get_user_meta($user_id, 'softmir_email_token_time', true);
    if ($last_sent && (time() - intval($last_sent)) < 120) {
        softmir_flash_set('warning', 'Подождите 2 минуты перед повторной отправкой.');
        wp_redirect(home_url('/login/'));
        exit;
    }

    $token = wp_generate_password(32, false);
    update_user_meta($user_id, 'softmir_email_token', $token);
    update_user_meta($user_id, 'softmir_email_token_time', time());

    softmir_send_verification_email($user_id, $user->user_email, $token);

    softmir_flash_set('success', 'Письмо с подтверждением отправлено повторно.');
    wp_redirect(home_url('/login/'));
    exit;
}
add_action('init', 'softmir_handle_resend_verification');

// ========== Handle Password Reset Request ==========
function softmir_handle_reset_request()
{
    if (!isset($_POST['softmir_reset_request_nonce']))
        return;
    if (!wp_verify_nonce($_POST['softmir_reset_request_nonce'], 'softmir_reset_request')) {
        softmir_flash_set('error', 'Ошибка безопасности.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    $email = sanitize_email(trim($_POST['email'] ?? ''));
    if (empty($email) || !is_email($email)) {
        softmir_flash_set('error', 'Введите корректный email.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    $user = get_user_by('email', $email);

    // Always show success to prevent email enumeration
    if (!$user) {
        softmir_flash_set('success', 'Если аккаунт с таким email существует, вы получите письмо со ссылкой для сброса пароля.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    // Generate reset token
    $token = wp_generate_password(32, false);
    update_user_meta($user->ID, 'softmir_reset_token', $token);
    update_user_meta($user->ID, 'softmir_reset_token_time', time());

    // Send reset email
    $reset_url = add_query_arg([
        'softmir_reset' => $token,
        'user_id' => $user->ID,
    ], home_url('/reset-password/'));

    $site_name = get_bloginfo('name');
    $subject = "[$site_name] Сброс пароля";
    $message = softmir_get_email_template('reset', [
        'site_name' => $site_name,
        'reset_url' => $reset_url,
        'user_name' => $user->display_name,
    ]);

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($email, $subject, $message, $headers);

    softmir_flash_set('success', 'Если аккаунт с таким email существует, вы получите письмо со ссылкой для сброса пароля.');
    wp_redirect(home_url('/reset-password/'));
    exit;
}
add_action('init', 'softmir_handle_reset_request');

// ========== Handle Password Reset ==========
function softmir_handle_reset_password()
{
    if (!isset($_POST['softmir_reset_password_nonce']))
        return;
    if (!wp_verify_nonce($_POST['softmir_reset_password_nonce'], 'softmir_reset_password')) {
        softmir_flash_set('error', 'Ошибка безопасности.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    $user_id = intval($_POST['user_id'] ?? 0);
    $token = sanitize_text_field($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirm'] ?? '';

    if (!$user_id || !$token) {
        softmir_flash_set('error', 'Неверные данные.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    $stored_token = get_user_meta($user_id, 'softmir_reset_token', true);
    $token_time = get_user_meta($user_id, 'softmir_reset_token_time', true);

    if ($token !== $stored_token) {
        softmir_flash_set('error', 'Неверный или устаревший токен.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    // 1 hour expiry
    if (time() - intval($token_time) > 3600) {
        softmir_flash_set('error', 'Ссылка для сброса пароля истекла. Запросите новую.');
        wp_redirect(home_url('/reset-password/'));
        exit;
    }

    if (strlen($password) < 8) {
        softmir_flash_set('error', 'Пароль должен содержать минимум 8 символов.');
        wp_redirect(home_url('/reset-password/?softmir_reset=' . $token . '&user_id=' . $user_id));
        exit;
    }

    if ($password !== $password2) {
        softmir_flash_set('error', 'Пароли не совпадают.');
        wp_redirect(home_url('/reset-password/?softmir_reset=' . $token . '&user_id=' . $user_id));
        exit;
    }

    // Update password
    wp_set_password($password, $user_id);
    delete_user_meta($user_id, 'softmir_reset_token');
    delete_user_meta($user_id, 'softmir_reset_token_time');

    softmir_flash_set('success', 'Пароль успешно изменён! Войдите с новым паролем.');
    wp_redirect(home_url('/login/'));
    exit;
}
add_action('init', 'softmir_handle_reset_password');

// ========== Handle Profile Update ==========
function softmir_handle_profile_update()
{
    if (!isset($_POST['softmir_profile_nonce']))
        return;
    if (!wp_verify_nonce($_POST['softmir_profile_nonce'], 'softmir_profile_update')) {
        softmir_flash_set('error', 'Ошибка безопасности.');
        wp_redirect(home_url('/profile/'));
        exit;
    }

    if (!is_user_logged_in()) {
        wp_redirect(home_url('/login/'));
        exit;
    }

    $user_id = get_current_user_id();
    $display_name = sanitize_text_field(trim($_POST['display_name'] ?? ''));
    $new_password = $_POST['new_password'] ?? '';
    $new_password2 = $_POST['new_password_confirm'] ?? '';

    $update_data = ['ID' => $user_id];

    if (!empty($display_name)) {
        $update_data['display_name'] = $display_name;
    }

    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            softmir_flash_set('error', 'Новый пароль должен содержать минимум 8 символов.');
            wp_redirect(home_url('/profile/'));
            exit;
        }
        if ($new_password !== $new_password2) {
            softmir_flash_set('error', 'Пароли не совпадают.');
            wp_redirect(home_url('/profile/'));
            exit;
        }
        $update_data['user_pass'] = $new_password;
    }

    $result = wp_update_user($update_data);
    if (is_wp_error($result)) {
        softmir_flash_set('error', $result->get_error_message());
    }
    else {
        softmir_flash_set('success', 'Профиль обновлён.');
    }

    wp_redirect(home_url('/profile/'));
    exit;
}
add_action('init', 'softmir_handle_profile_update');

// ========== Email Templates ==========
function softmir_get_email_template($type, $data)
{
    $site_name = $data['site_name'] ?? get_bloginfo('name');

    $styles = '
        body { font-family: "Inter", -apple-system, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .email-wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); overflow: hidden; }
        .email-header { background: linear-gradient(135deg, #0ea5e9, #8b5cf6); padding: 32px 40px; text-align: center; }
        .email-header h1 { color: #fff; font-size: 22px; margin: 0; font-weight: 700; }
        .email-body { padding: 40px; }
        .email-body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .email-btn { display: inline-block; background: #0ea5e9; color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 16px 0; }
        .email-footer { padding: 24px 40px; background: #f8fafc; text-align: center; border-top: 1px solid #e2e8f0; }
        .email-footer p { color: #94a3b8; font-size: 13px; margin: 0; }
    ';

    if ($type === 'verify') {
        return "
        <html><head><style>{$styles}</style></head><body>
        <div class='email-wrap'>
            <div class='email-header'><h1>{$site_name}</h1></div>
            <div class='email-body'>
                <p>Здравствуйте, <strong>{$data['user_name']}</strong>!</p>
                <p>Спасибо за регистрацию на {$site_name}. Для завершения регистрации подтвердите ваш email:</p>
                <p style='text-align:center'><a href='{$data['verify_url']}' class='email-btn'>Подтвердить email</a></p>
                <p>Или скопируйте ссылку: <br><small style='color:#94a3b8;word-break:break-all'>{$data['verify_url']}</small></p>
                <p>Ссылка действительна 24 часа.</p>
            </div>
            <div class='email-footer'><p>&copy; " . date('Y') . " {$site_name}</p></div>
        </div>
        </body></html>";
    }

    if ($type === 'reset') {
        return "
        <html><head><style>{$styles}</style></head><body>
        <div class='email-wrap'>
            <div class='email-header'><h1>{$site_name}</h1></div>
            <div class='email-body'>
                <p>Здравствуйте, <strong>{$data['user_name']}</strong>!</p>
                <p>Мы получили запрос на сброс пароля для вашего аккаунта. Нажмите кнопку ниже:</p>
                <p style='text-align:center'><a href='{$data['reset_url']}' class='email-btn'>Сбросить пароль</a></p>
                <p>Или скопируйте ссылку: <br><small style='color:#94a3b8;word-break:break-all'>{$data['reset_url']}</small></p>
                <p>Ссылка действительна 1 час. Если вы не запрашивали сброс пароля — проигнорируйте это письмо.</p>
            </div>
            <div class='email-footer'><p>&copy; " . date('Y') . " {$site_name}</p></div>
        </div>
        </body></html>";
    }

    return '';
}

// ========== Logout URL Helper ==========
function softmir_logout_url()
{
    return wp_nonce_url(
        add_query_arg('softmir_logout', '1', home_url('/')),
        'softmir_logout'
    );
}

// ========== User Avatar Helper ==========
function softmir_get_user_avatar_url($user_id = 0)
{
    if (!$user_id)
        $user_id = get_current_user_id();
    $google_avatar = get_user_meta($user_id, 'softmir_google_avatar', true);
    if ($google_avatar)
        return $google_avatar;
    return get_avatar_url($user_id, ['size' => 80]);
}

// ========== Get User Role Label ==========
function softmir_get_role_label($user = null)
{
    if (!$user)
        $user = wp_get_current_user();
    if (!$user || !$user->exists())
        return '';

    $roles = [
        'administrator' => 'Администратор',
        'editor' => 'Редактор',
        'author' => 'Автор',
        'contributor' => 'Участник',
        'subscriber' => 'Подписчик',
        'vendor' => 'Интегратор',
    ];

    $user_role = $user->roles[0] ?? 'subscriber';
    return $roles[$user_role] ?? ucfirst($user_role);
}
