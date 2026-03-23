<?php get_header(); ?>

<div class="container">
    <div class="catalog-header-box">
        <h1 class="section-title"><?php esc_html_e('Программное обеспечение', 'softmir'); ?></h1>
        <p class="section-subtitle">
            <?php esc_html_e('Найдите оптимальное решение для вашего бизнеса среди проверенных продуктов', 'softmir'); ?>
        </p>
    </div>

    <?php get_template_part('template-parts/search', 'bar'); ?>

    <?php
    $current_cat = 0;
    if (isset($_GET['sw_cat']) && !empty($_GET['sw_cat'])) {
        $current_cat = intval($_GET['sw_cat']);
    } elseif (is_tax('software_category')) {
        $current_cat = get_queried_object_id();
    }
    ?>

    <div class="catalog-layout">

        <!-- Sidebar Filters -->
        <button class="mobile-filter-toggle btn btn-outline" type="button"
            onclick="document.querySelector('.catalog-sidebar').classList.toggle('open');"
            style="margin-bottom: 1rem; width: 100%; display: none;">
            <?php esc_html_e('Показать / Скрыть фильтры', 'softmir'); ?>
        </button>
        <aside class="catalog-sidebar">
            <div class="filter-box">
                <div class="filter-header">
                    <h3><?php esc_html_e('Фильтры', 'softmir'); ?></h3>
                    <a
                        href="<?php echo get_post_type_archive_link('software'); ?>"><?php esc_html_e('Сбросить', 'softmir'); ?></a>
                </div>
                <form method="get" action="<?php echo get_post_type_archive_link('software'); ?>"
                    id="catalog-filter-form">
                    <?php
                    // Determine current category if we are searching OR if we are on a taxonomy archive
                    $catalog_cat_id = 0;
                    if (isset($_GET['sw_cat']) && !empty($_GET['sw_cat'])) {
                        $catalog_cat_id = intval($_GET['sw_cat']);
                    } elseif (is_tax('software_category')) {
                        $catalog_cat_id = get_queried_object_id();
                    }

                    if ($catalog_cat_id > 0): ?>
                        <input type="hidden" name="sw_cat" value="<?php echo esc_attr($catalog_cat_id); ?>">
                    <?php endif; ?>

                    <?php if (isset($_GET['s_search']) && $_GET['s_search'] !== ''): ?>
                        <input type="hidden" name="s_search" value="<?php echo esc_attr($_GET['s_search']); ?>">
                    <?php endif; ?>

                    <?php

                    $filterable_attrs = softmir_get_filterable_attributes();
                    if ($filterable_attrs):
                        foreach ($filterable_attrs as $attr):
                            $meta = softmir_get_attr_meta($attr->ID);
                            $field_name = 'sw_attr_' . $attr->ID;
                            $current_value = $_GET[$field_name] ?? '';

                            // Get translated title and options for current language
                            $display_title = $attr->post_title;
                            $display_options = $meta['options'];
                            if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
                                $cur_lang = pll_current_language();
                                $def_lang = pll_default_language();
                                if ($cur_lang && $cur_lang !== $def_lang) {
                                    $tr_id = pll_get_post($attr->ID, $cur_lang);
                                    if ($tr_id && $tr_id !== $attr->ID) {
                                        $tr_post = get_post($tr_id);
                                        if ($tr_post) {
                                            $display_title = $tr_post->post_title;
                                        }
                                        $tr_options = get_post_meta($tr_id, '_attr_options', true);
                                        if (!empty($tr_options)) {
                                            $display_options = $tr_options;
                                        }
                                    }
                                }
                            }

                            // Build parallel arrays: RU values (for filtering) ↔ translated labels (for display)
                            $ru_options = softmir_parse_options($meta['options']);
                            $tr_options_arr = softmir_parse_options($display_options);

                            echo '<div class="filter-group">';
                            echo '<h4 class="filter-group__title">' . esc_html($display_title) . '</h4>';

                            if ($meta['type'] === 'checkbox') {
                                if (!empty($ru_options)) {
                                    foreach ($ru_options as $i => $opt) {
                                        $label = isset($tr_options_arr[$i]) ? $tr_options_arr[$i] : $opt;
                                        $checked = is_array($current_value) && in_array($opt, $current_value);
                                        echo '<label class="filter-label">';
                                        echo '<input type="checkbox" name="' . esc_attr($field_name) . '[]" value="' . esc_attr($opt) . '"' . ($checked ? ' checked' : '') . '> ' . esc_html($label);
                                        echo '</label>';
                                    }
                                } else {
                                    $checked = $current_value === '1';
                                    echo '<label class="filter-label">';
                                    echo '<input type="checkbox" name="' . esc_attr($field_name) . '" value="1"' . ($checked ? ' checked' : '') . '> ' . esc_html__('Да', 'softmir');
                                    echo '</label>';
                                }
                            } elseif ($meta['type'] === 'select') {
                                echo '<select name="' . esc_attr($field_name) . ($meta['multiple'] ? '[]' : '') . '" class="filter-select"' . ($meta['multiple'] ? ' multiple' : '') . '>';
                                echo '<option value="">' . esc_html__('Любой', 'softmir') . '</option>';
                                foreach ($ru_options as $i => $opt) {
                                    $label = isset($tr_options_arr[$i]) ? $tr_options_arr[$i] : $opt;
                                    $selected = is_array($current_value) ? in_array($opt, $current_value) : $current_value === $opt;
                                    echo '<option value="' . esc_attr($opt) . '"' . ($selected ? ' selected' : '') . '>' . esc_html($label) . '</option>';
                                }
                                echo '</select>';
                            } else {
                                echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr(is_array($current_value) ? implode(",", $current_value) : $current_value) . '" class="filter-input">';
                            }
                            echo '</div>';
                        endforeach;
                    endif;
                    ?>

                    <button type="submit"
                        class="btn btn-primary filter-submit"><?php esc_html_e('Применить', 'softmir'); ?></button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <?php
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $args = [
                'post_type' => 'software',
                'posts_per_page' => 12,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC',
            ];

            // Category filter — include children
            if ($current_cat > 0) {
                $term_ids = [$current_cat];
                $children = get_term_children($current_cat, 'software_category');
                if (!is_wp_error($children)) {
                    $term_ids = array_merge($term_ids, $children);
                }
                $args['tax_query'] = [
                    ['taxonomy' => 'software_category', 'field' => 'term_id', 'terms' => $term_ids],
                ];
            }

            // Search filter
            $search = $_GET['s_search'] ?? '';
            if (!empty($search)) {
                $args['s'] = sanitize_text_field($search);
            }

            // Attribute filters
            if (function_exists('softmir_build_filter_meta_query')) {
                $attr_meta_query = softmir_build_filter_meta_query();
                if (!empty($attr_meta_query)) {
                    $args['meta_query'] = $attr_meta_query;
                }
            }

            $software = new WP_Query($args);
            ?>

            <div class="catalog-results-bar">
                <p class="catalog-results-count">
                    <?php esc_html_e('Найдено:', 'softmir'); ?> <strong
                        id="catalog-count"><?php echo $software->found_posts; ?></strong>
                    <?php esc_html_e('продуктов', 'softmir'); ?>
                </p>
                <div class="view-switch">
                    <button class="view-switch-btn active" data-view="list"
                        title="<?php esc_attr_e('Список', 'softmir'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                    </button>
                    <button class="view-switch-btn" data-view="grid" title="<?php esc_attr_e('Сетка', 'softmir'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                        </svg>
                    </button>
                    <button class="view-switch-btn" data-view="table"
                        title="<?php esc_attr_e('Таблица', 'softmir'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <line x1="3" y1="9" x2="21" y2="9" />
                            <line x1="3" y1="15" x2="21" y2="15" />
                            <line x1="9" y1="3" x2="9" y2="21" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="catalog-results">
                <?php if ($software->have_posts()): ?>
                    <div id="catalog-cards-wrapper" class="catalog-cards-wrapper view-list">

                        <!-- GRID VIEW -->
                        <div class="cards-grid software-grid catalog-grid-2col" style="display:none;">
                            <?php while ($software->have_posts()):
                                $software->the_post();
                                get_template_part('template-parts/card', 'software');
                            endwhile; ?>
                        </div>

                        <!-- LIST VIEW -->
                        <div class="cards-list">
                            <?php $software->rewind_posts();
                            while ($software->have_posts()):
                                $software->the_post();
                                get_template_part('template-parts/card-software', 'horizontal');
                            endwhile; ?>
                        </div>

                        <!-- TABLE VIEW -->
                        <table class="cards-table software-table" style="display:none;">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('ПО', 'softmir'); ?></th>
                                    <th><?php esc_html_e('Категория', 'softmir'); ?></th>
                                    <th><?php esc_html_e('Рейтинг', 'softmir'); ?></th>
                                    <th><?php esc_html_e('Описание', 'softmir'); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $software->rewind_posts();
                                while ($software->have_posts()):
                                    $software->the_post();
                                    get_template_part('template-parts/card-software', 'table');
                                endwhile; ?>
                            </tbody>
                        </table>

                    </div>

                    <!-- Pagination -->
                    <div class="catalog-pagination">
                        <?php
                        echo paginate_links([
                            'total' => $software->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '← ' . __('Назад', 'softmir'),
                            'next_text' => __('Далее', 'softmir') . ' →',
                        ]);
                        ?>
                    </div>
                    <?php
                else: ?>
                    <div class="catalog-empty">
                        <p class="catalog-empty__title"><?php esc_html_e('Программы не найдены', 'softmir'); ?></p>
                        <p class="catalog-empty__text">
                            <?php esc_html_e('Попробуйте изменить параметры фильтрации', 'softmir'); ?>
                        </p>
                    </div>
                    <?php
                endif;
                wp_reset_postdata(); ?>
            </div>
        </main>

    </div>
</div>

<?php get_footer(); ?>