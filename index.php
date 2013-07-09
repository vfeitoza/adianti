<?php
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
if (PHP_SAPI == 'cli')
{
    include 'index.gtk.php';
}
else
{
    include 'index.web.php';
}
?>