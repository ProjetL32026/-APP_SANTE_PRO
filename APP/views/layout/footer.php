</div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php 
    /**
     * Gestion dynamique des scripts JS
     * Si $pageScripts est un tableau, on boucle dessus.
     * Si c'est une simple chaîne, on l'affiche directement.
     */
    if (isset($pageScripts)) {
        // Force la conversion en tableau pour pouvoir utiliser foreach
        $scripts = is_array($pageScripts) ? $pageScripts : [$pageScripts];
        
        foreach ($scripts as $script) {
            // Le chemin 'js/' part de votre dossier 'public' car index.php y est
            echo '<script src="js/' . htmlspecialchars($script) . '"></script>' . PHP_EOL;
        }
    }
    ?>
</body>
</html>
