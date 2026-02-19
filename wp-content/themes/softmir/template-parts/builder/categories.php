<?php
// Categories Module — pure WP data, no ACF dependency
$title = '🔍 Категории программного обеспечения';
$subtitle = 'Выберите интересующую категорию';

$categories = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => true,
    'parent' => 0,
    'number' => 18,
    'orderby' => 'count',
    'order' => 'DESC',
]);
?>
<section class="section section-alt">
    <div class="container">
        <div class="categories-header">
            <div>
                <h2 class="section-title" style="margin-bottom: 0.5rem;"><?php echo esc_html($title); ?></h2>
                <p class="section-subtitle" style="margin-bottom: 0;"><?php echo esc_html($subtitle); ?></p>
            </div>
            <a href="<?php echo get_post_type_archive_link('software'); ?>" class="categories-more-link">Все категории →</a>
        </div>

        <?php if ($categories && !is_wp_error($categories)): ?>
            <div class="categories-cards-grid">
                <?php foreach ($categories as $cat):
        $cat_link = add_query_arg('sw_cat', $cat->term_id, get_post_type_archive_link('software'));
?>
                    <a href="<?php echo esc_url($cat_link); ?>" class="category-card">
                        <span class="category-card-icon">▷</span>
                        <span class="category-card-title"><?php echo esc_html($cat->name); ?></span>
                        <span class="category-card-count">(<?php echo $cat->count; ?>)</span>
                    </a>
                <?php
    endforeach; ?>
            </div>
        <?php
endif; ?>
    </div>
</section>
