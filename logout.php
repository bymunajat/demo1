<?php
session_start();
session_destroy();

// Setelah logout → ke index.php
header("Location: index.php");
exit;
