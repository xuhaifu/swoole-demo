<?php

/*******************Http************************************************/
$cli = new Swoole\Http\Client('127.0.0.1', 80);
$cli->setHeaders(array('User-Agent' => 'swoole-http-client'));
$cli->setCookies(array('test' => 'value'));

//POST
$cli->post('/index.php', array("test" => 'abc'), function ($cli) {
    //服务端返回的内容
    var_dump($cli->body);
});
//GET
$cli->get('/index.php', function ($cli) {
    //状态码
    var_dump($cli->statusCode);
    var_dump($cli->headers);
    var_dump($cli->cookies);
    var_dump($cli->set_cookie_headers);
});


exit;
/*******************Redis************************************************/
$redis = new Swoole\Redis;
$redis->connect('127.0.0.1', 6379, function ($redis, $result) {
    $redis->set('test_key', 'value', function ($redis, $result) {
        $redis->get('test_key', function ($redis, $result) {
            var_dump($result);
            $redis->close();
        });
    });
});


/*******************MYSQL************************************************/
$db = new Swoole\MySQL;
$server = array(
    'host' => '127.0.0.1',
    'user' => 'xhf',
    'password' => '123456',
    'database' => 'test',
);

$db->connect($server, function ($db, $result) {
    $db->query("show tables", function (Swoole\MySQL $db, $result) {
        var_dump($result);
        $db->close();
    });
});





