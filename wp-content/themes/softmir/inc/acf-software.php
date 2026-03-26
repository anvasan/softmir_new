<?php
if (function_exists('acf_add_local_field_group')):

    acf_add_local_field_group(array(
        'key' => 'group_software_scout_details',
        'title' => 'Детали ПО (Scout Data)',
        'fields' => array(
            // === СЦЕНАРИИ ИСПОЛЬЗОВАНИЯ (Markdown) ===
            array(
                'key' => 'field_sw_scenarios_md',
                'label' => 'Сценарии использования',
                'name' => 'scenarios_md',
                'type' => 'textarea',
                'rows' => 10,
                'instructions' => 'Формат: ### Заголовок сценария' . "\n" . 'Текст описания сценария. Каждый блок ### = отдельный сценарий.',
            ),

            // === ТЕКСТОВЫЕ СПИСКИ (каждая строка = пункт) ===
            array(
                'key' => 'field_sw_top_reasons',
                'label' => 'Почему это ТОП (Плюсы)',
                'name' => 'top_reasons',
                'type' => 'textarea',
                'rows' => 5,
                'instructions' => 'Каждая мысль с новой строки',
            ),
            array(
                'key' => 'field_sw_disadvantages',
                'label' => 'Нюансы и Риски (Минусы)',
                'name' => 'disadvantages',
                'type' => 'textarea',
                'rows' => 5,
                'instructions' => 'Каждая мысль с новой строки',
            ),
            array(
                'key' => 'field_sw_best_for',
                'label' => 'Вам ПОДОЙДЁТ, если',
                'name' => 'best_for',
                'type' => 'textarea',
                'rows' => 5,
                'instructions' => 'Каждое условие с новой строки',
            ),
            array(
                'key' => 'field_sw_bad_for',
                'label' => 'Лучше НЕ БРАТЬ, если',
                'name' => 'bad_for',
                'type' => 'textarea',
                'rows' => 5,
                'instructions' => 'Каждое условие с новой строки',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'software',
                ),
            ),
        ),
        'menu_order' => 5,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

endif;
