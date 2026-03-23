# SoftZor Project Reference Guide (March 2026)

## Overview
SoftZor Matchmaker — это интеллектуальная B2B-экосистема для подбора ПО. Переход от пассивного каталога к активному ИИ-консультанту.

## Основной стек технологий
- **Backend:** WordPress, Custom Post Types (`software`, `integrator`, `sw_attribute`).
- **Данные:** ACF для метаданных (JSON), кастомные SQL-таблицы (`wp_softzor_external_scout`, `wp_softzor_intent_logs`).
- **AI Core:** Gemini 1.5 Flash (классификация и скаут), GPT/Claude для глубокого анализа.
- **Frontend:** Vanilla JS для динамического квиза (`quiz.js`), PHP-шаблоны WordPress для архивов и отдельных страниц.

## Ключевые логические потоки
1. **2-этапный ИИ-квиз:**
   - Этап 1: Свободный текст (намерение) -> Gemini классифицирует в `software_category`.
   - Этап 2: Динамические вопросы из поля ACF `quiz_questions` соответствующей категории.
   - Этап 3: Поиск в локальной БД -> если 0 результатов -> Режим AI Scout (поиск в сети).
2. **Режим AI Scout:**
   - Использует Search API (Tavily/Firecrawl) для поиска ПО в интернете.
   - Наполняет таблицу `wp_softzor_external_scout`.
   - Создает "активные черновики" для потенциальных партнеров.
3. **Геополитический фильтр:**
   - Жесткая блокировка ПО из РФ и РБ.
   - Приоритет ПО из Украины, ЕС и США.

## Схема базы данных (Кастомные таблицы)
- `wp_softzor_external_scout`: `id`, `software_name`, `website_url`, `category_slug`, `hit_count`, `last_query`, `ai_summary` (JSON), `status`.
- `wp_softzor_intent_logs`: `id`, `session_id`, `user_intent`, `offered_partners`, `selected_external_id`, `is_expert_mode`, `timestamp`.

## Важные файлы
- `functions.php`: Логика темы и подключение компонентов.
- `inc/db-tables.php`: Миграции БД.
- `inc/quiz-functions.php`: Логика квиза на бэкенде и интеграция с Gemini.
- `inc/quiz-rest-api.php`: REST-эндпоинты для квиза.
- `inc/quiz-frontend.php`: Код для рендеринга фронтенда квиза.
