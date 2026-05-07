        </main>
    </div>
    <script src="assets/js/app.js"></script>
    <?php if (!empty($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $pageScript): ?>
            <script src="<?php echo htmlspecialchars($pageScript, ENT_QUOTES, 'UTF-8'); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>
