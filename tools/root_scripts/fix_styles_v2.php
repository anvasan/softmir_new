<?php
$file = 'd:/laragon/www/WP_Test_anti/wp-content/themes/softmir/style.css';
$content = file_get_contents($file);

// The marker is the start of the corruption zone
$marker = '/* ========== Categories Cards Grid (Homepage) ========== */';
$pos = strpos($content, $marker);

if ($pos !== false) {
    // Keep everything UP TO the end of the marker.
    // This effectively truncates the file right after this comment.
    $valid_content = substr($content, 0, $pos + strlen($marker));

    // Add a clean newline
    $valid_content .= "\n\n";

    // Define the correct CSS for horizontal cards
    $new_css = <<<CSS
/* ========== Horizontal Software Card (Popular Products) ========== */
.software-grid-horizontal {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .software-grid-horizontal {
        grid-template-columns: repeat(2, 1fr); /* 2 columns on desktop/tablet */
    }
}

.software-card-horizontal {
    display: flex;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
    align-items: center;
    text-decoration: none;
    height: 100%;
}

.software-card-horizontal:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--brand-300);
    transform: translateY(-2px);
    background: #fff;
}

.software-card-horizontal .card-side {
    width: 90px;
    height: 100%;
    flex-shrink: 0;
    border-right: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    background: #fcfcfc;
}

.software-card-horizontal .card-logo-img {
    max-width: 100%;
    max-height: 50px;
    object-fit: contain;
}

.software-card-horizontal .card-logo-placeholder {
    width: 50px;
    height: 50px;
    background: var(--brand-50);
    color: var(--brand);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    border-radius: 4px;
    font-size: 1.2rem;
}

.software-card-horizontal .card-main {
    flex: 1;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.4rem;
    min-width: 0;
}

.software-card-horizontal .card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.software-card-horizontal .card-title {
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.3;
    margin: 0;
    color: var(--brand-600);
}

.software-card-horizontal .card-title a {
    color: var(--brand-600);
    text-decoration: none;
}

.software-card-horizontal .card-title a:hover {
    color: var(--brand-800);
    text-decoration: underline;
}

.software-card-horizontal .card-rating-row {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    flex-shrink: 0;
    margin-left: auto;
}

.software-card-horizontal .review-count {
    color: var(--gray-400);
}

.software-card-horizontal .card-desc {
    font-size: 0.8rem;
    color: var(--gray-500);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
CSS;

    $final_content = $valid_content . $new_css;

    // Write back to file
    file_put_contents($file, $final_content);
    echo "<h1>FIXED_V2 SUCCESS</h1><p>Cleaned corrupted data and appended new CSS.</p>";
}
else {
    echo "<h1>ERROR</h1><p>Marker not found. Please check file content manually.</p>";
}
?>
