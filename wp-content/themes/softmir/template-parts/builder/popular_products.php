<?php
// Popular Products Module — reads selected categories from ACF, fallback to top by count
$title = '🔥 Популярные подборки';
$page_id = get_option('page_on_front');

// 1. Try ACF field (admin picks categories)
$selected = get_field('popular_categories', $page_id);

// 2. Fallback: top 4 categories by post count
if (empty($selected) || !is_array($selected)) {
    $top_cats = get_terms([
        'taxonomy' => 'software_category',
        'hide_empty' => true,
        'number' => 4,
        'orderby' => 'count',
        'order' => 'DESC',
    ]);
    $selected = [];
    if ($top_cats && !is_wp_error($top_cats)) {
        foreach ($top_cats as $tc) {
            $selected[] = $tc->term_id;
        }
    }
}

$icons = ['🔹', '🔸', '🔹', '🔸', '🔹', '🔸'];

if ($selected):
?>
<section class="section">
    <div class="container">
        <h2 class="section-title" style="margin-bottom: 2rem;"><?php echo esc_html($title); ?></h2>
        
        <div class="popular-tabs-layout">
            <!-- Menu -->
            <div class="popular-tabs-menu">
                <?php foreach ($selected as $index => $cat_id):
        $term = get_term($cat_id, 'software_category');
        if (!$term || is_wp_error($term))
            continue;
        $active = $index === 0 ? 'active' : '';
?>
                    <div class="popular-tab-trigger <?php echo $active; ?>" data-target="tab-<?php echo esc_attr($index); ?>">
                        <span class="tab-icon"><?php echo esc_html($icons[$index % 6]); ?></span>
                        <span class="tab-info">
                            <span class="tab-name"><?php echo esc_html($term->name); ?></span>
                            <span class="tab-count"><?php echo $term->count; ?> решений</span>
                        </span>
                    </div>
                <?php
    endforeach; ?>
            </div>

            <!-- Content -->
            <div class="popular-tabs-content-wrapper">
                <?php foreach ($selected as $index => $cat_id):
        $term = get_term($cat_id, 'software_category');
        if (!$term || is_wp_error($term))
            continue;
        $active = $index === 0 ? 'active' : '';

        $products = new WP_Query([
            'post_type' => 'software',
            'posts_per_page' => 6,
            'tax_query' => [['taxonomy' => 'software_category', 'field' => 'term_id', 'terms' => $cat_id]],
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
?>
                    <div id="tab-<?php echo esc_attr($index); ?>" class="popular-tab-content <?php echo $active; ?>">
                        <div class="tab-header-mobile">
                            <h3><?php echo esc_html($term->name); ?></h3>
                            <a href="<?php echo esc_url(add_query_arg('sw_cat', $term->term_id, get_post_type_archive_link('software'))); ?>">Смотреть все</a>
                        </div>
                        <?php if ($products->have_posts()): ?>
                            <div class="software-grid-horizontal">
                                <?php while ($products->have_posts()):
                $products->the_post();
                get_template_part('template-parts/card', 'software-mini');
            endwhile;
            wp_reset_postdata(); ?>
                            </div>
                            <div class="tab-footer-action">
                                <a href="<?php echo esc_url(add_query_arg('sw_cat', $term->term_id, get_post_type_archive_link('software'))); ?>" class="btn btn-light btn-block-mobile">
                                    Смотреть все предложения в категории «<?php echo esc_html($term->name); ?>» →
                                </a>
                            </div>
                        <?php
        else: ?>
                            <div class="empty-state" style="text-align: center; padding: 3rem;">
                                <div class="empty-icon" style="font-size: 3rem;">📭</div>
                                <p>В этой категории пока нет продуктов.</p>
                            </div>
                        <?php
        endif; ?>
                    </div>
                <?php
    endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php
endif; ?>
