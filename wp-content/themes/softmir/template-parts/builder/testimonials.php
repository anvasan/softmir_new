<?php
// Testimonials Module — hardcoded content (ACF Free doesn't support Flexible Content)
$title = __('Отзывы клиентов', 'softmir');
$subtitle = __('Что говорят наши клиенты о сотрудничестве', 'softmir');

$items = [
    [
        'text' => __('Благодаря SoftMir мы нашли CRM-систему, которая идеально подошла под наши процессы. Сэкономили недели на поиск и тестирование.', 'softmir'),
        'name' => 'Алексей Петров',
        'role' => __('Директор по развитию, ТехноПро', 'softmir'),
        'initials' => 'АП',
    ],
    [
        'text' => __('Очень удобный каталог с честными обзорами. Выбрали систему управления проектами за один день вместо обычных двух недель.', 'softmir'),
        'name' => 'Мария Сидорова',
        'role' => __('Руководитель отдела IT, ФинГрупп', 'softmir'),
        'initials' => 'МС',
    ],
    [
        'text' => __('Консультация экспертов помогла определиться с ERP-системой. Рекомендую всем, кто ищет бизнес-софт.', 'softmir'),
        'name' => 'Дмитрий Козлов',
        'role' => __('CEO, ЛогистикМастер', 'softmir'),
        'initials' => 'ДК',
    ],
];

$bg_colors = ['var(--brand-100)', '#fce7f3', '#dbeafe'];
$text_colors = ['var(--brand)', '#be185d', '#1d4ed8'];
?>
<section class="section">
    <div class="container">
        <h2 class="section-title text-center"><?php echo esc_html($title); ?></h2>
        <p class="section-subtitle text-center"><?php echo esc_html($subtitle); ?></p>

        <div class="testimonials-grid">
            <?php foreach ($items as $index => $item):
    $bg = $bg_colors[$index % 3];
    $col = $text_colors[$index % 3];
?>
                <div class="testimonial-card">
                    <p class="testimonial-quote">"<?php echo esc_html($item['text']); ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar" style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>;">
                            <?php echo esc_html($item['initials']); ?>
                        </div>
                        <div>
                            <div class="testimonial-name"><?php echo esc_html($item['name']); ?></div>
                            <div class="testimonial-title"><?php echo esc_html($item['role']); ?></div>
                        </div>
                    </div>
                </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>
