<?php
require_once __DIR__ . '/wp-load.php';

echo "=== Test Quiz Questions Inheritance ===\n\n";

// Find a parent-child pair
$terms = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
]);

$parent_id = 0;
$child_id = 0;

foreach ($terms as $term) {
    if ($term->parent !== 0) {
        $parent_id = $term->parent;
        $child_id = $term->term_id;
        break; // First found
    }
}

if (!$parent_id || !$child_id) {
    echo "ERROR: Could not find a parent-child category pair.\n";
    exit;
}

$parent_term = get_term($parent_id, 'software_category');
$child_term = get_term($child_id, 'software_category');

echo "Selected Pair:\n";
echo "Parent: {$parent_term->name} (ID: $parent_id)\n";
echo "Child: {$child_term->name} (ID: $child_id)\n\n";

// JSON 1
$json1 = '[
  {
    "q": "Для какого отдела в первую очередь нужна система (Родитель)?",
    "options": ["Продажи (Sales)", "Маркетинг", "Поддержка (Support)"]
  }
]';

// JSON 2
$json2 = '[
  {
    "q": "Какой размер вашей команды (Наследник)?",
    "options": ["1-10", "11-50", "Более 50"]
  }
]';

echo "1. Set parent JSON, clear child JSON...\n";
update_field('quiz_questions', $json1, 'software_category_' . $parent_id);
update_field('quiz_questions', '', 'software_category_' . $child_id);

echo "Parent questions (direct):\n";
print_r(softmir_get_category_quiz_questions($parent_id));

echo "\nChild questions (should be inherited from parent):\n";
print_r(softmir_get_category_quiz_questions($child_id));

echo "\n-------------------------------------\n";
echo "2. Set child JSON (override parent)...\n";
update_field('quiz_questions', $json2, 'software_category_' . $child_id);

echo "Child questions (should be unique to child):\n";
print_r(softmir_get_category_quiz_questions($child_id));

echo "\n-------------------------------------\n";
echo "3. Clear all to restore state...\n";
update_field('quiz_questions', '', 'software_category_' . $parent_id);
update_field('quiz_questions', '', 'software_category_' . $child_id);
echo "Cleanup done.\n";
