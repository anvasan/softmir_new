<?php
/**
 * SOF-9: Data Migration Script
 * Migrates data from HivePress (wpsoftzor) → SoftMir custom CPTs (wp_test_anti)
 *
 * Usage: D:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe migration.php
 */

// ===== Load WordPress environment =====
define('ABSPATH_TARGET', __DIR__ . '/');
$_SERVER['HTTP_HOST'] = 'wp-test-anti.test';
$_SERVER['REQUEST_URI'] = '/';

// Suppress any output buffering from WP
ob_start();
require_once ABSPATH_TARGET . 'wp-load.php';
ob_end_clean();

// ===== Source DB connection =====
$source_db = new mysqli('localhost', 'root', '', 'wpsoftzor');
if ($source_db->connect_error) {
    die("Source DB connection failed: " . $source_db->connect_error . PHP_EOL);
}
$source_db->set_charset('utf8mb4');

echo "============================================" . PHP_EOL;
echo "  SOF-9: Data Migration (HivePress → SoftMir)" . PHP_EOL;
echo "============================================" . PHP_EOL;
echo "Source DB: wpsoftzor" . PHP_EOL;
echo "Target DB: wp_test_anti" . PHP_EOL;
echo PHP_EOL;

// Stats tracking
$stats = [
    'categories_imported' => 0,
    'categories_errors' => 0,
    'attributes_imported' => 0,
    'attributes_errors' => 0,
    'tax_attrs_imported' => 0,
    'listings_imported' => 0,
    'listings_errors' => 0,
    'media_copied' => 0,
    'media_errors' => 0,
];

// ================================================================
//  STEP 0: CLEANUP
// ================================================================
echo "=== STEP 0: CLEANUP ===" . PHP_EOL;

// Delete all software posts
$sw_posts = get_posts(['post_type' => 'software', 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids']);
foreach ($sw_posts as $pid) {
    wp_delete_post($pid, true);
}
echo "  Deleted " . count($sw_posts) . " software posts" . PHP_EOL;

// Delete all sw_attribute posts
$attr_posts = get_posts(['post_type' => 'sw_attribute', 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids']);
foreach ($attr_posts as $pid) {
    wp_delete_post($pid, true);
}
echo "  Deleted " . count($attr_posts) . " sw_attribute posts" . PHP_EOL;

// Delete all software_category terms
$terms = get_terms(['taxonomy' => 'software_category', 'hide_empty' => false, 'fields' => 'ids']);
if (!is_wp_error($terms)) {
    foreach ($terms as $tid) {
        wp_delete_term($tid, 'software_category');
    }
    echo "  Deleted " . count($terms) . " software_category terms" . PHP_EOL;
}
else {
    echo "  No software_category terms to delete" . PHP_EOL;
}

echo "  Cleanup complete." . PHP_EOL . PHP_EOL;


// ================================================================
//  STEP 1: CATEGORIES
//  hp_listing_category → software_category
// ================================================================
echo "=== STEP 1: CATEGORIES ===" . PHP_EOL;

// Fetch all source categories
$src_cats = [];
$r = $source_db->query("
    SELECT t.term_id, t.name, t.slug, tt.parent, tt.description, tt.count
    FROM wp_terms t
    JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
    WHERE tt.taxonomy = 'hp_listing_category'
    ORDER BY tt.parent ASC, t.name ASC
");
while ($row = $r->fetch_assoc()) {
    $src_cats[] = $row;
}
echo "  Found " . count($src_cats) . " source categories" . PHP_EOL;

// ID mapping: source_term_id => new_term_id
$cat_id_map = [];

// Multi-pass: first insert root categories (parent=0), then children
$remaining = $src_cats;
$max_passes = 10;
$pass = 0;

while (!empty($remaining) && $pass < $max_passes) {
    $pass++;
    $still_remaining = [];

    foreach ($remaining as $cat) {
        $new_parent = 0;

        if ($cat['parent'] > 0) {
            // Parent not yet imported?
            if (!isset($cat_id_map[$cat['parent']])) {
                $still_remaining[] = $cat;
                continue;
            }
            $new_parent = $cat_id_map[$cat['parent']];
        }

        $result = wp_insert_term($cat['name'], 'software_category', [
            'slug' => $cat['slug'],
            'parent' => $new_parent,
            'description' => $cat['description'] ?: '',
        ]);

        if (is_wp_error($result)) {
            // If term already exists, try to get existing
            if ($result->get_error_code() === 'term_exists') {
                $existing_id = $result->get_error_data('term_exists');
                $cat_id_map[$cat['term_id']] = (int)$existing_id;
                $stats['categories_imported']++;
            }
            else {
                echo "  ERROR importing category '{$cat['name']}': " . $result->get_error_message() . PHP_EOL;
                $stats['categories_errors']++;
            }
        }
        else {
            $cat_id_map[$cat['term_id']] = $result['term_id'];
            $stats['categories_imported']++;
        }
    }

    $remaining = $still_remaining;
}

if (!empty($remaining)) {
    echo "  WARNING: " . count($remaining) . " categories could not be imported (orphaned parents)" . PHP_EOL;
    $stats['categories_errors'] += count($remaining);
}

echo "  Imported {$stats['categories_imported']} categories (errors: {$stats['categories_errors']})" . PHP_EOL;
echo "  Passes needed: {$pass}" . PHP_EOL . PHP_EOL;


// ================================================================
//  STEP 2: ATTRIBUTES
//  hp_listing_attribute → sw_attribute (boolean features)
//  hp_listing_* taxonomies → sw_attribute (select-type)
// ================================================================
echo "=== STEP 2: ATTRIBUTES ===" . PHP_EOL;

// --- 2A: Boolean attributes from hp_listing_attribute ---
echo "  --- 2A: Boolean attributes from hp_listing_attribute ---" . PHP_EOL;

$attr_id_map = []; // source hp_listing_attribute post_name => new sw_attribute post_id
$hp_attr_name_to_id = []; // hp attribute post_name => new_id (for listing meta mapping)

$r = $source_db->query("
    SELECT ID, post_title, post_name
    FROM wp_posts
    WHERE post_type = 'hp_listing_attribute' AND post_status = 'publish'
    ORDER BY post_title
");

while ($row = $r->fetch_assoc()) {
    // Skip non-boolean attrs (special ones like link, des, vidio, counter, price, integro)
    $skip_names = ['link', 'des', 'vidio', 'counter', 'price', 'integro-2'];
    if (in_array($row['post_name'], $skip_names)) {
        continue;
    }

    $new_id = wp_insert_post([
        'post_type' => 'sw_attribute',
        'post_title' => $row['post_title'],
        'post_name' => sanitize_title($row['post_title']),
        'post_status' => 'publish',
    ], true);

    if (is_wp_error($new_id)) {
        echo "    ERROR: {$row['post_title']}: " . $new_id->get_error_message() . PHP_EOL;
        $stats['attributes_errors']++;
        continue;
    }

    // Set attribute meta
    update_post_meta($new_id, '_attr_type', 'checkbox');
    update_post_meta($new_id, '_attr_icon', '✅');
    update_post_meta($new_id, '_attr_filterable', '1');
    update_post_meta($new_id, '_attr_card_position', 'middle');
    update_post_meta($new_id, '_attr_page_position', 'middle');
    update_post_meta($new_id, '_attr_options', '');
    update_post_meta($new_id, '_attr_multiple', '0');
    update_post_meta($new_id, '_attr_categories', []);

    $hp_attr_name_to_id[$row['post_name']] = $new_id;
    $attr_id_map[$row['ID']] = $new_id;
    $stats['attributes_imported']++;
}

echo "    Imported {$stats['attributes_imported']} boolean attributes" . PHP_EOL;

// --- 2B: Taxonomy-based attributes ---
echo "  --- 2B: Taxonomy-based select attributes ---" . PHP_EOL;

$tax_attr_map = []; // taxonomy_name => new_sw_attribute_id

$taxonomy_attrs = [
    'hp_listing_platforma' => [
        'title' => 'Платформа',
        'icon' => '💻',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'footer',
        'page_pos' => 'sidebar',
    ],
    'hp_listing_lingvo' => [
        'title' => 'Язык интерфейса',
        'icon' => '🌐',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'none',
        'page_pos' => 'sidebar',
    ],
    'hp_listing_oplata' => [
        'title' => 'Модель оплаты',
        'icon' => '💰',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'footer',
        'page_pos' => 'sidebar',
    ],
    'hp_listing_biz' => [
        'title' => 'Размер бизнеса',
        'icon' => '🏢',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'footer',
        'page_pos' => 'sidebar',
    ],
    'hp_listing_sferabiz' => [
        'title' => 'Сфера бизнеса',
        'icon' => '🏭',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'none',
        'page_pos' => 'middle',
    ],
    'hp_listing_tip_ustanovki' => [
        'title' => 'Тип установки',
        'icon' => '⚙️',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'footer',
        'page_pos' => 'sidebar',
    ],
    'hp_listing_integro_2' => [
        'title' => 'Интеграции',
        'icon' => '🔗',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'none',
        'page_pos' => 'middle',
    ],
    'hp_listing_podderjka' => [
        'title' => 'Поддержка',
        'icon' => '🛟',
        'type' => 'checkbox',
        'multiple' => '1',
        'filterable' => '1',
        'card_pos' => 'none',
        'page_pos' => 'sidebar',
    ],
];

foreach ($taxonomy_attrs as $tax_name => $config) {
    // Gather options from source taxonomy terms
    $options = [];
    $r = $source_db->query("
        SELECT t.name
        FROM wp_terms t
        JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
        WHERE tt.taxonomy = '{$tax_name}'
        ORDER BY t.name
    ");
    while ($row = $r->fetch_assoc()) {
        $options[] = $row['name'];
    }

    $new_id = wp_insert_post([
        'post_type' => 'sw_attribute',
        'post_title' => $config['title'],
        'post_name' => sanitize_title($config['title']),
        'post_status' => 'publish',
    ], true);

    if (is_wp_error($new_id)) {
        echo "    ERROR: {$config['title']}: " . $new_id->get_error_message() . PHP_EOL;
        $stats['attributes_errors']++;
        continue;
    }

    update_post_meta($new_id, '_attr_type', $config['type']);
    update_post_meta($new_id, '_attr_icon', $config['icon']);
    update_post_meta($new_id, '_attr_filterable', $config['filterable']);
    update_post_meta($new_id, '_attr_card_position', $config['card_pos']);
    update_post_meta($new_id, '_attr_page_position', $config['page_pos']);
    update_post_meta($new_id, '_attr_options', implode(', ', $options));
    update_post_meta($new_id, '_attr_multiple', $config['multiple']);
    update_post_meta($new_id, '_attr_categories', []);

    $tax_attr_map[$tax_name] = $new_id;
    $stats['tax_attrs_imported']++;

    echo "    Created '{$config['title']}' (ID={$new_id}) with " . count($options) . " options" . PHP_EOL;
}

echo "  Imported {$stats['tax_attrs_imported']} taxonomy-based attributes" . PHP_EOL . PHP_EOL;


// ================================================================
//  STEP 3: LISTINGS
//  hp_listing → software
// ================================================================
echo "=== STEP 3: LISTINGS ===" . PHP_EOL;

$r = $source_db->query("
    SELECT ID, post_title, post_name, post_content, post_excerpt, post_status, post_date, post_date_gmt
    FROM wp_posts
    WHERE post_type = 'hp_listing' AND post_status = 'publish'
    ORDER BY post_title
");

$listings = [];
while ($row = $r->fetch_assoc()) {
    $listings[] = $row;
}

echo "  Found " . count($listings) . " source listings" . PHP_EOL;

foreach ($listings as $listing) {
    $src_id = $listing['ID'];

    // Fetch all meta for this listing
    $meta = [];
    $mr = $source_db->query("SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = {$src_id}");
    while ($mrow = $mr->fetch_assoc()) {
        $meta[$mrow['meta_key']] = $mrow['meta_value'];
    }

    // Fetch taxonomy term relationships
    $term_rels = [];
    $tr = $source_db->query("
        SELECT t.name, tt.taxonomy
        FROM wp_term_relationships trel
        JOIN wp_term_taxonomy tt ON trel.term_taxonomy_id = tt.term_taxonomy_id
        JOIN wp_terms t ON tt.term_id = t.term_id
        WHERE trel.object_id = {$src_id}
    ");
    while ($trow = $tr->fetch_assoc()) {
        $term_rels[$trow['taxonomy']][] = $trow['name'];
    }

    // Create the software post
    $new_id = wp_insert_post([
        'post_type' => 'software',
        'post_title' => $listing['post_title'],
        'post_name' => $listing['post_name'],
        'post_content' => $listing['post_content'],
        'post_excerpt' => $listing['post_excerpt'],
        'post_status' => $listing['post_status'],
        'post_date' => $listing['post_date'],
        'post_date_gmt' => $listing['post_date_gmt'],
    ], true);

    if (is_wp_error($new_id)) {
        echo "  ERROR: '{$listing['post_title']}': " . $new_id->get_error_message() . PHP_EOL;
        $stats['listings_errors']++;
        continue;
    }

    // --- ACF Fields mapping ---
    // short_description ← hp_des
    if (!empty($meta['hp_des'])) {
        update_post_meta($new_id, 'short_description', wp_strip_all_tags(html_entity_decode($meta['hp_des'])));
        update_post_meta($new_id, '_short_description', 'field_sw_short_desc');
    }

    // website_url ← hp_link
    if (!empty($meta['hp_link'])) {
        update_post_meta($new_id, 'website_url', esc_url_raw($meta['hp_link']));
        update_post_meta($new_id, '_website_url', 'field_sw_website');
    }

    // video_url ← hp_vidio
    if (!empty($meta['hp_vidio'])) {
        update_post_meta($new_id, 'video_url', esc_url_raw($meta['hp_vidio']));
        update_post_meta($new_id, '_video_url', 'field_sw_video');
    }

    // advantages ← preimushhestva
    if (!empty($meta['preimushhestva'])) {
        update_post_meta($new_id, 'advantages', $meta['preimushhestva']);
        update_post_meta($new_id, '_advantages', 'field_sw_advantages');
    }

    // business_areas ← sferabiznesa
    if (!empty($meta['sferabiznesa'])) {
        update_post_meta($new_id, 'business_areas', $meta['sferabiznesa']);
        update_post_meta($new_id, '_business_areas', 'field_sw_areas');
    }

    // --- Assign software_category terms ---
    if (!empty($term_rels['hp_listing_category'])) {
        // Find matching category names that were imported
        $cat_names = $term_rels['hp_listing_category'];
        $target_term_ids = [];

        foreach ($cat_names as $cname) {
            $found = get_term_by('name', $cname, 'software_category');
            if ($found) {
                $target_term_ids[] = (int)$found->term_id;
            }
        }

        if (!empty($target_term_ids)) {
            wp_set_object_terms($new_id, $target_term_ids, 'software_category');
        }
    }

    // --- Boolean attributes (hp_{attr_name} meta → _sw_attr_{new_id}) ---
    foreach ($hp_attr_name_to_id as $hp_name => $sw_attr_id) {
        $meta_key = 'hp_' . $hp_name;
        if (isset($meta[$meta_key]) && $meta[$meta_key]) {
            update_post_meta($new_id, '_sw_attr_' . $sw_attr_id, '1');
        }
    }

    // --- Taxonomy-based attributes (term names → _sw_attr_{new_id} as array) ---
    foreach ($tax_attr_map as $tax_name => $sw_attr_id) {
        if (!empty($term_rels[$tax_name])) {
            update_post_meta($new_id, '_sw_attr_' . $sw_attr_id, $term_rels[$tax_name]);
        }
    }

    $stats['listings_imported']++;
    echo "  ✓ '{$listing['post_title']}' => ID={$new_id}" . PHP_EOL;
}

echo "  Imported {$stats['listings_imported']} listings (errors: {$stats['listings_errors']})" . PHP_EOL . PHP_EOL;


// ================================================================
//  STEP 4: MEDIA
//  Copy thumbnails from source uploads → target uploads
// ================================================================
echo "=== STEP 4: MEDIA ===" . PHP_EOL;

$source_uploads = 'D:/laragon/www/wordpress/wp-content/uploads/';
$target_uploads = 'D:/laragon/www/WP_Test_anti/wp-content/uploads/';

// Get listing → thumbnail mapping from source
$r = $source_db->query("
    SELECT p.ID as listing_id, p.post_name as listing_slug,
           pm.meta_value as thumb_id
    FROM wp_posts p
    JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
    WHERE p.post_type = 'hp_listing' AND p.post_status = 'publish'
");

$thumb_map = [];
while ($row = $r->fetch_assoc()) {
    $thumb_map[$row['listing_slug']] = $row['thumb_id'];
}

foreach ($thumb_map as $listing_slug => $thumb_id) {
    // Get the attachment file path from source
    $ar = $source_db->query("
        SELECT pm.meta_value as file_path, p.post_title, p.post_mime_type
        FROM wp_postmeta pm
        JOIN wp_posts p ON pm.post_id = p.ID
        WHERE pm.post_id = {$thumb_id} AND pm.meta_key = '_wp_attached_file'
    ");
    $arow = $ar->fetch_assoc();
    if (!$arow)
        continue;

    $src_file = $source_uploads . $arow['file_path'];
    $tgt_file = $target_uploads . $arow['file_path'];

    if (!file_exists($src_file)) {
        echo "  WARN: Source file missing: {$src_file}" . PHP_EOL;
        $stats['media_errors']++;
        continue;
    }

    // Create target directory if needed
    $tgt_dir = dirname($tgt_file);
    if (!is_dir($tgt_dir)) {
        mkdir($tgt_dir, 0755, true);
    }

    // Copy file
    if (!file_exists($tgt_file)) {
        copy($src_file, $tgt_file);
    }

    // Find the software post by slug
    $sw_posts = get_posts([
        'post_type' => 'software',
        'name' => $listing_slug,
        'numberposts' => 1,
    ]);

    if (empty($sw_posts)) {
        echo "  WARN: No software post found for slug '{$listing_slug}'" . PHP_EOL;
        continue;
    }

    $sw_post = $sw_posts[0];

    // Check if attachment already exists
    $existing = get_posts([
        'post_type' => 'attachment',
        'meta_key' => '_wp_attached_file',
        'meta_value' => $arow['file_path'],
        'numberposts' => 1,
    ]);

    if (!empty($existing)) {
        $attach_id = $existing[0]->ID;
    }
    else {
        // Create attachment post
        $attach_id = wp_insert_attachment([
            'post_title' => $arow['post_title'],
            'post_mime_type' => $arow['post_mime_type'],
            'post_status' => 'inherit',
            'post_parent' => $sw_post->ID,
        ], $tgt_file, $sw_post->ID);

        if (is_wp_error($attach_id)) {
            echo "  ERROR creating attachment for '{$listing_slug}': " . $attach_id->get_error_message() . PHP_EOL;
            $stats['media_errors']++;
            continue;
        }

        // Generate attachment metadata
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $tgt_file);
        wp_update_attachment_metadata($attach_id, $attach_data);
    }

    // Set as thumbnail
    set_post_thumbnail($sw_post->ID, $attach_id);

    // Also set as company_logo (ACF)
    update_post_meta($sw_post->ID, 'company_logo', wp_get_attachment_url($attach_id));
    update_post_meta($sw_post->ID, '_company_logo', 'field_sw_logo');

    $stats['media_copied']++;
    echo "  ✓ Media for '{$listing_slug}' => attachment ID={$attach_id}" . PHP_EOL;
}

echo "  Copied {$stats['media_copied']} media files (errors: {$stats['media_errors']})" . PHP_EOL . PHP_EOL;


// ================================================================
//  SUMMARY
// ================================================================
echo "============================================" . PHP_EOL;
echo "  MIGRATION COMPLETE" . PHP_EOL;
echo "============================================" . PHP_EOL;
echo "  Categories: {$stats['categories_imported']} imported, {$stats['categories_errors']} errors" . PHP_EOL;
echo "  Boolean Attributes: {$stats['attributes_imported']} imported" . PHP_EOL;
echo "  Taxonomy Attributes: {$stats['tax_attrs_imported']} imported" . PHP_EOL;
echo "  Listings: {$stats['listings_imported']} imported, {$stats['listings_errors']} errors" . PHP_EOL;
echo "  Media: {$stats['media_copied']} copied, {$stats['media_errors']} errors" . PHP_EOL;
echo "============================================" . PHP_EOL;

$source_db->close();
echo PHP_EOL . "Done." . PHP_EOL;
