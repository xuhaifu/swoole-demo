<?php

$redisClient = new swoole_redis();
$redisClient->connect('127.0.0.1', 6379, function (swoole_redis $redis, $result) {
    echo "connect : {$result} \n";
    //    $redis->set('name1', 'ronaldo', function (swoole_redis $redis, $result) {
    //        echo "set : {$result} \n";
    //        $redis->close();
    //    });
    $redis->keys('*', function (swoole_redis $redis, $result) {
        var_dump($result);
        $redis->close();
    });
});

echo 'start' . PHP_EOL;