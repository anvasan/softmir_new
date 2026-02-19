<?php
/**
 * SoftMir Gmail SMTP Configuration
 * Configures WordPress PHPMailer to use Gmail SMTP
 */

// ========== Override From Address (runs before PHPMailer) ==========
function softmir_mail_from($from)
{
    $smtp_user = get_option('softmir_smtp_user', '');
    return !empty($smtp_user) ? $smtp_user : $from;
}
add_filter('wp_mail_from', 'softmir_mail_from');

function softmir_mail_from_name($name)
{
    $from_name = get_option('softmir_smtp_from_name', '');
    return !empty($from_name) ? $from_name : $name;
}
add_filter('wp_mail_from_name', 'softmir_mail_from_name');

// ========== Configure PHPMailer for Gmail SMTP ==========
function softmir_smtp_config($phpmailer)
{
    $smtp_user = get_option('softmir_smtp_user', '');
    $smtp_pass = get_option('softmir_smtp_pass', '');
    $from_name = get_option('softmir_smtp_from_name', get_bloginfo('name'));

    if (empty($smtp_user) || empty($smtp_pass))
        return;

    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.gmail.com';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 587;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Username = $smtp_user;
    $phpmailer->Password = $smtp_pass;
    $phpmailer->From = $smtp_user;
    $phpmailer->FromName = $from_name;
}
add_action('phpmailer_init', 'softmir_smtp_config');

// ========== Admin Settings Page ==========
function softmir_auth_settings_menu()
{
    add_options_page(
        'SoftMir Auth',
        'SoftMir Auth',
        'manage_options',
        'softmir-auth',
        'softmir_auth_settings_page'
    );
}
add_action('admin_menu', 'softmir_auth_settings_menu');

function softmir_auth_settings_init()
{
    // SMTP Section
    add_settings_section('softmir_smtp_section', 'Gmail SMTP', function () {
        echo '<p>Настройте Gmail SMTP для отправки Email (верификация, сброс пароля).</p>';
        echo '<p><small>Используйте <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a> (требуется 2FA).</small></p>';
    }, 'softmir-auth');

    // SMTP fields
    register_setting('softmir_auth_settings', 'softmir_smtp_user', ['sanitize_callback' => 'sanitize_email']);
    register_setting('softmir_auth_settings', 'softmir_smtp_pass', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('softmir_auth_settings', 'softmir_smtp_from_name', ['sanitize_callback' => 'sanitize_text_field']);

    add_settings_field('softmir_smtp_user', 'Gmail адрес', function () {
        $val = get_option('softmir_smtp_user', '');
        echo "<input type='email' name='softmir_smtp_user' value='" . esc_attr($val) . "' class='regular-text' placeholder='your@gmail.com'>";
    }, 'softmir-auth', 'softmir_smtp_section');

    add_settings_field('softmir_smtp_pass', 'App Password', function () {
        $val = get_option('softmir_smtp_pass', '');
        echo "<input type='password' name='softmir_smtp_pass' value='" . esc_attr($val) . "' class='regular-text' placeholder='xxxx xxxx xxxx xxxx'>";
    }, 'softmir-auth', 'softmir_smtp_section');

    add_settings_field('softmir_smtp_from_name', 'Имя отправителя', function () {
        $val = get_option('softmir_smtp_from_name', get_bloginfo('name'));
        echo "<input type='text' name='softmir_smtp_from_name' value='" . esc_attr($val) . "' class='regular-text'>";
    }, 'softmir-auth', 'softmir_smtp_section');

    // Google OAuth Section
    add_settings_section('softmir_google_section', 'Google OAuth', function () {
        echo '<p>Настройте Google OAuth для входа через Google.</p>';
        echo '<p><small>Создайте проект в <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a>.</small></p>';
        echo '<p><strong>Redirect URI:</strong> <code>' . esc_html(home_url('/?softmir_google_callback=1')) . '</code></p>';
    }, 'softmir-auth');

    register_setting('softmir_auth_settings', 'softmir_google_client_id', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('softmir_auth_settings', 'softmir_google_client_secret', ['sanitize_callback' => 'sanitize_text_field']);

    add_settings_field('softmir_google_client_id', 'Client ID', function () {
        $val = get_option('softmir_google_client_id', '');
        echo "<input type='text' name='softmir_google_client_id' value='" . esc_attr($val) . "' class='regular-text' placeholder='xxxxx.apps.googleusercontent.com'>";
    }, 'softmir-auth', 'softmir_google_section');

    add_settings_field('softmir_google_client_secret', 'Client Secret', function () {
        $val = get_option('softmir_google_client_secret', '');
        echo "<input type='password' name='softmir_google_client_secret' value='" . esc_attr($val) . "' class='regular-text'>";
    }, 'softmir-auth', 'softmir_google_section');
}
add_action('admin_init', 'softmir_auth_settings_init');

// ========== Settings Page Render ==========
function softmir_auth_settings_page()
{
    if (!current_user_can('manage_options'))
        return;

    // Handle test email
    if (isset($_POST['softmir_test_email']) && check_admin_referer('softmir_test_email')) {
        $to = get_option('softmir_smtp_user', get_option('admin_email'));
        $smtp_user = get_option('softmir_smtp_user', '');
        $smtp_pass = get_option('softmir_smtp_pass', '');

        // Pre-flight checks
        if (empty($smtp_user) || empty($smtp_pass)) {
            echo '<div class="notice notice-error"><p>❌ Сначала введите Gmail адрес и App Password выше и сохраните настройки.</p></div>';
        }
        elseif (!extension_loaded('openssl')) {
            echo '<div class="notice notice-error"><p>❌ PHP-расширение OpenSSL не включено. Оно необходимо для SMTP с TLS.</p></div>';
        }
        else {
            // Capture PHPMailer errors
            $mail_error = '';
            $error_handler = function ($wp_error) use (&$mail_error) {
                $mail_error = $wp_error->get_error_message();
                $data = $wp_error->get_error_data();
                if (!empty($data['phpmailer_exception_code'])) {
                    $mail_error .= ' (код: ' . $data['phpmailer_exception_code'] . ')';
                }
            };
            add_action('wp_mail_failed', $error_handler);

            $subject = '[SoftMir] Тестовое письмо SMTP';
            $message = '<html><body style="font-family:Inter,sans-serif;padding:40px;">
                <h2 style="color:#0ea5e9;">✅ SMTP работает!</h2>
                <p style="color:#475569;">Это тестовое письмо от SoftMir. Если вы его видите — настройки SMTP корректны.</p>
                <p style="color:#94a3b8;font-size:13px;">' . date('Y-m-d H:i:s') . '</p>
            </body></html>';
            $headers = ['Content-Type: text/html; charset=UTF-8'];
            $sent = wp_mail($to, $subject, $message, $headers);

            remove_action('wp_mail_failed', $error_handler);

            if ($sent) {
                echo '<div class="notice notice-success"><p>✅ Тестовое письмо отправлено на ' . esc_html($to) . '</p></div>';
            }
            else {
                echo '<div class="notice notice-error"><p>❌ Ошибка отправки.</p>';
                if ($mail_error) {
                    echo '<p><strong>Причина:</strong> ' . esc_html($mail_error) . '</p>';
                }
                echo '<p><strong>Проверьте:</strong></p><ul style="list-style:disc;margin-left:20px;">';
                echo '<li>Включена ли 2FA в Google аккаунте</li>';
                echo '<li>Правильно ли введён App Password (16 символов, без пробелов)</li>';
                echo '<li>Не заблокирован ли SMTP порт 587 (фаервол/антивирус)</li>';
                echo '</ul>';
                echo '<p><small>SMTP хост: smtp.gmail.com:587 | Пользователь: ' . esc_html($smtp_user) . ' | Пароль: ' . str_repeat('•', min(strlen($smtp_pass), 16)) . '</small></p>';
                echo '</div>';
            }
        }
    }

?>
    <div class="wrap">
        <h1>SoftMir Auth — Настройки</h1>
        <form method="post" action="options.php">
            <?php
    settings_fields('softmir_auth_settings');
    do_settings_sections('softmir-auth');
    submit_button('Сохранить настройки');
?>
        </form>

        <hr>
        <h2>Тестовое письмо</h2>
        <p>Отправить тестовое письмо на <strong><?php echo esc_html(get_option('softmir_smtp_user', get_option('admin_email'))); ?></strong></p>
        <form method="post">
            <?php wp_nonce_field('softmir_test_email'); ?>
            <button type="submit" name="softmir_test_email" class="button button-secondary">📧 Отправить тестовое письмо</button>
        </form>
    </div>
    <?php
}
