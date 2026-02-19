<?php get_header(); ?>

<?php while (have_posts()):
    the_post();
    $logo = get_field('company_logo');
    $website = get_field('website_url');
    $video = get_field('video_url');
    $short_desc = get_field('short_description');
    $pricing = get_field('pricing');
    $features = get_field('key_features');
    $advantages = get_field('advantages');
    $areas = get_field('business_areas');
    $markets = get_field('target_markets');
    $is_featured = get_field('is_featured');
    $terms = get_the_terms(get_the_ID(), 'software_category');
    $category = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
?>

<div class="container">
    <div class="breadcrumbs">
        <a href="<?php echo home_url('/'); ?>">Главная</a>
        <span class="sep">›</span>
        <a href="<?php echo get_post_type_archive_link('software'); ?>">Каталог</a>
        <span class="sep">›</span>
        <?php the_title(); ?>
    </div>
</div>

<div class="container">
    <div class="detail-layout">

        <!-- Main Content -->
        <div class="detail-main">

            <h1 style="font-size: 2rem; font-weight: 800; color: var(--gray-900);"><?php the_title(); ?></h1>

            <div class="view-counter">
                <svg class="view-counter__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <span class="view-counter__count"><?php echo number_format_i18n(softmir_get_views()); ?></span>
                <span class="view-counter__label">просм.</span>
            </div>

            <?php if ($short_desc): ?>
                <p style="font-size: 1.05rem; color: var(--gray-600); line-height: 1.7;"><?php echo esc_html($short_desc); ?></p>
            <?php
    endif; ?>

            <!-- Full Description (collapsible) -->
            <?php if (get_the_content()): ?>
            <div class="description-wrapper">
                <div class="description-content page-content" id="softDescription" style="margin: 0; max-width: none; padding: 2rem; box-shadow: none; border: 1px solid var(--gray-200);">
                    <?php the_content(); ?>
                </div>
                <div class="description-fade" id="softDescFade"></div>
                <button class="description-toggle" id="softDescToggle" onclick="softmirToggleDesc()">
                    Показать больше...
                </button>
            </div>
            <script>
            function softmirToggleDesc() {
                var content = document.getElementById('softDescription');
                var fade = document.getElementById('softDescFade');
                var btn = document.getElementById('softDescToggle');
                if (content.classList.contains('expanded')) {
                    content.classList.remove('expanded');
                    fade.style.display = '';
                    btn.textContent = 'Показать больше...';
                    content.scrollIntoView({behavior: 'smooth', block: 'start'});
                } else {
                    content.classList.add('expanded');
                    fade.style.display = 'none';
                    btn.textContent = 'Показать меньше...';
                }
            }
            // Auto-hide toggle if content is short
            document.addEventListener('DOMContentLoaded', function() {
                var content = document.getElementById('softDescription');
                var fade = document.getElementById('softDescFade');
                var btn = document.getElementById('softDescToggle');
                if (content && content.scrollHeight <= 340) {
                    content.classList.add('expanded');
                    if (fade) fade.style.display = 'none';
                    if (btn) btn.style.display = 'none';
                }
            });
            </script>
            <?php
    endif; ?>

            <!-- Attributes (Middle) -->
            <?php echo softmir_render_attrs_block(get_the_ID(), '_attr_page_position', 'middle', 'detail-block'); ?>

            <!-- Categories Block (Moved from Sidebar) -->
            <?php if ($terms && !is_wp_error($terms)): ?>
            <div class="detail-block">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Категории</h3>
                <div class="categories-2col">
                    <?php foreach ($terms as $term): ?>
                        <a href="<?php echo get_term_link($term); ?>" class="category-item-link">
                            <?php echo esc_html($term->name); ?>
                        </a>
                    <?php
        endforeach; ?>
                </div>
            </div>
            <?php
    endif; ?>

            <!-- Feature List -->
            <?php if ($features): ?>
            <div class="accordion">
                <button class="accordion-header" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
                    <span>🔑 Ключевые особенности</span>
                    <span class="icon">+</span>
                </button>
                <div class="accordion-body">
                    <div class="wysiwyg-content">
                        <?php echo $features; ?>
                    </div>
                </div>
            </div>
            <?php
    endif; ?>

            <!-- Advantages -->
            <?php if ($advantages): ?>
            <div class="accordion">
                <button class="accordion-header" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
                    <span>💪 Преимущества</span>
                    <span class="icon">+</span>
                </button>
                <div class="accordion-body">
                    <div class="wysiwyg-content">
                        <?php echo $advantages; ?>
                    </div>
                </div>
            </div>
            <?php
    endif; ?>

            <!-- Pricing -->
            <?php if ($pricing): ?>
            <div class="accordion">
                <button class="accordion-header" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
                    <span>💰 Тарифы и цены</span>
                    <span class="icon">+</span>
                </button>
                <div class="accordion-body">
                    <div class="wysiwyg-content">
                        <?php echo $pricing; ?>
                    </div>
                </div>
            </div>
            <?php
    endif; ?>

            <!-- Business Areas -->
            <?php if ($areas): ?>
            <div class="accordion">
                <button class="accordion-header" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
                    <span>🏢 Сферы применения</span>
                    <span class="icon">+</span>
                </button>
                <div class="accordion-body">
                    <div class="wysiwyg-content">
                        <?php echo $areas; ?>
                    </div>
                </div>
            </div>
            <?php
    endif; ?>

            <!-- Video -->
            <?php
    if ($video):
        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|watch\?v=))([A-Za-z0-9_-]{11})/', $video, $matches);
        $video_id = $matches[1] ?? '';
        if ($video_id):
?>
                    <div>
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Видео-обзор</h2>
                        <div style="position: relative; padding-top: 56.25%; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-lg);">
                            <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                                frameborder="0" allowfullscreen
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            </iframe>
                        </div>
                    </div>
            <?php
        endif;
    endif;
?>

            <!-- Gallery -->
            <?php
    $screenshots = [];
    for ($i = 1; $i <= 4; $i++) {
        $img = get_field('screenshot_' . $i);
        if ($img) {
            $screenshots[] = $img;
        }
    }

    if (!empty($screenshots)):
?>
                <div style="margin-top: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Скриншоты</h2>
                    <div class="gallery-grid popup-gallery">
                        <?php foreach ($screenshots as $image_url): ?>
                            <a href="<?php echo esc_url($image_url); ?>" target="_blank">
                                <img src="<?php echo esc_url($image_url); ?>" alt="Скриншот интерфейса">
                            </a>
                        <?php
        endforeach; ?>
                    </div>
                </div>
            <?php
    endif; ?>

            <!-- Reviews Block -->
            <div id="reviews" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--gray-200);">
                <div class="reviews-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0;">Отзывы пользователей</h2>
                </div>

                <!-- Reviews List -->
                <div class="reviews-list">
                    <?php echo do_shortcode('[site_reviews pagination="ajax" hide="title" assigned_posts="post_id"]'); ?>
                </div>

                <!-- Hidden Review Form -->
                <div id="review-form-wrapper" class="hidden" style="margin-top: 2rem; background: var(--gray-50); padding: 2rem; border-radius: var(--radius);">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Оставить отзыв</h3>
                    <?php echo do_shortcode('[site_reviews_form hide="title" assigned_posts="post_id"]'); ?>
                </div>
            </div>

            <!-- Similar Products -->
            <?php
    $similar_args = [
        'post_type' => 'software',
        'posts_per_page' => 2,
        'post__not_in' => [get_the_ID()],
    ];
    if ($terms && !is_wp_error($terms)) {
        $similar_args['tax_query'] = [
            ['taxonomy' => 'software_category', 'field' => 'term_id', 'terms' => $terms[0]->term_id],
        ];
    }
    $similar = new WP_Query($similar_args);
    if ($similar->have_posts()):
?>
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--gray-200);">
                <h2 style="font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 1.5rem;">Похожие продукты</h2>
                <div class="software-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <?php
        while ($similar->have_posts()):
            $similar->the_post();
            get_template_part('template-parts/card', 'software');
        endwhile;
        wp_reset_postdata();
?>
                </div>
            </div>
            <?php
    endif; ?>

        </div>

        <!-- Sidebar -->
        <aside class="detail-sidebar">
            <div class="sidebar-box" style="text-align: center;">
                <?php if ($logo): ?>
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title_attribute(); ?> логотип" class="sidebar-logo">
                <?php
    endif; ?>

                <?php if ($website): ?>
                    <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                        Посетить сайт →
                    </a>
                <?php
    endif; ?>

                <!-- Category removed from here -->

                <?php if ($is_featured): ?>
                    <p style="margin-top: 0.75rem; padding: 0.5rem; background: var(--yellow-50); border-radius: 0.5rem; font-size: 0.8rem; color: #92400e;">
                        ⭐ Рекомендуемый продукт
                    </p>
                <?php
    endif; ?>
            </div>

            <!-- Reviews Summary (Sidebar) -->
            <div class="sidebar-box">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Рейтинг</h3>
                <?php echo do_shortcode('[site_reviews_summary hide="title,summary" assigned_posts="post_id"]'); ?>
                
                <button class="btn btn-primary btn-block" onclick="softmirOpenReviewForm()" style="margin-top: 1.5rem; width: 100%;">
                    Написать отзыв
                </button>
            </div>
            <script>
            function softmirOpenReviewForm() {
                var form = document.getElementById('review-form-wrapper');
                form.classList.remove('hidden');
                form.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
            </script>

            <!-- Attributes (Sidebar) -->
            <?php echo softmir_render_attrs_block(get_the_ID(), '_attr_page_position', 'sidebar', 'sidebar-box'); ?>

            <?php if ($markets && is_array($markets) && count($markets) > 0): ?>
            <div class="sidebar-box">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem;">🌍 Целевые рынки</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                    <?php foreach ($markets as $market): ?>
                        <span style="background: var(--brand-50); color: var(--brand-800); padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 500;">
                            <?php echo esc_html($market); ?>
                        </span>
                    <?php
        endforeach; ?>
                </div>
            </div>
            <?php
    endif; ?>

            <!-- CTA -->
            <div class="sidebar-box" style="background: var(--brand-50); border-color: var(--brand-200);">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;">💡 Нужна помощь?</h3>
                <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 1rem;">Наши эксперты помогут с выбором и внедрением.</p>
                <a href="<?php echo home_url('/contacts/'); ?>" class="btn btn-primary" style="width: 100%; font-size: 0.85rem;">Связаться</a>
            </div>
        </aside>

    </div>
</div>

<?php
endwhile; ?>

<?php get_footer(); ?>
