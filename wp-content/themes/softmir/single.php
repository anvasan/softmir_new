<?php get_header(); ?>

<div class="container">
    <div class="page-content">
        <?php while (have_posts()):
    the_post(); ?>

        <article>
            <div class="post-header">
                <h1><?php the_title(); ?></h1>
                <div class="post-meta">
                    <span>📅 <?php echo get_the_date(); ?></span>
                    <span>✍️ <?php the_author(); ?></span>
                    <?php
    $cats = get_the_category();
    if ($cats):
?>
                    <span>📁 <?php echo esc_html($cats[0]->name); ?></span>
                    <?php
    endif; ?>
                </div>
            </div>

            <?php if (has_post_thumbnail()): ?>
                <img src="<?php echo get_the_post_thumbnail_url(null, 'large'); ?>" alt="<?php the_title_attribute(); ?>" class="post-featured-image">
            <?php
    endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--gray-200);">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <?php
    $tags = get_the_tags();
    if ($tags):
        foreach ($tags as $tag):
?>
                    <a href="<?php echo get_tag_link($tag); ?>" style="background: var(--gray-100); color: var(--gray-600); padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.8rem;">
                        #<?php echo esc_html($tag->name); ?>
                    </a>
                    <?php
        endforeach;
    endif; ?>
                </div>
            </div>

            <!-- Navigation between posts -->
            <div style="margin-top: 2rem; display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <?php
    $prev = get_previous_post();
    $next = get_next_post();
?>
                <?php if ($prev): ?>
                    <a href="<?php echo get_permalink($prev); ?>" class="btn btn-outline" style="font-size: 0.85rem;">
                        ← <?php echo softmir_truncate($prev->post_title, 30); ?>
                    </a>
                <?php
    endif; ?>
                <?php if ($next): ?>
                    <a href="<?php echo get_permalink($next); ?>" class="btn btn-outline" style="font-size: 0.85rem; margin-left: auto;">
                        <?php echo softmir_truncate($next->post_title, 30); ?> →
                    </a>
                <?php
    endif; ?>
            </div>
        </article>

        <?php
endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
