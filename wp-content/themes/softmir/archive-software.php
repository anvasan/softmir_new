<?php get_header(); ?>

<div class="container">
    <div
        style="background: #fff; padding: 1.5rem 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem;">
        <h1 class="section-title" style="margin-bottom: 0.25rem;">Программное обеспечение</h1>
        <p class="section-subtitle" style="margin-bottom: 0;">Найдите оптимальное решение для вашего бизнеса среди
            проверенных продуктов</p>
    </div>

    <?php get_template_part('template-parts/search', 'bar'); ?>

    <?php
    $current_cat = isset($_GET['sw_cat']) ? intval($_GET['sw_cat']) : 0;
    ?>

    <div class="catalog-layout">

        <!-- Sidebar Filters -->
        <aside class="catalog-sidebar">
            <div class="filter-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin-bottom: 0;">Фильтры</h3>
                    <a href="<?php echo get_post_type_archive_link('software'); ?>"
                        style="font-size: 0.8rem;">Сбросить</a>
                </div>
                <form method="get" action="<?php echo get_post_type_archive_link('software'); ?>">
                    <?php if (isset($_GET['sw_cat'])): ?>
                        <input type="hidden" name="sw_cat" value="<?php echo esc_attr($_GET['sw_cat']); ?>">
                        <?php
                    endif; ?>
                    <?php if (isset($_GET['s_search']) && $_GET['s_search'] !== ''): ?>
                        <input type="hidden" name="s_search" value="<?php echo esc_attr($_GET['s_search']); ?>">
                        <?php
                    endif; ?>

                    <?php
                    $filterable_attrs = softmir_get_filterable_attributes();
                    if ($filterable_attrs):
                        foreach ($filterable_attrs as $attr):
                            $meta = softmir_get_attr_meta($attr->ID);
                            $field_name = 'sw_attr_' . $attr->ID;
                            $current_value = $_GET[$field_name] ?? '';

                            echo '<div style="margin-bottom: 1rem;">';
                            echo '<h4 style="font-size: 0.9rem; margin-bottom: 0.5rem; color: var(--gray-800);">' . esc_html($attr->post_title) . '</h4>';

                            if ($meta['type'] === 'checkbox') {
                                $options = softmir_parse_options($meta['options']);
                                if (!empty($options)) {
                                    foreach ($options as $opt) {
                                        $checked = is_array($current_value) && in_array($opt, $current_value);
                                        echo '<label style="display:block; font-size: 0.85rem; margin-bottom: 0.25rem;">';
                                        echo '<input type="checkbox" name="' . esc_attr($field_name) . '[]" value="' . esc_attr($opt) . '"' . ($checked ? ' checked' : '') . '> ' . esc_html($opt);
                                        echo '</label>';
                                    }
                                } else {
                                    $checked = $current_value === '1';
                                    echo '<label style="display:block; font-size: 0.85rem;">';
                                    echo '<input type="checkbox" name="' . esc_attr($field_name) . '" value="1"' . ($checked ? ' checked' : '') . '> Да';
                                    echo '</label>';
                                }
                            } elseif ($meta['type'] === 'select') {
                                $options = softmir_parse_options($meta['options']);
                                echo '<select name="' . esc_attr($field_name) . ($meta['multiple'] ? '[]' : '') . '" style="width:100%; font-size:0.85rem; padding:0.4rem;"' . ($meta['multiple'] ? ' multiple' : '') . '>';
                                echo '<option value="">Любой</option>';
                                foreach ($options as $opt) {
                                    $selected = is_array($current_value) ? in_array($opt, $current_value) : $current_value === $opt;
                                    echo '<option value="' . esc_attr($opt) . '"' . ($selected ? ' selected' : '') . '>' . esc_html($opt) . '</option>';
                                }
                                echo '</select>';
                            } else {
                                echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr(is_array($current_value) ? implode(",", $current_value) : $current_value) . '" style="width:100%; font-size:0.85rem; padding:0.4rem;">';
                            }
                            echo '</div>';
                        endforeach;
                    endif;
                    ?>

                    <button type="submit" class="btn btn-primary"
                        style="width: 100%; margin-top: 0.5rem;">Применить</button>
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
                // Get child terms too
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

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <p style="color: var(--gray-600); font-size: 0.9rem;">
                    Найдено: <strong><?php echo $software->found_posts; ?></strong> продуктов
                </p>
                <div class="view-switch">
                    <button class="view-switch-btn active" data-view="grid" title="Сетка">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                        </svg>
                    </button>
                    <button class="view-switch-btn" data-view="list" title="Список">
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
                    <button class="view-switch-btn" data-view="table" title="Таблица">
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

            <?php if ($software->have_posts()): ?>
                <div id="catalog-cards-wrapper" class="catalog-cards-wrapper view-grid">

                    <!-- GRID VIEW -->
                    <div class="cards-grid software-grid" style="grid-template-columns: repeat(2, 1fr);">
                        <?php while ($software->have_posts()):
                            $software->the_post();
                            get_template_part('template-parts/card', 'software');
                        endwhile; ?>
                    </div>

                    <!-- LIST VIEW -->
                    <div class="cards-list" style="display:none;">
                        <?php $software->rewind_posts();
                        while ($software->have_posts()):
                            $software->the_post();
                            get_template_part('template-parts/card-software', 'horizontal');
                        endwhile; ?>
                    </div>

                    <!-- TABLE VIEW -->
                    <div style="overflow-x: auto; width: 100%; display: none;" class="cards-table">
                        <table class="software-table" style="width: 100%; min-width: 800px;">
                            <thead>
                                <tr>
                                    <th>ПО</th>
                                    <th>Категория</th>
                                    <th>Рейтинг</th>
                                    <th>Описание</th>
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

                </div>

                <!-- Pagination -->
                <div style="margin-top: 2rem; text-align: center;">
                    <?php
                    echo paginate_links([
                        'total' => $software->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '← Назад',
                        'next_text' => 'Далее →',
                    ]);
                    ?>
                </div>
                <?php
            else: ?>
                <div
                    style="text-align: center; padding: 3rem; background: #fff; border-radius: var(--radius); box-shadow: var(--shadow);">
                    <p style="font-size: 1.125rem; color: var(--gray-600);">Программы не найдены</p>
                    <p style="font-size: 0.875rem; color: var(--gray-400); margin-top: 0.5rem;">Попробуйте изменить
                        параметры фильтрации</p>
                </div>
                <?php
            endif;
            wp_reset_postdata(); ?>
        </main>

    </div>
</div>

<?php get_footer(); ?>