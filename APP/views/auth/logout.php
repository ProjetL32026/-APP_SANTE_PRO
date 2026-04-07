<?php
session_start();
session_unset();
session_destroy();

// On redirige vers le point d'entrée du projet
// Le chemin doit inclure 'sante_pro' si tu es sur localhost
header("Location: /sante_pro/public/index.php");
exit();