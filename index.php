<?php
/**
 * Created by index.php
 * Author: XHF
 * Date: 2018/5/16
 * Time: 10:26
 */

//构建一个server对象
$serv = new swoole_server('0.0.0.0',9501);
$serv->set(array(
    //'max_conn' => 10000,              //最大连接
    'daemonize ' => 1,                  //守护进程化
    //'reactor_num' => 2,               //reactor线程数,通过此参数来调节poll线程的数量，以充分利用多核
    'worker_num' => 4,                  //worker进程数
    //'max_request' => 2000,            //此参数表示worker进程在处理完n次请求后结束运行
    //'backlog' => 128,                 //Listen队列长度
    //'open_cpu_affinity' => 1,         //启用CPU亲和设置
    //'open_tcp_nodelay' => 1,          //启用tcp_nodelay
    //'tcp_defer_accept' => 5,          //此参数设定一个秒数，当客户端连接连接到服务器时，在约定秒数内并不会触发accept，直到有数据发送，或者超时时才会触发。
    'log_file' => 'log/swoole.log',     //指定swoole错误日志文件
    //'open_eof_check' => true,         //数据buffer-打开buffer
    //'package_eof' => "\r\n\r\n"       //数据buffer-设置EOF
    //'heartbeat_check_interval' => 30, //心跳检测机制-每隔多少秒检测一次，单位秒,Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭掉
    //'heartbeat_idle_time' => 60,      //心跳检测机制-TCP连接的最大闲置时间，单位s , 如果某fd最后一次发包距离现在的时间超过heartbeat_idle_time会把这个连接关闭。
    //'dispatch_mode' => 1              //worker进程数据包分配模式   1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
));

$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, $data);
    /*$serv->sendfile($fd, 'data/file');    //发送文件内容。文件路径
    $serv->tick(1000, function() use ($serv, $fd) {
        echo '服务执行完成后，定时执行';
    });
    $serv->after(1000,function(){
        echo '服务执行完成后只执行一次，就销毁';
    });*/
    $serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->start();


//var_dump($serv);




exit;

require 'redis-async/src/Swoole/Async/RedisClient.php';
$redis = new Swoole\Async\RedisClient('127.0.0.1');

$redis->select('2', function () use ($redis) {
    $redis->set('key', 'value-rango', function () use ($redis) {
        for ($i = 0; $i < 3; $i++) {
            $redis->get('key', function ($result, $success) {
                echo "redis ok:\n";
                var_dump($success, $result);
            });
        }
    });
});