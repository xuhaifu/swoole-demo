<?php



/*******************调度支持 Stream 模式************************/
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'dispatch_mode' => 7,
    'worker_num' => 2,
));

$serv->on('receive', function (swoole_server $serv, $fd, $threadId, $data)
{
    var_dump($data);
    echo "#{$serv->worker_id}>> received length=" . strlen($data) . "\n";
});

$serv->start();

exit;
/***********进程池模块的使用--任务投递(TCP端口消息队列)************************/

$pool = new Swoole\Process\Pool(2, SWOOLE_IPC_SOCKET);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
});

$pool->on("Message", function ($pool, $message) {
    echo "Message: {$message}\n";
});

$pool->listen('127.0.0.1', 8089);

$pool->start();


/*******************进程池模块的使用--任务投递(消息队列)************************/
$pool = new Swoole\Process\Pool(2, SWOOLE_IPC_MSGQUEUE, 0x7000001);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
});

$pool->on("Message", function ($pool, $message) {
    echo "Message: {$message}\n";
});

$pool->start();

/*******************进程池模块的使用--信号处理************************/
$workerNum = 5;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    $running = true;
    pcntl_signal(SIGTERM, function () use (&$running) {
        $running = false;
    });
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "runoobkey";
    while ($running) {
        $msgs = $redis->brpop($key, 2);
        pcntl_signal_dispatch();
        if ( $msgs == null) continue;
        var_dump($msgs);
    }
});

/*******************进程池模块的使用--创建进程池************************/
$workerNum = 5;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "runoobkey";
    while (true) {
        $msgs = $redis->brpop($key, 2);
        if ( $msgs == null) continue;
        var_dump($msgs);
    }
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();

/*******************mt_rand随机数必须在每个进程中调用。父进程调用。每个进程的值会相同************************/
//开始
$worker_num = 16;
// fork 进程
for($i = 0; $i < $worker_num; $i++) {
    $process = new swoole_process('child_async', false, 2);
    $pid = $process -> start();
}
//异步执行进程
function child_async(swoole_process $worker) {
    mt_srand();
    echo mt_rand(0, 100).PHP_EOL;
    $worker->exit();
}

/*******************异步服务不能使用死循环(客户端连接不上)************************************************/
$serv = new swoole_server("127.0.0.1", 9501);
$serv->set(['worker_num' => 1]);
$serv->on('receive', function ($serv, $fd, $reactorId, $data) {
    $i= 0;
    while(1){
        $i++;
    }
    $serv->send($fd, 'Swoole: '.$data);
});
$serv->start();


$fds = array();
$server->on('connect', function ($server, $fd){
    echo "connection open: {$fd}\n";
    global $fds;
    $fds[] = $fd;
    var_dump($fds);
});


/*******************固定包头+包体协议************************************************/
$server->set(array(
    'open_length_check' => true,
    'package_max_length' => 81920,
    'package_length_type' => 'n', //see php pack()
    'package_length_offset' => 0,
    'package_body_offset' => 2,
));


/*******************EOF结束符协议************************************************/
//在swoole_server和swoole_client的代码中只需要设置2个参数就可以使用EOF协议处理。
$server->set(array(
    'open_eof_split' => true,
    'package_eof' => "\r\n",
));
$client->set(array(
    'open_eof_split' => true,
    'package_eof' => "\r\n",
));


/*******************设置定时器************************************************/
//每隔2000ms触发一次
swoole_timer_tick(2000, function ($timer_id) {
    echo "tick-2000ms\n";
});

//3000ms后执行此函数
swoole_timer_after(3000, function () {
    echo "after 3000ms.\n";
});
