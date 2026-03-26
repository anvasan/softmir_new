<?php
/**
 * Template Part: Software Card (Grid View)
 * 3-part layout matching list view: Top (logo+title+rating) | Middle (attrs+desc) | Bottom (footer attrs + CTA)
 */
$is_featured = get_field('is_featured');
$short_desc = get_field('short_description');
$logo = softmir_get_company_logo();
$price = get_field('price_summary');
$website = get_field('website_url');
$terms = get_the_terms(get_the_ID(), 'software_category');

// Primary Category logic
$primary_cat_id = get_post_meta(get_the_ID(), 'primary_category', true);
$category = '';
if ($primary_cat_id) {
    $term_to_display = get_term($primary_cat_id, 'software_category');
    if ($term_to_display && !is_wp_error($term_to_display)) {
        $category = $term_to_display->name;
    }
} elseif ($terms && !is_wp_error($terms)) {
    $category = $terms[0]->name;
}
$post_id = get_the_ID();

$rating = 0;
$review_count = 0;
if (function_exists('glsr_get_ratings')) {
    $ratings = glsr_get_ratings(['assigned_posts' => $post_id]);
    if ($ratings && isset($ratings->average)) {
        $rating = round($ratings->average, 1);
        $review_count = $ratings->reviews;
    }
}
?>
<div class="software-card <?php echo $is_featured ? 'featured' : ''; ?>">

    <!-- ===== TOP: Logo + Category + Title + Rating ===== -->
    <div class="card-header">
        <?php if ($is_featured): ?>
            <span class="card-badge">⭐ TOP</span>
            <?php
        endif; ?>
        <?php if ($logo): ?>
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title_attribute(); ?> логотип" class="card-logo"
                    loading="lazy">
            </a>
            <?php
        else: ?>
            <a href="<?php the_permalink(); ?>" class="card-logo card-logo-placeholder">
                <?php echo mb_substr(get_the_title(), 0, 2); ?>
            </a>
            <?php
        endif; ?>
        <div class="card-header-info">
            <?php if ($category): ?>
                <span class="card-category"><?php echo esc_html($category); ?></span>
                <?php
            endif; ?>
            <h3 class="card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <?php if ($rating > 0): ?>
                <div class="card-rating">
                    <?php echo softmir_stars($rating); ?>
                    <span class="card-rating-text"><?php echo $rating; ?> (<?php echo intval($review_count); ?>)</span>
                </div>
                <?php
            endif; ?>
        </div>
    </div>

    <!-- ===== MIDDLE: Attrs (middle) + Description ===== -->
    <div class="card-middle">
        <?php
        if (function_exists('softmir_render_key_functions')) {
            echo softmir_render_key_functions($post_id);
        }
        ?>
        <?php if ($short_desc || get_the_excerpt()): ?>
            <p class="card-description">
                <?php echo $short_desc ? esc_html(softmir_truncate($short_desc, 130)) : get_the_excerpt(); ?>
            </p>
            <?php
        endif; ?>
    </div>

    <!-- ===== BOTTOM: Footer attrs + CTA ===== -->
    <div class="card-bottom">
        <?php
        $footer_html = softmir_render_attrs_block($post_id, '_attr_card_position', 'footer', 'card-attrs-footer');
        if ($footer_html):
            echo $footer_html;
        endif;
        ?>
    </div>

    <div class="card-footer">
        <?php if ($price): ?>
            <span class="card-price"><?php echo esc_html($price); ?></span>
            <?php
        endif; ?>
        <?php if ($website): ?>
            <a href="<?php echo esc_url($website); ?>" class="btn btn-primary btn-sm card-cta-btn" target="_blank"
                rel="noopener">
                Посетить сайт
            </a>
            <?php
        else: ?>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm card-cta-btn">
                Перейти
            </a>
            <?php
        endif; ?>
    </div>
</div>