<?php
// Partnership CTA Module — "Есть готовый софт?"
$title_line1 = __('Есть готовый софт?', 'softmir');
$title_line2 = __('Мы найдём для него клиентов.', 'softmir');
$subtitle = __('Разместите ваш продукт в нашем каталоге и получите доступ к тысячам потенциальных клиентов, которые уже ищут решения.', 'softmir');
$btn_text = __('Обсудить сотрудничество', 'softmir');
$btn_link = home_url('/contacts/');
?>
<section class="section partnership-section">
    <div class="container">
        <div class="partnership-block">
            <!-- Left: decorative -->
            <div class="partnership-visual">
                <div class="partnership-bg-shape"></div>
                <!-- Floating cards -->
                <div class="partnership-float-card card-1">
                    <div class="pfc-logo">💼</div>
                    <div class="pfc-info">
                        <div class="pfc-name"><?php esc_html_e('Ваш продукт', 'softmir'); ?></div>
                        <div class="pfc-stars">★★★★★ <span>5.0</span></div>
                    </div>
                </div>
                <div class="partnership-float-card card-2">
                    <div class="pfc-logo">📊</div>
                    <div class="pfc-info">
                        <div class="pfc-name"><?php esc_html_e('CRM система', 'softmir'); ?></div>
                        <div class="pfc-stars">★★★★☆ <span>4.5</span></div>
                    </div>
                </div>
                <div class="partnership-float-card card-3">
                    <div class="pfc-logo">🚀</div>
                    <div class="pfc-info">
                        <div class="pfc-name"><?php esc_html_e('SaaS платформа', 'softmir'); ?></div>
                        <div class="pfc-stars">★★★★★ <span>4.8</span></div>
                    </div>
                </div>
                <!-- Avatars -->
                <div class="partnership-avatars">
                    <span class="p-avatar">АК</span>
                    <span class="p-avatar">МП</span>
                    <span class="p-avatar">ДС</span>
                </div>
            </div>
            <!-- Right: text + button -->
            <div class="partnership-content">
                <h2 class="partnership-title">
                    <?php echo esc_html($title_line1); ?><br>
                    <span><?php echo esc_html($title_line2); ?></span>
                </h2>
                <p class="partnership-desc"><?php echo esc_html($subtitle); ?></p>
                <a href="<?php echo esc_url($btn_link); ?>" class="btn btn-primary partnership-btn">
                    <?php echo esc_html($btn_text); ?> →
                </a>
            </div>
        </div>
    </div>
</section>
