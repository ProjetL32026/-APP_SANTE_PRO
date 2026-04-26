</div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/js/jquery.min.js"></script>

<?php if (isset($pageScript)): ?>
    <script src="<?= BASE_URL . '/public/' . $pageScript ?>"></script>
<?php endif; ?>

</body>
</html>