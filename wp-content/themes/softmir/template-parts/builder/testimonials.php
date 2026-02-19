<?php
// Testimonials Module — hardcoded content (ACF Free doesn't support Flexible Content)
$title = 'Отзывы клиентов';
$subtitle = 'Что говорят наши клиенты о сотрудничестве';

$items = [
    [
        'text' => 'Благодаря SoftMir мы нашли CRM-систему, которая идеально подошла под наши процессы. Сэкономили недели на поиск и тестирование.',
        'name' => 'Алексей Петров',
        'role' => 'Директор по развитию, ТехноПро',
        'initials' => 'АП',
    ],
    [
        'text' => 'Очень удобный каталог с честными обзорами. Выбрали систему управления проектами за один день вместо обычных двух недель.',
        'name' => 'Мария Сидорова',
        'role' => 'Руководитель отдела IT, ФинГрупп',
        'initials' => 'МС',
    ],
    [
        'text' => 'Консультация экспертов помогла определиться с ERP-системой. Рекомендую всем, кто ищет бизнес-софт.',
        'name' => 'Дмитрий Козлов',
        'role' => 'CEO, ЛогистикМастер',
        'initials' => 'ДК',
    ],
];

$bg_colors = ['var(--brand-100)', '#fce7f3', '#dbeafe'];
$text_colors = ['var(--brand)', '#be185d', '#1d4ed8'];
?>
<section class="section">
    <div class="container">
        <h2 class="section-title" style="text-align: center;"><?php echo esc_html($title); ?></h2>
        <p class="section-subtitle" style="text-align: center;"><?php echo esc_html($subtitle); ?></p>

        <div class="testimonials-grid">
            <?php foreach ($items as $index => $item):
    $bg = $bg_colors[$index % 3];
    $col = $text_colors[$index % 3];
?>
                <div class="testimonial-card">
                    <p class="testimonial-quote">"<?php echo esc_html($item['text']); ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar" style="background: <?php echo $bg; ?>; display: flex; align-items: center; justify-content: center; font-weight: 700; color: <?php echo $col; ?>;">
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
