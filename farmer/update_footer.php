<?php
$directory = __DIR__;
$files = glob($directory . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Remove old footer includes
    $content = preg_replace('/include\s*\(\s*[\'"]footer\.php[\'"]\s*\);/i', '', $content);
    $content = preg_replace('/include\s*\(\s*[\'"]\.\.\/footer\.php[\'"]\s*\);/i', '', $content);
    
    // Add jQuery if not already present
    if (!preg_match('/jquery/i', $content)) {
        $jqueryScript = '
    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
';
        $content = preg_replace('/(<\/head>)/i', $jqueryScript . '$1', $content);
    }
    
    // Replace footer include with modern-footer
    $content = preg_replace('/include\s*\(\s*[\'"]footer\.php[\'"]\s*\);/i', 'include("../modern-footer.php");', $content);
    $content = preg_replace('/include\s*\(\s*[\'"]\.\.\/footer\.php[\'"]\s*\);/i', 'include("../modern-footer.php");', $content);
    
    // Add modern-footer if no footer is present
    if (!preg_match('/modern-footer\.php/i', $content)) {
        $content = preg_replace('/(<\/body>)/i', '    <?php include("../modern-footer.php"); ?>' . "\n" . '$1', $content);
    }
    
    file_put_contents($file, $content);
    echo "Updated: " . basename($file) . "\n";
}
?>
