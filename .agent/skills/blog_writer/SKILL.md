---
name: Blog Post Writer
description: Writes high-quality, SEO-optimized blog posts for SoftMir (software reviews and category comparisons).
---

# Blog Post Writer Skill

This skill helps you write blog posts for the SoftMir project. It covers two main types of articles: **Software Reviews** and **Category Overviews**.

## 1. Preparation

Before writing, identify the type of article:
*   **Software Review**: focuses on a single software product (e.g., "RemOnline Review").
*   **Category Overview**: compares multiple products in a category (e.g., "Top 10 CRM Systems").

## 2. Information Gathering

*   **For Software Reviews**:
    *   Find the software in the database or codebase (check `seed-content.php` or `wp_posts` if possible).
    *   Identify: Key features, Pricing, Pros/Cons, Target Audience.
    *   If data is missing, ask the user or search online (if enabled).

*   **For Category Overviews**:
    *   Identify the top software in the category.
    *   Compare their key features and pricing.
    *   determine the "Best for..." for each (e.g., Best for Small Business, Best for Enterprise).

## 3. Drafting Content

Use the appropriate template to structure the article.

### A. Software Review Structure

(See `templates/software_review.md` for the full structure)

1.  **Title**: Catchy, includes software name and "Review" or "Обзор".
2.  **Introduction**: What is it? Who is it for?
3.  **Key Features**: Bullet points or detailed paragraphs.
4.  **Pros & Cons**: Honest assessment.
5.  **Pricing**: Summary of plans.
6.  **Comparison**: Brief mention of alternatives.
7.  **Conclusion**: Final verdict.

### B. Category Overview Structure

(See `templates/category_overview.md` for the full structure)

1.  **Title**: "Top X [Category] Software" or "Best [Category] Systems".
2.  **Introduction**: Definition of the category and why it matters.
3.  **Selection Criteria**: How we chose the software.
4.  **Top Software List**:
    *   For each software: Mini-review, Key Features, "Best For".
5.  **Comparison Table**: Summary of features/price.
6.  **Conclusion**: Recommendations.

## 4. Writing Guidelines (SoftMir Style)

*   **Language**: Russian.
*   **Tone**: Professional, objective, helpful, expert.
*   **Formatting**:
    *   Use `<h2>` and `<h3>` for hierarchy.
    *   Use `<ul>` and `<ol>` for lists.
    *   Use **bold** for emphasis.
*   **SEO**:
    *   Include keywords naturally.
    *   Write a compelling Meta Description (to be placed at the top or in a separate block).
*   **Internal Linking**: Link to other relevant software reviews or categories on SoftMir.

## 5. Next Steps

After drafting the content:
1.  Ask the user for review.
2.  (Optional) If approved, use a script to publish to WordPress (future implementation).
