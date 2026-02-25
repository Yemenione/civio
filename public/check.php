<?php
echo "<h1>Server Environment Check</h1>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";

$extensions = ['pdo_mysql', 'bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'filter', 'hash', 'iconv', 'json', 'libxml', 'mbstring', 'openssl', 'pcre', 'pdo', 'phar', 'reflection', 'session', 'simplexml', 'spl', 'standard', 'tokenizer', 'xml', 'xmlreader', 'xmlwriter', 'zlib', 'intl'];

echo "<h2>Required Extensions:</h2><ul>";
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li style='color:green;'>[OK] $ext</li>";
    } else {
        echo "<li style='color:red;'>[MISSING] $ext</li>";
    }
}
echo "</ul>";

echo "<h2>Directory Permissions:</h2><ul>";
$paths = ['../storage', '../bootstrap/cache'];
foreach ($paths as $path) {
    if (is_writable($path)) {
        echo "<li style='color:green;'>[WRITABLE] $path</li>";
    } else {
        echo "<li style='color:red;'>[NOT WRITABLE] $path - Please set permissions to 775</li>";
    }
}
echo "</ul>";
