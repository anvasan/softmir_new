<?php
// Hero Module — uses ACF page fields (registered in acf-home-options.php)
$page_id = get_option('page_on_front');
$title = get_field('home_header_title', $page_id) ?: __('Найдите идеальное ПО для вашего бизнеса', 'softmir');
$text = get_field('home_header_subtitle', $page_id) ?: __('Каталог проверенных CRM-систем, инструментов управления проектами и решений для e-commerce.', 'softmir');
$btn_text = get_field('home_header_btn_text', $page_id) ?: __('Перейти в каталог', 'softmir');
$btn_link = get_field('home_header_btn_url', $page_id) ?: get_post_type_archive_link('software');
?>
<section class="hero">
    <div class="container">
        <div class="hero-row">
            <div class="hero-content">
                <h1><?php echo wp_kses_post($title); ?></h1>
                <p><?php echo esc_html($text); ?></p>
                <?php if ($btn_link): ?>
                    <a href="<?php echo esc_url($btn_link); ?>" class="btn btn-primary hero-btn-cta">
                        <?php echo esc_html($btn_text); ?> →
                    </a>
                <?php
endif; ?>
            </div>

            <div class="hero-infographic">
                <svg class="infographic-svg" viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

                    <!-- Central Dashboard Monitor -->
                    <g transform="translate(120, 100)">
                        <g class="hero-float-2">
                            <!-- Monitor body -->
                            <rect width="260" height="175" rx="12" fill="white" stroke="#e2e8f0" stroke-width="1.5"/>
                            <rect x="12" y="12" width="236" height="140" rx="6" fill="#f8fafc"/>
                            <!-- Bar chart -->
                            <rect x="30" y="100" width="24" height="40" rx="3" fill="#bae6fd"/>
                            <rect x="62" y="80" width="24" height="60" rx="3" fill="#0ea5e9"/>
                            <rect x="94" y="90" width="24" height="50" rx="3" fill="#bae6fd"/>
                            <rect x="126" y="65" width="24" height="75" rx="3" fill="#0ea5e9"/>
                            <rect x="158" y="75" width="24" height="65" rx="3" fill="#8b5cf6"/>
                            <rect x="190" y="55" width="24" height="85" rx="3" fill="#0ea5e9"/>
                            <!-- Line chart -->
                            <path d="M30 45 L70 35 L110 40 L150 25 L190 30 L230 18" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" fill="none" class="hero-pulse-line"/>
                            <circle cx="30" cy="45" r="3" fill="#8b5cf6"/>
                            <circle cx="70" cy="35" r="3" fill="#8b5cf6"/>
                            <circle cx="110" cy="40" r="3" fill="#8b5cf6"/>
                            <circle cx="150" cy="25" r="3" fill="#8b5cf6"/>
                            <circle cx="190" cy="30" r="3" fill="#8b5cf6"/>
                            <circle cx="230" cy="18" r="3" fill="#8b5cf6"/>
                            <!-- Monitor stand -->
                            <rect x="100" y="175" width="60" height="8" rx="4" fill="#e2e8f0"/>
                            <rect x="120" y="175" width="20" height="20" rx="2" fill="#e2e8f0"/>
                            <rect x="90" y="192" width="80" height="6" rx="3" fill="#cbd5e1"/>
                        </g>
                    </g>

                    <!-- CRM / People icon (top-left) -->
                    <g transform="translate(30, 40)">
                        <g class="hero-float-1">
                            <rect width="70" height="70" rx="16" fill="white" stroke="#e2e8f0" stroke-width="1"/>
                            <circle cx="35" cy="28" r="10" fill="#0ea5e9" opacity="0.15"/>
                            <circle cx="35" cy="25" r="7" fill="#0ea5e9"/>
                            <path d="M20 48 C20 38 50 38 50 48" fill="#0ea5e9" opacity="0.7"/>
                            <text x="35" y="63" text-anchor="middle" fill="#64748b" font-size="8" font-weight="600" font-family="Inter, sans-serif">CRM</text>
                        </g>
                    </g>

                    <!-- Cloud/SaaS icon (top-right) -->
                    <g transform="translate(410, 20)">
                        <g class="hero-float-3">
                            <rect width="70" height="70" rx="16" fill="white" stroke="#e2e8f0" stroke-width="1"/>
                            <path d="M25 42 C20 42 18 36 22 33 C22 27 30 24 35 28 C38 24 48 24 48 32 C53 32 54 40 49 42 Z" fill="#8b5cf6" opacity="0.8"/>
                            <text x="35" y="62" text-anchor="middle" fill="#64748b" font-size="8" font-weight="600" font-family="Inter, sans-serif">SaaS</text>
                        </g>
                    </g>

                    <!-- E-commerce / Cart icon (bottom-right) -->
                    <g transform="translate(420, 240)">
                        <g class="hero-float-5">
                            <rect width="70" height="70" rx="16" fill="white" stroke="#e2e8f0" stroke-width="1"/>
                            <path d="M22 28 L26 28 L32 46 L50 46" stroke="#0ea5e9" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                            <circle cx="34" cy="52" r="3" fill="#0ea5e9"/>
                            <circle cx="47" cy="52" r="3" fill="#0ea5e9"/>
                            <path d="M27 30 L50 30 L47 42 L30 42 Z" fill="#0ea5e9" opacity="0.2" stroke="#0ea5e9" stroke-width="1.5"/>
                            <text x="35" y="66" text-anchor="middle" fill="#64748b" font-size="7" font-weight="600" font-family="Inter, sans-serif">Shop</text>
                        </g>
                    </g>

                    <!-- Project Management / Kanban (left-center) -->
                    <g transform="translate(10, 200)">
                        <g class="hero-float-4">
                            <rect width="80" height="75" rx="16" fill="white" stroke="#e2e8f0" stroke-width="1"/>
                            <rect x="14" y="16" width="16" height="5" rx="2" fill="#0ea5e9"/>
                            <rect x="14" y="24" width="16" height="12" rx="2" fill="#0ea5e9" opacity="0.3"/>
                            <rect x="14" y="39" width="16" height="8" rx="2" fill="#0ea5e9" opacity="0.3"/>
                            <rect x="33" y="16" width="16" height="5" rx="2" fill="#8b5cf6"/>
                            <rect x="33" y="24" width="16" height="8" rx="2" fill="#8b5cf6" opacity="0.3"/>
                            <rect x="33" y="35" width="16" height="12" rx="2" fill="#8b5cf6" opacity="0.3"/>
                            <rect x="52" y="16" width="16" height="5" rx="2" fill="#22c55e"/>
                            <rect x="52" y="24" width="16" height="15" rx="2" fill="#22c55e" opacity="0.3"/>
                            <text x="40" y="65" text-anchor="middle" fill="#64748b" font-size="7" font-weight="600" font-family="Inter, sans-serif">Проекты</text>
                        </g>
                    </g>

                    <!-- Analytics / Pie chart (top-center) -->
                    <g transform="translate(215, 5)">
                        <g class="hero-float-6">
                            <rect width="70" height="70" rx="16" fill="white" stroke="#e2e8f0" stroke-width="1"/>
                            <circle cx="35" cy="33" r="16" fill="#e0f2fe"/>
                            <path d="M35 17 A16 16 0 0 1 51 33 L35 33 Z" fill="#0ea5e9"/>
                            <path d="M51 33 A16 16 0 0 1 35 49 L35 33 Z" fill="#8b5cf6" opacity="0.7"/>
                            <text x="35" y="63" text-anchor="middle" fill="#64748b" font-size="7" font-weight="600" font-family="Inter, sans-serif">Analytics</text>
                        </g>
                    </g>

                    <!-- Automation / Gear icon (bottom-center) -->
                    <g transform="translate(90, 320)">
                        <g class="hero-float-1">
                            <rect width="70" height="65" rx="16" fill="white" stroke="#e2e8f0" stroke-width="1"/>
                            <g transform="translate(35, 28)">
                                <circle cx="0" cy="0" r="8" fill="#0ea5e9" opacity="0.2"/>
                                <circle cx="0" cy="0" r="5" fill="none" stroke="#0ea5e9" stroke-width="2"/>
                                <rect x="-2" y="-12" width="4" height="5" rx="1" fill="#0ea5e9"/>
                                <rect x="-2" y="7" width="4" height="5" rx="1" fill="#0ea5e9"/>
                                <rect x="-12" y="-2" width="5" height="4" rx="1" fill="#0ea5e9"/>
                                <rect x="7" y="-2" width="5" height="4" rx="1" fill="#0ea5e9"/>
                                <rect x="5" y="-10" width="4" height="5" rx="1" fill="#0ea5e9" transform="rotate(45, 7, -7.5)"/>
                                <rect x="-10" y="5" width="4" height="5" rx="1" fill="#0ea5e9" transform="rotate(45, -8, 7.5)"/>
                                <rect x="-10" y="-10" width="4" height="5" rx="1" fill="#0ea5e9" transform="rotate(-45, -8, -7.5)"/>
                                <rect x="5" y="5" width="4" height="5" rx="1" fill="#0ea5e9" transform="rotate(-45, 7, 7.5)"/>
                            </g>
                            <text x="35" y="54" text-anchor="middle" fill="#64748b" font-size="7" font-weight="600" font-family="Inter, sans-serif">Авто</text>
                        </g>
                    </g>

                    <!-- Connecting dotted lines from icons to monitor -->
                    <line x1="100" y1="75" x2="140" y2="120" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>
                    <line x1="410" y1="55" x2="380" y2="115" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>
                    <line x1="90" y1="237" x2="120" y2="210" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>
                    <line x1="420" y1="275" x2="380" y2="260" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>
                    <line x1="250" y1="75" x2="250" y2="100" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>
                    <line x1="125" y1="340" x2="180" y2="295" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>

                    <!-- Decorative dots -->
                    <circle cx="400" cy="150" r="3" fill="#0ea5e9" opacity="0.3"/>
                    <circle cx="105" cy="140" r="2" fill="#8b5cf6" opacity="0.3"/>
                    <circle cx="380" cy="340" r="4" fill="#0ea5e9" opacity="0.2"/>
                    <circle cx="320" cy="350" r="2" fill="#8b5cf6" opacity="0.25"/>
                    <circle cx="460" cy="180" r="3" fill="#8b5cf6" opacity="0.2"/>
                </svg>
            </div>
        </div>
    </div>
</section>
