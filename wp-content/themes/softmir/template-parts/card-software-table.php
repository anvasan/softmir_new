<?php
/**
 * Template Part: Software Card (Table Row)
 * Used in archive-software.php for table view mode
 */
$short_desc = get_field('short_description');
$logo = get_field('company_logo');
$is_featured = get_field('is_featured');
$terms = get_the_terms(get_the_ID(), 'software_category');
$category = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
$rating = 0;
$review_count = 0;

if (function_exists('glsr_get_ratings')) {
    $ratings = glsr_get_ratings(['assigned_posts' => get_the_ID()]);
    if ($ratings) {
        $rating = $ratings->average;
        $review_count = $ratings->reviews;
    }
}
?>
<tr class="table-card-row <?php echo $is_featured ? 'featured' : ''; ?>">
    <td class="table-cell-name">
        <div class="table-name-wrap">
            <?php if ($logo): ?>
                <a href="<?php the_permalink(); ?>">
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title_attribute(); ?>" class="table-logo">
                </a>
            <?php
else: ?>
                <a href="<?php the_permalink(); ?>" class="table-logo table-logo-placeholder">
                    <?php echo mb_substr(get_the_title(), 0, 2); ?>
                </a>
            <?php
endif; ?>
            <a href="<?php the_permalink(); ?>" class="table-title"><?php the_title(); ?></a>
        </div>
    </td>
    <td class="table-cell-cat">
        <?php if ($category): ?>
            <span class="card-category"><?php echo esc_html($category); ?></span>
        <?php
endif; ?>
    </td>
    <td class="table-cell-rating">
        <?php echo softmir_stars($rating, 5); ?>
        <span class="review-count"><?php echo number_format($rating, 1); ?> (<?php echo intval($review_count); ?>)</span>
    </td>
    <td class="table-cell-desc">
        <?php
$desc = $short_desc ? $short_desc : get_the_excerpt();
echo esc_html(softmir_truncate($desc, 100));
?>
    </td>
    <td class="table-cell-action">
        <a href="<?php the_permalink(); ?>" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Перейти</a>
    </td>
</tr>
