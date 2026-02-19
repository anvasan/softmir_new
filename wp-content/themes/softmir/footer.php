<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">

            <div class="footer-brand">
                <h3>SoftMir</h3>
                <p>Сертифицированный партнёр ведущих вендоров ПО. Помогаем бизнесу расти, внедряя лучшие IT-решения.</p>
                <div class="footer-social" style="margin-top: 1rem;">
                    <a href="#" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5 0-.28-.03-.56-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 2a2 2 0 110 4 2 2 0 010-4z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Навигация</h4>
                <?php
wp_nav_menu([
    'theme_location' => 'footer',
    'container' => false,
    'fallback_cb' => function () {
        echo '<ul>';
        echo '<li><a href="' . home_url('/') . '">Главная</a></li>';
        echo '<li><a href="' . get_post_type_archive_link('software') . '">Каталог ПО</a></li>';
        echo '<li><a href="' . home_url('/about/') . '">О нас</a></li>';
        echo '<li><a href="' . home_url('/contacts/') . '">Контакты</a></li>';
        echo '</ul>';
    },
]);
?>
            </div>

            <div class="footer-links">
                <h4>Информация</h4>
                <ul>
                    <li><a href="<?php echo home_url('/privacy-policy/'); ?>">Политика конфиденциальности</a></li>
                    <li><a href="<?php echo home_url('/terms/'); ?>">Условия использования</a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> SoftMir — Каталог бизнес-ПО. Все права защищены.</p>
        </div>
    </div>
</footer>

<script>
function softmirToggleAttrs(id, btn) {
    var el = document.getElementById(id);
    if (!el) return;
    var isHidden = el.style.display === '' || el.style.display === 'none';
    el.style.display = isHidden ? 'block' : 'none';
    if (isHidden) {
        btn.textContent = 'Скрыть ▴';
        btn.classList.add('open');
    } else {
        btn.textContent = btn.getAttribute('data-label');
        btn.classList.remove('open');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.attrs-toggle-btn').forEach(function(btn) {
        btn.setAttribute('data-label', btn.textContent);
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
