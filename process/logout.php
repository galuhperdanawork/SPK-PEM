<?php
session_start();
session_unset();
session_destroy();
header('Location: /SPK-PEM/login.php');
exit;