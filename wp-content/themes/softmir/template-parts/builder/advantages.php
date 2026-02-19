<?php
// Advantages Module — hardcoded content (ACF Free doesn't support Flexible Content)
$title = 'Почему выбирают нас';
$subtitle = 'SoftMir — ваш надёжный проводник в мире бизнес-ПО';

$items = [
    [
        'icon' => '🔍',
        'title' => 'Экспертные обзоры',
        'text' => 'Детальные обзоры каждого продукта от наших специалистов с реальным опытом использования.',
    ],
    [
        'icon' => '⚖️',
        'title' => 'Сравнение решений',
        'text' => 'Удобные таблицы сравнения функций, цен и условий для выбора лучшего варианта.',
    ],
    [
        'icon' => '⭐',
        'title' => 'Реальные отзывы',
        'text' => 'Честные отзывы от пользователей, которые уже внедрили эти решения в своём бизнесе.',
    ],
    [
        'icon' => '💬',
        'title' => 'Бесплатные консультации',
        'text' => 'Наши эксперты помогут подобрать идеальное решение под ваши задачи и бюджет.',
    ],
];
?>
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title" style="text-align: center;"><?php echo esc_html($title); ?></h2>
        <p class="section-subtitle" style="text-align: center;"><?php echo esc_html($subtitle); ?></p>

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
