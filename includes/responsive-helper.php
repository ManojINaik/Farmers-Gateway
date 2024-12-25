<?php
if (!function_exists('addResponsiveCSS')) {
    function addResponsiveCSS() {
        $cssPath = '/agriculture-portal/assets/css/responsive-fix.css';
        echo '<link rel="stylesheet" href="' . $cssPath . '?v=' . filemtime($_SERVER['DOCUMENT_ROOT'] . $cssPath) . '">';
    }
}
?>
