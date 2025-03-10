<?php
session_start();

if (isset($_POST['locale'])) {
    $locale = $_POST['locale'];
    $_SESSION['locale'] = $locale;
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>