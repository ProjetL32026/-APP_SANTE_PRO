</div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_GET['action'])): ?>
    <?php if ($_GET['action'] === 'consulter'): ?>
        <script src="jsbaya/consultation.js"></script>
    <?php elseif ($_GET['action'] === 'historique'): ?>
        <script src="jsbaya/historique.js"></script>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>