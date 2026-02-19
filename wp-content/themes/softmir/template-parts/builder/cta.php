<?php
// CTA Module — hardcoded content, no ACF dependency
$title = 'Нужна помощь в выборе ПО?';
$text = 'Оставьте email и наши эксперты помогут подобрать идеальное решение для вашего бизнеса.';
$btn_text = 'Отправить';
?>
<section class="section">
    <div class="container">
        <div class="cta-box">
            <h2><?php echo esc_html($title); ?></h2>
            <p style="color: var(--gray-600); margin-bottom: 1.5rem;"><?php echo esc_html($text); ?></p>
            <div class="email-form" style="display: flex; gap: 0.75rem; max-width: 480px; margin: 0 auto;">
                <input type="email" placeholder="Введите ваш email" style="flex: 1; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: 0.5rem;">
                <button class="btn btn-primary"><?php echo esc_html($btn_text); ?></button>
            </div>
        </div>
    </div>
</section>
