<?php
/**
 * Template Part: Software Card (Horizontal / List View)
 * 3-part layout: Top (logo+title+rating+CTA) | Middle (attrs+desc) | Bottom (price+footer attrs)
 */
$is_featured = get_field('is_featured');
$short_desc = get_field('short_description');
$logo = get_field('company_logo');
$price = get_field('price_summary');
$website = get_field('website_url');
$terms = get_the_terms(get_the_ID(), 'software_category');
$category = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
$post_id = get_the_ID();

$rating = 0;
$review_count = 0;
if (function_exists('glsr_get_ratings')) {
    $ratings = glsr_get_ratings(['assigned_posts' => $post_id]);
    if ($ratings) {
        $rating = $ratings->average;
        $review_count = $ratings->reviews;
    }
}
?>
<div class="list-card <?php echo $is_featured ? 'featured' : ''; ?>">

    <!-- ===== TOP: Logo + Category + Title + Rating + CTA ===== -->
    <div class="list-card-top">
        <a href="<?php the_permalink(); ?>" class="list-card-logo-link">
            <?php if ($logo): ?>
                <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title_attribute(); ?>" class="list-card-logo">
            <?php
else: ?>
                <div class="list-card-logo list-card-logo-placeholder">
                    <?php echo mb_substr(get_the_title(), 0, 2); ?>
                </div>
            <?php
endif; ?>
        </a>
        <div class="list-card-top-info">
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
                    <span class="card-rating-text"><?php echo round($rating, 1); ?> (<?php echo intval($review_count); ?>)</span>
                </div>
            <?php
endif; ?>
        </div>
        <div class="list-card-cta">
            <?php if ($website): ?>
                <a href="<?php echo esc_url($website); ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                    Посетить сайт ⊙ ≫
                </a>
            <?php
else: ?>
                <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
                    Подробнее ≫
                </a>
            <?php
endif; ?>
        </div>
    </div>

    <!-- ===== MIDDLE: Attrs (middle) + Description ===== -->
    <div class="list-card-middle">
        <?php
$middle_html = softmir_render_attrs_block($post_id, '_attr_card_position', 'middle', 'list-card-attrs-middle', 6);
if ($middle_html):
    echo $middle_html;
endif;
?>
        <?php
$desc_text = '';
if ($short_desc) {
    $desc_text = $short_desc;
}
elseif (has_excerpt()) {
    $desc_text = get_the_excerpt();
}
else {
    $desc_text = wp_trim_words(get_the_content(), 30, '...');
}
if ($desc_text):
?>
            <p class="list-card-desc"><?php echo esc_html(softmir_truncate($desc_text, 250)); ?></p>
        <?php
endif; ?>
    </div>

    <!-- ===== BOTTOM: Price + Footer attrs ===== -->
    <div class="list-card-bottom">
        <?php
$footer_html = softmir_render_attrs_block($post_id, '_attr_card_position', 'footer', 'list-card-attrs-footer');
if ($footer_html):
    echo $footer_html;
endif;
?>
        <?php if ($price): ?>
            <span class="list-card-price"><?php echo esc_html($price); ?></span>
        <?php
endif; ?>
    </div>
</div>
