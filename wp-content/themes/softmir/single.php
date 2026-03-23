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
                    <img src="<?php echo get_the_post_thumbnail_url(null, 'large'); ?>" alt="<?php the_title_attribute(); ?>"
                        class="post-featured-image" loading="lazy">
                    <?php
                endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <div class="tags-section">
                    <div class="tags-wrapper">
                        <?php
                        $tags = get_the_tags();
                        if ($tags):
                            foreach ($tags as $tag):
                                ?>
                                <a href="<?php echo get_tag_link($tag); ?>" class="tag-link">
                                    #<?php echo esc_html($tag->name); ?>
                                </a>
                                <?php
                            endforeach;
                        endif; ?>
                    </div>
                </div>

                <!-- Navigation between posts -->
                <div class="post-nav">
                    <?php
                    $prev = get_previous_post();
                    $next = get_next_post();
                    ?>
                    <?php if ($prev): ?>
                        <a href="<?php echo get_permalink($prev); ?>" class="btn btn-outline btn-sm">
                            ← <?php echo softmir_truncate($prev->post_title, 30); ?>
                        </a>
                        <?php
                    endif; ?>
                    <?php if ($next): ?>
                        <a href="<?php echo get_permalink($next); ?>" class="btn btn-outline btn-sm btn-sm--next">
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