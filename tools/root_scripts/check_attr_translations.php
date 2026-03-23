<?php
/**
 * Debug: try linking the existing RemOnline translations manually
 * and see what happens
 */

// First, fix the existing orphans
echo "=== FIXING REMONLINE TRANSLATIONS ===\n";
$translations = ['ru' => 457, 'uk' => 1040, 'en' => 1041];
pll_save_post_translations($translations);

// Verify
echo "After manual fix:\n";
foreach ($translations as $lang => $id) {
    $trans = pll_get_post_translations($id);
    echo "  $lang ($id): " . json_encode($trans) . "\n";
}

// Now test: what does softmir_translate_post actually do?
// Let's trace the issue by simulating the translation flow
echo "\n=== TESTING TRANSLATION FLOW ===\n";

// Pick another RU software post to test
$test_posts = get_posts([
    'post_type' => 'software',
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'lang' => 'ru',
    'orderby' => 'ID',
    'order' => 'ASC',
    'exclude' => [457],
]);

if ($test_posts) {
    $test = $test_posts[0];
    echo "Test post: {$test->ID} - {$test->post_title}\n";
    
    // Check if it already has translations
    $trans = pll_get_post_translations($test->ID);
    echo "Current translations: " . json_encode($trans) . "\n";
    
    if (!isset($trans['uk'])) {
        echo "No UK translation - testing translation creation...\n";
        
        // Simulate what softmir_translate_post does step by step
        $target_lang = 'uk';
        $source_lang = pll_get_post_language($test->ID) ?: pll_default_language();
        
        // Step 1: Check pll_get_post
        $existing = pll_get_post($test->ID, $target_lang);
        echo "pll_get_post({$test->ID}, '$target_lang') = " . var_export($existing, true) . "\n";
        
        // Step 2: Create post
        $new_post = [
            'post_type' => $test->post_type,
            'post_status' => 'draft', // Draft so we can delete after test
            'post_author' => $test->post_author,
            'post_title' => '[TEST] ' . $test->post_title,
        ];
        $new_id = wp_insert_post($new_post);
        echo "Created test post: $new_id\n";
        
        // Step 3: Set language
        pll_set_post_language($new_id, $target_lang);
        $verified_lang = pll_get_post_language($new_id);
        echo "Set language: $target_lang, verified: $verified_lang\n";
        
        // Step 4: Try to link
        $current_trans = pll_get_post_translations($test->ID);
        echo "pll_get_post_translations({$test->ID}) BEFORE link: " . json_encode($current_trans) . "\n";
        
        $current_trans[$target_lang] = $new_id;
        $current_trans[$source_lang] = $test->ID;
        echo "Calling pll_save_post_translations with: " . json_encode($current_trans) . "\n";
        
        pll_save_post_translations($current_trans);
        
        // Step 5: Verify
        $after_trans = pll_get_post_translations($test->ID);
        echo "pll_get_post_translations({$test->ID}) AFTER link: " . json_encode($after_trans) . "\n";
        
        $after_new = pll_get_post_translations($new_id);
        echo "pll_get_post_translations($new_id) AFTER link: " . json_encode($after_new) . "\n";
        
        // Cleanup
        wp_delete_post($new_id, true);
        echo "Deleted test post $new_id\n";
    } else {
        echo "Already has UK translation - checking another post...\n";
    }
}
