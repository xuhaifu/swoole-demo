<?php




$filename = __DIR__ . '/test.txt';
// 异步读取文件
$result = swoole_async_readfile($filename, function ($filename, $content) {
    echo "filename : {$filename}" . PHP_EOL;
    echo "content : {$content}" . PHP_EOL;
});

echo 'start'.PHP_EOL;
var_dump($result);

exit;

// 异步写文件
$content = date('Y-m-d H:i:s') . PHP_EOL;

Swoole\Async::writeFile($filename, $content, function ($filename) {
    echo "write {$filename} success" . PHP_EOL;
}, FILE_APPEND);

echo 'start' . PHP_EOL;