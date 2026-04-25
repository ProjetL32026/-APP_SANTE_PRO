<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($pageScript)): ?>
    <?php foreach (explode(',', $pageScript) as $s): ?>
        <script src="<?= BASE_URL ?>/public/js/<?= trim($s) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>