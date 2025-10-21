<?php
echo "✅ PHP يعمل بشكل صحيح<br>";
echo "✅ الملفات الجديدة متاحة<br>";
echo "✅ الوقت الحالي: " . date('Y-m-d H:i:s') . "<br>";
echo "✅ المسار: " . __DIR__ . "<br>";
echo "✅ الملفات الموجودة:<br>";

$files = scandir(__DIR__);
foreach($files as $file) {
    if(strpos($file, '.php') !== false) {
        echo "- " . $file . "<br>";
    }
}
?>
