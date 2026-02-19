<?php
/**
 * Content Seeder Script — Run once via WP-CLI: wp eval-file seed-content.php
 */

if (!defined('ABSPATH')) {
    echo "Run via WP-CLI: wp eval-file seed-content.php\n";
    exit;
}

// ========== Software Products ==========
$products = [
    [
        'title' => 'RemOnline',
        'slug' => 'remonline',
        'category' => 'crm',
        'short_description' => 'CRM и ERP для сервисных центров и мастерских. Управление заказами, складом, финансами и сотрудниками в одном окне.',
        'content' => '<p>RemOnline — облачная система для автоматизации сервисного бизнеса. Подходит для ремонтных мастерских, сервисных центров, медицинских лабораторий и других организаций, работающих с заявками и заказами.</p><p>Система включает модули для управления заказами, складом, финансами, персоналом и клиентской базой. Интеграция с телефонией, кассами и курьерскими службами.</p>',
        'website_url' => 'https://remonline.app/',
        'price_summary' => 'От $9/мес',
        'target_markets' => ['Россия', 'СНГ', 'Европа'],
        'is_featured' => true,
        'is_pinned' => true,
        'pricing' => [
            ['plan_name' => 'Стартовый', 'plan_price' => '$9/мес', 'plan_note' => '1 локация'],
            ['plan_name' => 'Бизнес', 'plan_price' => '$19/мес', 'plan_note' => 'До 3 локаций'],
            ['plan_name' => 'Премиум', 'plan_price' => '$39/мес', 'plan_note' => 'Безлимит + API'],
        ],
        'key_features' => ['Управление заказами', 'Складской учёт', 'Финансовый модуль', 'SMS и email-уведомления', 'Отчёты и аналитика', 'Мобильное приложение'],
        'advantages' => ['Быстрый старт за 15 минут', 'Интуитивный интерфейс', 'Интеграция с 50+ сервисами', 'Техподдержка 24/7'],
        'business_areas' => ['Сервисные центры', 'Ремонт техники', 'Стоматология', 'Автосервисы', 'Ателье'],
    ],
    [
        'title' => 'KeyCRM',
        'slug' => 'keycrm',
        'category' => 'crm',
        'short_description' => 'CRM для e-commerce и розничной торговли. Все маркетплейсы, мессенджеры и службы доставки в одном окне.',
        'content' => '<p>KeyCRM — специализированная CRM-система для интернет-магазинов и розничного бизнеса. Интеграция с Rozetka, Prom, OLX, Nova Poshta, Instagram и другими площадками.</p><p>Автоматизирует обработку заказов, учёт товаров, общение с клиентами и отправку посылок. Подключается к 20+ маркетплейсам и 10+ службам доставки.</p>',
        'website_url' => 'https://keycrm.app/',
        'price_summary' => 'От $19/мес',
        'target_markets' => ['СНГ', 'Европа'],
        'is_featured' => true,
        'is_pinned' => true,
        'pricing' => [
            ['plan_name' => 'Base', 'plan_price' => '$19/мес', 'plan_note' => '200 заказов'],
            ['plan_name' => 'Pro', 'plan_price' => '$39/мес', 'plan_note' => '1000 заказов'],
            ['plan_name' => 'Enterprise', 'plan_price' => '$79/мес', 'plan_note' => 'Безлимит'],
        ],
        'key_features' => ['Мульти-канальность', 'Авто-обработка заказов', 'Интеграция с маркетплейсами', 'Складской учёт', 'Аналитика продаж'],
        'advantages' => ['Настройка за 30 минут', 'Поддержка 20+ маркетплейсов', 'Встроенный мессенджер', 'Гибкие отчёты'],
        'business_areas' => ['Интернет-магазины', 'Розничная торговля', 'Дропшиппинг', 'Опт'],
    ],
    [
        'title' => 'SalesDrive',
        'slug' => 'salesdrive',
        'category' => 'crm',
        'short_description' => 'CRM для увеличения продаж. Автоматизация воронки от первого контакта до сделки.',
        'content' => '<p>SalesDrive — CRM-система, сфокусированная на увеличении конверсии и управлении командой продаж. Воронки, лиды, задачи, IP-телефония, email-рассылки и детальная аналитика.</p>',
        'website_url' => 'https://salesdrive.me/',
        'price_summary' => 'От $15/мес',
        'target_markets' => ['СНГ'],
        'is_featured' => false,
        'is_pinned' => false,
        'pricing' => [
            ['plan_name' => 'Start', 'plan_price' => '$15/мес', 'plan_note' => 'До 3 пользователей'],
            ['plan_name' => 'Business', 'plan_price' => '$35/мес', 'plan_note' => 'До 15 пользователей'],
        ],
        'key_features' => ['Воронка продаж', 'IP-телефония', 'Email-трекинг', 'Автоматические задачи', 'Детальная аналитика'],
        'advantages' => ['Простота настройки', 'Фокус на продажах', 'Детальные воронки', 'Интеграция с почтой'],
        'business_areas' => ['B2B-продажи', 'Консалтинг', 'Агентства недвижимости'],
    ],
    [
        'title' => 'NetHunt CRM',
        'slug' => 'nethunt-crm',
        'category' => 'crm',
        'short_description' => 'CRM внутри Gmail. Управляйте продажами и клиентами, не выходя из почты.',
        'content' => '<p>NetHunt CRM — уникальная CRM-система, которая полностью интегрируется с Gmail и Google Workspace. Создавайте воронки, управляйте контактами и автоматизируйте рутину прямо в почтовом ящике.</p>',
        'website_url' => 'https://nethunt.com/',
        'price_summary' => 'От $24/мес',
        'target_markets' => ['Россия', 'СНГ', 'Европа', 'США'],
        'is_featured' => true,
        'is_pinned' => false,
        'pricing' => [
            ['plan_name' => 'Basic', 'plan_price' => '$24/мес', 'plan_note' => 'За пользователя'],
            ['plan_name' => 'Business', 'plan_price' => '$48/мес', 'plan_note' => 'Автоматизация + API'],
            ['plan_name' => 'Advanced', 'plan_price' => '$96/мес', 'plan_note' => 'Все функции'],
        ],
        'key_features' => ['Интеграция с Gmail', 'Автоматизация workflow', 'Трекинг email-открытий', 'Воронки продаж', 'Google Workspace'],
        'advantages' => ['Работа прямо в Gmail', 'Нет кривой обучения', 'Мощная автоматизация', 'API и интеграции'],
        'business_areas' => ['IT-компании', 'Стартапы', 'Маркетинговые агентства', 'Консалтинг'],
    ],
    [
        'title' => 'Monday.com',
        'slug' => 'monday-com',
        'category' => 'project-management',
        'short_description' => 'Платформа для управления проектами и командной работы. Визуальные доски, автоматизация и интеграции.',
        'content' => '<p>Monday.com — одна из ведущих платформ для управления проектами и работой команд. Гибкие визуальные доски, диаграммы Ганта, автоматизации, дашборды и более 200 интеграций.</p>',
        'website_url' => 'https://monday.com/',
        'price_summary' => 'От $8/мес',
        'target_markets' => ['Россия', 'СНГ', 'Европа', 'США'],
        'is_featured' => true,
        'is_pinned' => true,
        'pricing' => [
            ['plan_name' => 'Individual', 'plan_price' => 'Бесплатно', 'plan_note' => 'До 2 пользователей'],
            ['plan_name' => 'Basic', 'plan_price' => '$8/мес', 'plan_note' => 'За пользователя'],
            ['plan_name' => 'Standard', 'plan_price' => '$10/мес', 'plan_note' => 'Автоматизация'],
            ['plan_name' => 'Pro', 'plan_price' => '$16/мес', 'plan_note' => 'Полный функционал'],
        ],
        'key_features' => ['Визуальные доски', 'Диаграммы Ганта', '200+ интеграций', 'Автоматизация', 'Дашборды', 'Мобильное приложение'],
        'advantages' => ['Отмечен Gartner', 'Гибкость настройки', 'Красивый интерфейс', 'Поддержка Agile/Waterfall'],
        'business_areas' => ['IT-команды', 'Маркетинг', 'HR', 'Строительство', 'Производство'],
    ],
    [
        'title' => 'Wrike',
        'slug' => 'wrike',
        'category' => 'project-management',
        'short_description' => 'Профессиональное управление проектами для средних и крупных компаний с мощной аналитикой.',
        'content' => '<p>Wrike — корпоративная платформа для управления проектами и совместной работы. Идеальна для сложных проектов с несколькими командами, кросс-функциональными зависимостями и строгими дедлайнами.</p>',
        'website_url' => 'https://wrike.com/',
        'price_summary' => 'От $9.80/мес',
        'target_markets' => ['Россия', 'СНГ', 'Европа', 'США'],
        'is_featured' => false,
        'is_pinned' => false,
        'pricing' => [
            ['plan_name' => 'Free', 'plan_price' => 'Бесплатно', 'plan_note' => 'До 5 пользователей'],
            ['plan_name' => 'Team', 'plan_price' => '$9.80/мес', 'plan_note' => 'За пользователя'],
            ['plan_name' => 'Business', 'plan_price' => '$24.80/мес', 'plan_note' => 'Кастомизация'],
        ],
        'key_features' => ['Диаграммы Ганта', 'Канбан-доски', 'Управление ресурсами', 'Отчёты', 'Тайм-трекинг', 'Proofing'],
        'advantages' => ['Enterprise-уровень', 'Мощная аналитика', '400+ интеграций', 'GDPR-совместимость'],
        'business_areas' => ['Enterprise', 'Маркетинг', 'Разработка ПО', 'Профессиональные услуги'],
    ],
    [
        'title' => 'Helium 10',
        'slug' => 'helium-10',
        'category' => 'ecommerce',
        'short_description' => 'Набор инструментов для продавцов на Amazon. SEO, аналитика, отслеживание конкурентов.',
        'content' => '<p>Helium 10 — комплексная платформа для Amazon-продавцов. Более 30 инструментов для поиска товаров, оптимизации листингов, управления рекламой PPC, анализа конкурентов и отслеживания трендов.</p>',
        'website_url' => 'https://helium10.com/',
        'price_summary' => 'От $29/мес',
        'target_markets' => ['США', 'Европа'],
        'is_featured' => false,
        'is_pinned' => false,
        'pricing' => [
            ['plan_name' => 'Starter', 'plan_price' => '$29/мес', 'plan_note' => 'Базовые инструменты'],
            ['plan_name' => 'Platinum', 'plan_price' => '$79/мес', 'plan_note' => 'Полный набор'],
            ['plan_name' => 'Diamond', 'plan_price' => '$229/мес', 'plan_note' => 'Для агентств'],
        ],
        'key_features' => ['Поиск товаров', 'Keyword Research', 'Оптимизация листингов', 'PPC-управление', 'Прибыльность', 'Защита бренда'],
        'advantages' => ['30+ инструментов', 'Данные в реальном времени', 'Обучающая платформа', 'Chrome-расширение'],
        'business_areas' => ['Amazon FBA', 'E-commerce', 'Private Label'],
    ],
    [
        'title' => 'Битрикс24',
        'slug' => 'bitrix24',
        'category' => 'crm',
        'short_description' => 'Корпоративный портал и CRM. Задачи, проекты, коммуникации, документы и HR в одной системе.',
        'content' => '<p>Битрикс24 — крупнейшая платформа для управления бизнесом в СНГ. Объединяет CRM, управление проектами и задачами, корпоративную социальную сеть, видеозвонки, облачное хранилище и HR-модуль.</p>',
        'website_url' => 'https://bitrix24.ru/',
        'price_summary' => 'Бесплатно / от ₽1990/мес',
        'target_markets' => ['Россия', 'СНГ'],
        'is_featured' => true,
        'is_pinned' => true,
        'pricing' => [
            ['plan_name' => 'Free', 'plan_price' => 'Бесплатно', 'plan_note' => 'Безлимит пользователей'],
            ['plan_name' => 'Базовый', 'plan_price' => '₽1990/мес', 'plan_note' => '5 пользователей'],
            ['plan_name' => 'Стандартный', 'plan_price' => '₽5590/мес', 'plan_note' => '50 пользователей'],
            ['plan_name' => 'Профессиональный', 'plan_price' => '₽11190/мес', 'plan_note' => '100 пользователей'],
        ],
        'key_features' => ['CRM', 'Задачи и проекты', 'Видеозвонки', 'Облачное хранилище', 'Корпортал', 'HR-модуль', 'Конструктор сайтов'],
        'advantages' => ['Бесплатный тариф без ограничений', 'Всё в одном', 'Русскоязычная поддержка', 'On-premise версия'],
        'business_areas' => ['Любой бизнес', 'Госсектор', 'Образование', 'Производство', 'Торговля'],
    ],
    [
        'title' => 'amoCRM',
        'slug' => 'amocrm',
        'category' => 'crm',
        'short_description' => 'CRM для отделов продаж. Простая воронка, автоматизация и интеграция с мессенджерами.',
        'content' => '<p>amoCRM — популярная CRM-система, созданная специально для отделов продаж. Простой и понятный интерфейс, мощная автоматизация через Digital Pipeline, интеграция с WhatsApp, Telegram, Instagram и другими каналами коммуникации.</p>',
        'website_url' => 'https://amocrm.ru/',
        'price_summary' => 'От ₽499/мес',
        'target_markets' => ['Россия', 'СНГ'],
        'is_featured' => true,
        'is_pinned' => true,
        'pricing' => [
            ['plan_name' => 'Базовый', 'plan_price' => '₽499/мес', 'plan_note' => 'За пользователя'],
            ['plan_name' => 'Расширенный', 'plan_price' => '₽999/мес', 'plan_note' => 'Автоматизация'],
            ['plan_name' => 'Профессиональный', 'plan_price' => '₽1499/мес', 'plan_note' => 'Все функции'],
        ],
        'key_features' => ['Digital Pipeline', 'Интеграция с мессенджерами', 'Сканер визиток', 'Аналитика продаж', 'SalesBot', 'API'],
        'advantages' => ['Интуитивный интерфейс', 'Digital Pipeline', '500+ интеграций', 'Мобильное приложение'],
        'business_areas' => ['Продажи B2B', 'Недвижимость', 'Образование', 'Медицина', 'Услуги'],
    ],
];

echo "=== Creating Software Products ===\n";
foreach ($products as $p) {
    // Check if already exists
    $existing = get_page_by_path($p['slug'], OBJECT, 'software');
    if ($existing) {
        echo "SKIP: {$p['title']} (already exists)\n";
        continue;
    }

    $post_id = wp_insert_post([
        'post_title' => $p['title'],
        'post_name' => $p['slug'],
        'post_content' => $p['content'],
        'post_status' => 'publish',
        'post_type' => 'software',
    ]);

    if (is_wp_error($post_id)) {
        echo "ERROR: {$p['title']} — {$post_id->get_error_message()}\n";
        continue;
    }

    // Category
    $term = get_term_by('slug', $p['category'], 'software_category');
    if ($term) {
        wp_set_object_terms($post_id, $term->term_id, 'software_category');
    }

    // ACF Fields
    update_field('short_description', $p['short_description'], $post_id);
    update_field('website_url', $p['website_url'], $post_id);
    update_field('price_summary', $p['price_summary'], $post_id);
    update_field('target_markets', $p['target_markets'], $post_id);
    update_field('is_featured', $p['is_featured'], $post_id);
    update_field('is_pinned', $p['is_pinned'], $post_id);
    update_field('pricing', $p['pricing'], $post_id);

    // Repeater fields
    $features = [];
    foreach ($p['key_features'] as $f) {
        $features[] = ['feature_text' => $f];
    }
    update_field('key_features', $features, $post_id);

    $advs = [];
    foreach ($p['advantages'] as $a) {
        $advs[] = ['advantage_text' => $a];
    }
    update_field('advantages', $advs, $post_id);

    $areas = [];
    foreach ($p['business_areas'] as $ba) {
        $areas[] = ['area_text' => $ba];
    }
    update_field('business_areas', $areas, $post_id);

    echo "OK: {$p['title']} (ID: {$post_id})\n";
}

// ========== Blog Posts ==========
echo "\n=== Creating Blog Posts ===\n";
$blog_posts = [
    [
        'title' => 'Введение в мир CRM: зачем это нужно вашему бизнесу',
        'slug' => 'crm-introduction',
        'content' => '<p>CRM-система (Customer Relationship Management) — это не просто программа для хранения контактов. Это стратегический инструмент, который позволяет бизнесу выстраивать долгосрочные отношения с клиентами.</p>

<h2>Что даёт CRM?</h2>
<p>Современная CRM-система автоматизирует ключевые процессы: от первого контакта с потенциальным клиентом до повторных продаж. Вот основные преимущества:</p>
<ul>
<li><strong>Единая база клиентов</strong> — вся история взаимодействий в одном месте</li>
<li><strong>Автоматизация продаж</strong> — воронки, напоминания, автоматические email</li>
<li><strong>Аналитика</strong> — понимание, какие каналы приносят больше прибыли</li>
<li><strong>Командная работа</strong> — прозрачное распределение задач и контроль</li>
</ul>

<h2>Кому нужна CRM?</h2>
<p>CRM нужна любому бизнесу, у которого есть клиенты. Будь то интернет-магазин, сервисный центр, консалтинговая компания или строительная фирма — правильно внедрённая CRM увеличит продажи на 25-40%.</p>',
        'excerpt' => 'Разбираемся, зачем бизнесу нужна CRM-система и как она помогает увеличить продажи на 25-40%.',
    ],
    [
        'title' => 'Топ-5 трендов автоматизации бизнеса в 2025 году',
        'slug' => 'business-automation-trends-2025',
        'content' => '<p>Автоматизация бизнес-процессов перестала быть привилегией крупных корпораций. Облачные сервисы сделали мощные инструменты доступными для компаний любого размера.</p>

<h2>1. AI-ассистенты в CRM</h2>
<p>Искусственный интеллект помогает менеджерам — подсказывает лучшее время для звонка, анализирует тональность переписки и прогнозирует вероятность сделки.</p>

<h2>2. No-code автоматизация</h2>
<p>Платформы вроде Monday.com и Zapier позволяют создавать сложные автоматизации без единой строки кода.</p>

<h2>3. Мессенджер-коммерция</h2>
<p>WhatsApp, Telegram и Instagram стали полноценными каналами продаж. CRM-системы интегрируются с ними для мгновенной обработки заказов.</p>

<h2>4. Гиперперсонализация</h2>
<p>Данные из CRM используются для персонализации каждого взаимодействия — от email-рассылок до рекомендаций на сайте.</p>

<h2>5. Экосистемы вместо отдельных инструментов</h2>
<p>Бизнес всё чаще выбирает платформы «всё в одном», такие как Битрикс24, вместо набора разрозненных сервисов.</p>',
        'excerpt' => 'AI-ассистенты, no-code, мессенджер-коммерция — главные тренды автоматизации бизнеса на 2025 год.',
    ],
    [
        'title' => 'Облачные решения vs локальное ПО: что выбрать?',
        'slug' => 'cloud-vs-onpremise',
        'content' => '<p>Выбор между облачным и локальным ПО — одно из ключевых решений для бизнеса. Каждый вариант имеет свои сильные стороны.</p>

<h2>Облачные решения (SaaS)</h2>
<p>Плюсы: быстрый старт, не нужен свой сервер, автоматические обновления, доступ из любой точки мира, предсказуемая стоимость (подписка).</p>
<p>Примеры: amoCRM, Monday.com, KeyCRM, NetHunt CRM.</p>

<h2>Локальное ПО (On-premise)</h2>
<p>Плюсы: полный контроль над данными, работа без интернета, одноразовая оплата, гибкая кастомизация.</p>
<p>Примеры: Битрикс24 (коробочная версия), 1С, SAP.</p>

<h2>Наша рекомендация</h2>
<p>Для большинства малых и средних компаний оптимальным выбором будет облачное решение. Оно позволяет быстро начать работу без капитальных вложений. Локальное ПО подходит для организаций со строгими требованиями к безопасности данных (банки, госструктуры).</p>',
        'excerpt' => 'Сравниваем облачные SaaS-решения и локальное ПО — что лучше подходит для вашего бизнеса.',
    ],
];

foreach ($blog_posts as $bp) {
    $existing = get_page_by_path($bp['slug'], OBJECT, 'post');
    if ($existing) {
        echo "SKIP: {$bp['title']}\n";
        continue;
    }

    $pid = wp_insert_post([
        'post_title' => $bp['title'],
        'post_name' => $bp['slug'],
        'post_content' => $bp['content'],
        'post_excerpt' => $bp['excerpt'],
        'post_status' => 'publish',
        'post_type' => 'post',
    ]);
    echo($pid && !is_wp_error($pid)) ? "OK: {$bp['title']} (ID: {$pid})\n" : "ERROR: {$bp['title']}\n";
}

// ========== Static Pages ==========
echo "\n=== Creating Static Pages ===\n";
$pages = [
    [
        'title' => 'О нас',
        'slug' => 'about',
        'content' => '<h2>Компания IntegroSoft</h2>
<p>IntegroSoft — сертифицированный партнёр ведущих вендоров бизнес-ПО. Мы помогаем компаниям выбрать, внедрить и настроить оптимальные IT-решения для роста бизнеса.</p>

<h2>Наша миссия</h2>
<p>Делать мир бизнес-технологий доступным и понятным. Мы верим, что правильно подобранное ПО способно кратно увеличить эффективность любой компании.</p>

<h2>Что мы делаем</h2>
<ul>
<li>Подбираем CRM, ERP и другие системы под задачи бизнеса</li>
<li>Внедряем и настраиваем выбранные решения</li>
<li>Обучаем сотрудников работе с новым ПО</li>
<li>Обеспечиваем техническую поддержку</li>
</ul>

<h2>В цифрах</h2>
<ul>
<li><strong>100+</strong> успешных внедрений</li>
<li><strong>50+</strong> партнёров-вендоров</li>
<li><strong>5 лет</strong> на рынке</li>
<li><strong>97%</strong> клиентов довольны</li>
</ul>',
    ],
    [
        'title' => 'Контакты',
        'slug' => 'contacts',
        'content' => '<h2>Свяжитесь с нами</h2>
<p>Мы всегда рады ответить на ваши вопросы и помочь с подбором ПО.</p>

<p><strong>📧 Email:</strong> info@integrosoft.com</p>
<p><strong>📞 Телефон:</strong> +7 (495) 123-45-67</p>
<p><strong>📍 Адрес:</strong> г. Москва, ул. Программистов, д. 42, офис 301</p>
<p><strong>🕐 Режим работы:</strong> Пн–Пт, 9:00–18:00</p>

<h2>Оставьте заявку</h2>
<p>Заполните форму ниже, и наш менеджер свяжется с вами в течение 1 рабочего дня.</p>',
    ],
    [
        'title' => 'Политика конфиденциальности',
        'slug' => 'privacy-policy',
        'content' => '<h2>Политика конфиденциальности</h2>
<p>Настоящая Политика описывает порядок сбора, использования и защиты персональных данных пользователей сайта IntegroSoft.</p>

<h2>Сбор информации</h2>
<p>Мы собираем информацию, которую вы добровольно предоставляете при заполнении форм: имя, email, телефон.</p>

<h2>Использование данных</h2>
<p>Персональные данные используются исключительно для обработки заявок и информирования о наших услугах.</p>

<h2>Защита данных</h2>
<p>Мы принимаем все необходимые меры для защиты ваших данных от несанкционированного доступа.</p>',
    ],
    [
        'title' => 'Условия использования',
        'slug' => 'terms',
        'content' => '<h2>Условия использования</h2>
<p>Используя данный сайт, вы соглашаетесь с настоящими условиями.</p>

<h2>Интеллектуальная собственность</h2>
<p>Все материалы сайта являются собственностью IntegroSoft и защищены законодательством об авторском праве.</p>

<h2>Ограничение ответственности</h2>
<p>Информация на сайте предоставляется «как есть». IntegroSoft не несёт ответственности за решения, принятые на основании информации с сайта.</p>

<h2>Изменение условий</h2>
<p>IntegroSoft оставляет за собой право изменять настоящие условия без предварительного уведомления.</p>',
    ],
];

foreach ($pages as $pg) {
    $existing = get_page_by_path($pg['slug']);
    if ($existing) {
        echo "SKIP: {$pg['title']}\n";
        continue;
    }

    $pid = wp_insert_post([
        'post_title' => $pg['title'],
        'post_name' => $pg['slug'],
        'post_content' => $pg['content'],
        'post_status' => 'publish',
        'post_type' => 'page',
    ]);
    echo($pid && !is_wp_error($pid)) ? "OK: {$pg['title']} (ID: {$pid})\n" : "ERROR: {$pg['title']}\n";
}

// ========== Set Homepage ==========
echo "\n=== Configuring Homepage ===\n";
// Use the blog-style index.php as front page
update_option('show_on_front', 'posts');
echo "Homepage set to latest posts (index.php template)\n";

echo "\n=== DONE ===\n";
