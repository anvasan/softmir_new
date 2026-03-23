<?php
// Advantages Module — hardcoded content (ACF Free doesn't support Flexible Content)
$title = __('Почему выбирают нас', 'softmir');
$subtitle = __('SoftMir — ваш надёжный проводник в мире бизнес-ПО', 'softmir');

$items = [
    [
        'icon' => '🔍',
        'title' => __('Экспертные обзоры', 'softmir'),
        'text' => __('Детальные обзоры каждого продукта от наших специалистов с реальным опытом использования.', 'softmir'),
    ],
    [
        'icon' => '⚖️',
        'title' => __('Сравнение решений', 'softmir'),
        'text' => __('Удобные таблицы сравнения функций, цен и условий для выбора лучшего варианта.', 'softmir'),
    ],
    [
        'icon' => '⭐',
        'title' => __('Реальные отзывы', 'softmir'),
        'text' => __('Честные отзывы от пользователей, которые уже внедрили эти решения в своём бизнесе.', 'softmir'),
    ],
    [
        'icon' => '💬',
        'title' => __('Бесплатные консультации', 'softmir'),
        'text' => __('Наши эксперты помогут подобрать идеальное решение под ваши задачи и бюджет.', 'softmir'),
    ],
];
?>
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title text-center"><?php echo esc_html($title); ?></h2>
        <p class="section-subtitle text-center"><?php echo esc_html($subtitle); ?></p>

        <div class="advantages-grid">
            <?php foreach ($items as $item): ?>
                <div class="advantage-card">
                    <div class="advantage-icon"><?php echo esc_html($item['icon']); ?></div>
                    <h3><?php echo esc_html($item['title']); ?></h3>
                    <p><?php echo esc_html($item['text']); ?></p>
                </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>
