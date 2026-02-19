<?php
if (!defined('ABSPATH'))
    exit;

add_action('acf/init', 'softmir_register_home_flexible');

function softmir_register_home_flexible()
{
    if (!function_exists('acf_add_local_field_group'))
        return;

    // Define the Flexible Content layout
    acf_add_local_field_group([
        'key' => 'group_home_flexible',
        'title' => 'Конструктор Главной Страницы',
        'fields' => [
            [
                'key' => 'field_home_modules',
                'label' => 'Модули страницы',
                'name' => 'home_modules',
                'type' => 'flexible_content',
                'button_label' => 'Добавить модуль',
                'layouts' => [
                    // 1. HERO
                    [
                        'key' => 'layout_hero',
                        'name' => 'hero',
                        'label' => 'Hero (Первый экран)',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_hero_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text', 'default_value' => 'Найдите идеальное ПО'],
                            ['key' => 'field_hero_text', 'label' => 'Текст', 'name' => 'text', 'type' => 'textarea', 'rows' => 3],
                            ['key' => 'field_hero_btn_text', 'label' => 'Текст кнопки', 'name' => 'btn_text', 'type' => 'text', 'default_value' => 'Перейти в каталог'],
                            ['key' => 'field_hero_btn_link', 'label' => 'Ссылка кнопки', 'name' => 'btn_link', 'type' => 'text'],
                            // Future: Background image field
                        ]
                    ],
                    // 2. CATEGORIES
                    [
                        'key' => 'layout_categories',
                        'name' => 'categories',
                        'label' => 'Категории (Сетка)',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_cat_title', 'label' => 'Заголовок секции', 'name' => 'title', 'type' => 'text', 'default_value' => '🔍 Категории ПО'],
                            ['key' => 'field_cat_subtitle', 'label' => 'Подзаголовок', 'name' => 'subtitle', 'type' => 'text', 'default_value' => 'Найдите нужное решение'],
                            ['key' => 'field_cat_count', 'label' => 'Количество категорий', 'name' => 'count', 'type' => 'number', 'default_value' => 8],
                        ]
                    ],
                    // 3. POPULAR PRODUCTS (TABS)
                    [
                        'key' => 'layout_popular_products',
                        'name' => 'popular_products',
                        'label' => 'Популярные продукты (Табы)',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_pop_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text', 'default_value' => '🔥 Популярные подборки'],
                            [
                                'key' => 'field_pop_tabs',
                                'label' => 'Вкладки категорий',
                                'name' => 'tabs',
                                'type' => 'repeater',
                                'button_label' => 'Добавить вкладку',
                                'sub_fields' => [
                                    ['key' => 'field_pop_cat', 'label' => 'Категория', 'name' => 'category', 'type' => 'taxonomy', 'taxonomy' => 'software_category', 'field_type' => 'select', 'return_format' => 'id'],
                                    ['key' => 'field_pop_icon', 'label' => 'Иконка', 'name' => 'icon', 'type' => 'text', 'default_value' => '🔹'],
                                ]
                            ]
                        ]
                    ],
                    // 4. ADVANTAGES
                    [
                        'key' => 'layout_advantages',
                        'name' => 'advantages',
                        'label' => 'Преимущества',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_adv_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text', 'default_value' => 'Почему выбирают нас'],
                            ['key' => 'field_adv_subtitle', 'label' => 'Подзаголовок', 'name' => 'subtitle', 'type' => 'text'],
                            [
                                'key' => 'field_adv_items',
                                'label' => 'Элементы',
                                'name' => 'items',
                                'type' => 'repeater',
                                'button_label' => 'Добавить преимущество',
                                'sub_fields' => [
                                    ['key' => 'field_adv_icon', 'label' => 'Иконка', 'name' => 'icon', 'type' => 'text', 'default_value' => '✅'],
                                    ['key' => 'field_adv_name', 'label' => 'Название', 'name' => 'title', 'type' => 'text'],
                                    ['key' => 'field_adv_desc', 'label' => 'Описание', 'name' => 'text', 'type' => 'textarea', 'rows' => 2],
                                ]
                            ]
                        ]
                    ],
                    // 5. TESTIMONIALS
                    [
                        'key' => 'layout_testimonials',
                        'name' => 'testimonials',
                        'label' => 'Отзывы',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_testi_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text', 'default_value' => 'Отзывы клиентов'],
                            ['key' => 'field_testi_desc', 'label' => 'Описание', 'name' => 'subtitle', 'type' => 'text'],
                            [
                                'key' => 'field_testi_items',
                                'label' => 'Отзывы',
                                'name' => 'items',
                                'type' => 'repeater',
                                'sub_fields' => [
                                    ['key' => 'field_t_text', 'label' => 'Текст отзыва', 'name' => 'text', 'type' => 'textarea'],
                                    ['key' => 'field_t_name', 'label' => 'Имя', 'name' => 'name', 'type' => 'text'],
                                    ['key' => 'field_t_role', 'label' => 'Должность/Компания', 'name' => 'role', 'type' => 'text'],
                                    ['key' => 'field_t_avatar', 'label' => 'Инициалы (2 буквы)', 'name' => 'initials', 'type' => 'text', 'maxlength' => 2],
                                ]
                            ]
                        ]
                    ],
                    // 6. BLOG
                    [
                        'key' => 'layout_blog',
                        'name' => 'blog',
                        'label' => 'Блог (Последние записи)',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_blog_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text', 'default_value' => 'Последние статьи'],
                            ['key' => 'field_blog_subtitle', 'label' => 'Подзаголовок', 'name' => 'subtitle', 'type' => 'text'],
                            ['key' => 'field_blog_count', 'label' => 'Количество записей', 'name' => 'count', 'type' => 'number', 'default_value' => 3],
                        ]
                    ],
                    // 7. CTA
                    [
                        'key' => 'layout_cta',
                        'name' => 'cta',
                        'label' => 'CTA (Форма подписки)',
                        'display' => 'block',
                        'sub_fields' => [
                            ['key' => 'field_cta_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text', 'default_value' => 'Нужна помощь?'],
                            ['key' => 'field_cta_text', 'label' => 'Текст', 'name' => 'text', 'type' => 'textarea', 'rows' => 2],
                            ['key' => 'field_cta_btn', 'label' => 'Текст кнопки', 'name' => 'btn_text', 'type' => 'text', 'default_value' => 'Отправить'],
                        ]
                    ],
                ] // end layouts
            ] // end field
        ],
        'location' => [
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'page']
            ]
        ]
    ]);
}
