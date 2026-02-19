<?php get_header(); ?>

<div class="container">
    <div class="breadcrumbs">
        <a href="<?php echo home_url('/'); ?>">Главная</a>
        <span class="sep">›</span>
        <a href="<?php echo get_post_type_archive_link('software'); ?>">Каталог ПО</a>
        <span class="sep">›</span>
        <?php single_term_title(); ?>
    </div>
</div>

<div class="container">
    <div style="background: #fff; padding: 1.5rem 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem;">
        <h1 class="section-title" style="margin-bottom: 0.25rem;">
            <?php single_term_title(); ?>
        </h1>
        <?php if (term_description()): ?>
            <div class="section-subtitle" style="margin-bottom: 0;">
                <?php echo term_description(); ?>
            </div>
        <?php
else: ?>
            <p class="section-subtitle" style="margin-bottom: 0;">Программное обеспечение в категории «<?php single_term_title(); ?>»</p>
        <?php
endif; ?>
    </div>

    <div class="catalog-layout">

        <!-- Sidebar Filters (Simplified) -->
        <aside class="catalog-sidebar">
            <div class="filter-box">
                <h3>Категории</h3>
                <ul class="filter-list">
                    <?php
$current_term = get_queried_object();
$cats = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
    'parent' => 0,
]);

foreach ($cats as $cat):
    $is_current = ($current_term->term_id === $cat->term_id);
    $is_parent = ($current_term->parent === $cat->term_id);
?>
                    <li>
                        <a href="<?php echo get_term_link($cat); ?>" 
                           style="<?php echo($is_current || $is_parent) ? 'font-weight:bold; color:var(--brand);' : 'color:var(--gray-700);'; ?>">
                            <?php echo esc_html($cat->name); ?>
                            <span style="color: var(--gray-400); font-size: 0.75rem;">(<?php echo $cat->count; ?>)</span>
                        </a>
                        
                        <?php
    // Show children if this is current or parent
    if ($is_current || $is_parent):
        $children = get_terms(['taxonomy' => 'software_category', 'parent' => $cat->term_id, 'hide_empty' => false]);
        if ($children):
?>
                            <ul style="margin-top:0.5rem; padding-left:1rem; list-style:none;">
                                <?php foreach ($children as $child):
                $is_child_current = ($current_term->term_id === $child->term_id);
?>
                                <li style="margin-bottom:0.25rem;">
                                    <a href="<?php echo get_term_link($child); ?>"
                                       style="font-size:0.9rem; <?php echo $is_child_current ? 'font-weight:bold; color:var(--brand);' : 'color:var(--gray-600);'; ?>">
                                        — <?php echo esc_html($child->name); ?>
                                    </a>
                                </li>
                                <?php
            endforeach; ?>
                            </ul>
                        <?php
        endif;
    endif;
?>
                    </li>
                    <?php
endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <?php if (have_posts()): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <p style="color: var(--gray-600); font-size: 0.9rem;">
                        Найдено: <strong><?php echo $wp_query->found_posts; ?></strong> продуктов
                    </p>
                </div>

                <div class="software-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <?php
    while (have_posts()):
        the_post();
        get_template_part('template-parts/card', 'software');
    endwhile;
?>
                </div>

                <!-- Pagination -->
                <div style="margin-top: 2rem; text-align: center;">
                    <?php
    echo paginate_links([
        'prev_text' => '← Назад',
        'next_text' => 'Далее →',
    ]);
?>
                </div>

            <?php
else: ?>
                <div style="text-align: center; padding: 3rem; background: #fff; border-radius: var(--radius); box-shadow: var(--shadow);">
                    <p style="font-size: 1.125rem; color: var(--gray-600);">В этой категории пока нет программ.</p>
                </div>
            <?php
endif; ?>
        </main>

    </div>
</div>

<?php get_footer(); ?>
