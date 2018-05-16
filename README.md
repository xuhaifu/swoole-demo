官网;https://www.swoole.com/

官方中文文档：https://wiki.swoole.com/wiki/page/1.html

**一.服务端连接：**

1. tcp连接
```
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501);
//var_dump($serv);
//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});
//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});
//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
//启动服务器
$serv->start();
```
2. udp连接
 ```
 //创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
 $serv = new swoole_server("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
 
 //监听数据接收事件
 $serv->on('Packet', function ($serv, $data, $clientInfo) {
     $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
     var_dump($clientInfo);
 });
 
 //启动服务器
 $serv->start();
 ```
3. http连接
```
$http = new swoole_http_server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
    var_dump($request->get, $request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();
```
4. websocket连接
```
//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    echo "Message: {$frame->data}\n";
    $ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();

```

**二.参数设置**
```
$serv = new swoole_server('0.0.0.0',9503);
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
```

**其他**

###### phpstorm自动提示工具[swoole-ide-helper](https://github.com/eaglewu/swoole-ide-helper.git)
>gitHub：https://github.com/eaglewu/swoole-ide-helper.git


