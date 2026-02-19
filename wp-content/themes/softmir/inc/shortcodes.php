<?php
/**
 * Theme Shortcodes
 */

if (!defined('ABSPATH'))
    exit;

/** /**
 * 1. Categories Grid Shortcode (List Style)
 * Usage: [softmir_categories count="12" title="Категории программного обеспечения" view_all_text="Посмотреть все" view_all_link="/categories/"]
 */
function softmir_sc_categories($atts)
{
    $atts = shortcode_atts([
        'count' => 15,
        'title' => 'Категории программного обеспечения',
        'view_all_text' => 'Посмотреть все',
        'view_all_link' => get_post_type_archive_link('software'), // Default to archive
    ], $atts);

    // Get categories
    $terms = get_terms([
        'taxonomy' => 'software_category',
        'hide_empty' => false,
        'number' => intval($atts['count']),
        'parent' => 0, // Top level only
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }

    ob_start();
?>
    <section class="home-categories alignfull" style="padding: 3rem 0; background: #fff;">
        <div class="container">
            <div class="categories-header">
                <h2 class="section-title" style="margin: 0; font-size: 1.75rem;"><?php echo esc_html($atts['title']); ?></h2>
                <?php if ($atts['view_all_text']): ?>
                    <a href="<?php echo esc_url($atts['view_all_link']); ?>" class="btn-view-all">
                        <?php echo esc_html($atts['view_all_text']); ?> ≡
                    </a>
                <?php
    endif; ?>
            </div>

            <div class="categories-list-grid">
                <?php foreach ($terms as $term):
        // Use query arg to stay on main archive with filter
        $link = add_query_arg('sw_cat', $term->term_id, get_post_type_archive_link('software'));
        $count = $term->count;
?>
                    <a href="<?php echo esc_url($link); ?>" class="cat-list-card">
                        <span class="cat-arrow">▶</span>
                        <span class="cat-name-wrap">
                            <span class="cat-name"><?php echo esc_html($term->name); ?></span>
                            <span class="cat-count-simple">(<?php echo $count; ?>)</span>
                        </span>
                    </a>
                <?php
    endforeach; ?>
            </div>
        </div>
    </section>

    <style>
        .categories-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-view-all {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--brand, #0ea5e9);
            border: 1px solid var(--brand, #0ea5e9);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-view-all:hover {
            background: var(--brand, #0ea5e9);
            color: #fff;
        }

        .categories-list-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .categories-list-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .categories-list-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .cat-list-card {
            display: flex;
            align-items: center; /* Center arrow vertically */
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem;
            text-decoration: none;
            color: #334155;
            transition: all 0.2s;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .cat-list-card:hover {
            border-color: var(--brand, #0ea5e9);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            color: #0f172a;
        }

        .cat-arrow {
            color: var(--brand, #0ea5e9);
            font-size: 0.7rem;
            margin-right: 0.75rem;
            flex-shrink: 0; /* Prevent arrow from shrinking */
        }
        
        .cat-name-wrap {
            display: block;
        }

        .cat-name {
            font-weight: 600;
        }

        .cat-count-simple {
            color: #64748b;
            margin-left: 0.25rem;
            font-weight: 400;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('softmir_categories', 'softmir_sc_categories');


/**
 * 2. Popular Products Shortcode
 * Usage: 
 * - Tabs: [softmir_popular_products title="Популярное"]
 * - Grid: [softmir_popular_products category="crm" count="6"]
 */
function softmir_sc_popular_products($atts)
{
    $atts = shortcode_atts([
        'title' => '',
        'count' => 6,
        'category' => '', // If set, shows grid instead of tabs
    ], $atts);

    // MODE 1: Specific Category Grid (Mini Cards)
    if (!empty($atts['category'])) {
        $query_args = [
            'post_type' => 'software',
            'posts_per_page' => intval($atts['count']),
            'tax_query' => [
                [
                    'taxonomy' => 'software_category',
                    'field' => 'slug',
                    'terms' => $atts['category']
                ]
            ],
            // Order by Featured/Pinned first, then Rating/Date
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        $products = new WP_Query($query_args);

        if (!$products->have_posts()) {
            return ''; // Hide if empty
        }

        ob_start();
?>
        <div class="software-list-mini">
            <?php while ($products->have_posts()):
            $products->the_post(); ?>
                <?php get_template_part('template-parts/card-software-mini'); ?>
            <?php
        endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

    // MODE 2: Tabs (Default) - Only if no category specified
    // Get 4 most populated categories for tabs
    $categories = get_terms([
        'taxonomy' => 'software_category',
        'hide_empty' => true,
        'number' => 4,
        'orderby' => 'count',
        'order' => 'DESC',
    ]);

    if (empty($categories) || is_wp_error($categories)) {
        return '';
    }

    ob_start();
?>
    <section class="home-popular alignfull" style="padding: 4rem 0;">
        <div class="container">
            <?php if ($atts['title']): ?>
                <div class="section-header text-center" style="margin-bottom: 2rem;">
                    <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
                </div>
            <?php
    endif; ?>

            <div class="popular-tabs-wrapper">
                <!-- Tabs Navigation -->
                <div class="popular-tabs-nav">
                    <?php foreach ($categories as $index => $cat): ?>
                        <button class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                                data-tab="tab-<?php echo $cat->term_id; ?>">
                            <?php echo esc_html($cat->name); ?>
                        </button>
                    <?php
    endforeach; ?>
                </div>

                <!-- Tabs Content -->
                <div class="popular-tabs-content">
                    <?php foreach ($categories as $index => $cat):
        // Query products for this category
        $products = new WP_Query([
            'post_type' => 'software',
            'posts_per_page' => $atts['count'],
            'tax_query' => [
                [
                    'taxonomy' => 'software_category',
                    'field' => 'term_id',
                    'terms' => $cat->term_id
                ]
            ]
        ]);
?>
                        <div class="tab-pane <?php echo $index === 0 ? 'active fade-in' : ''; ?>"
                             id="tab-<?php echo $cat->term_id; ?>">
                            
                            <?php if ($products->have_posts()): ?>
                                <div class="software-grid">
                                    <?php while ($products->have_posts()):
                $products->the_post();
                // Simple card output (using part if possible, or inline)
                get_template_part('template-parts/card', 'software-horizontal');
            endwhile; ?>
                                </div>
                                <div class="tab-footer text-center" style="margin-top: 2rem;">
                                    <a href="<?php echo get_term_link($cat); ?>" class="btn btn-outline">
                                        Показать все в <?php echo esc_html($cat->name); ?>
                                    </a>
                                </div>
                            <?php
        else: ?>
                                <p class="text-center">В этой категории пока нет программ.</p>
                            <?php
        endif;
        wp_reset_postdata(); ?>
                        </div>
                    <?php
    endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        .popular-tabs-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            background: none;
            border: none;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.2s;
            background: #f5f5f7;
        }
        
        .tab-btn.active, .tab-btn:hover {
            background: var(--primary-color, #007bff);
            color: #fff;
            transform: translateY(-2px);
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        /* Fade animation */
        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Ensure horizontal cards look good in grid/list */
        .software-grid {
             display: flex;
             flex-direction: column;
             gap: 1.5rem;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active', 'fade-in'));

                // Add active to clicked
                tab.classList.add('active');
                
                // Show content
                const targetId = tab.getAttribute('data-tab');
                const targetContent = document.getElementById(targetId);
                if(targetContent) {
                    targetContent.classList.add('active', 'fade-in');
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('softmir_popular_products', 'softmir_sc_popular_products');


// Testimonials removed by user request
