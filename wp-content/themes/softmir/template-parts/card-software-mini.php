<?php
/**
 * Template part for displaying a mini software card (e.g. in Popular Products accordion)
 */

$post_id = get_the_ID();
$logo_url = get_field('company_logo', $post_id);
$short_desc = get_field('short_description', $post_id);

// Get Rating from Site Reviews
$rating = 0;
$review_count = 0;
if (function_exists('glsr_get_ratings')) {
    $ratings = glsr_get_ratings(['assigned_posts' => get_the_ID()]);
    if ($ratings) {
        $rating = $ratings->average;
        $review_count = $ratings->reviews;
    }
}

// Fallback description
if (empty($short_desc)) {
    $short_desc = get_the_excerpt();
}

// Truncate helper
if (!function_exists('softmir_truncate')) {
    // Fallback if not defined yet
    $display_desc = wp_trim_words($short_desc, 10);
}
else {
    $display_desc = softmir_truncate($short_desc, 200);
}
?>

<a href="<?php the_permalink(); ?>" class="software-mini-card">
    <div class="software-mini-logo">
        <?php if ($logo_url): ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php the_title_attribute(); ?>">
        <?php
else: ?>
            <div class="software-mini-placeholder">
                <?php echo mb_substr(get_the_title(), 0, 1); ?>
            </div>
        <?php
endif; ?>
    </div>
    
    <div class="software-mini-content">
        <h3 class="software-mini-title"><?php the_title(); ?></h3>
        
        <div class="software-mini-rating">
            <?php if (function_exists('softmir_stars')): ?>
                <?php echo softmir_stars($rating, 5); ?>
            <?php
else: ?>
                <span class="stars">
                    <?php for ($i = 1; $i <= 5; $i++)
        echo $i <= round($rating) ? '★' : '<span class="empty">★</span>'; ?>
                </span>
            <?php
endif; ?>
            
            <?php if ($review_count > 0): ?>
                <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
            <?php
else: ?>
                <span class="rating-value" style="font-size:0.75rem; color:var(--gray-400);">(0)</span>
            <?php
endif; ?>
        </div>
        
        <div class="software-mini-desc">
            <?php echo esc_html($display_desc); ?>
        </div>
    </div>
</a>
