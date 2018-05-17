<?php
/**
 * 异步抓包，通过客户端发送的连接，返回需要的内容保存数据库
 * Author: XHF
 * Date: 2018/5/17
 * Time: 15:00
 */
class curl
{
    private $host;
    private $port;
    public $server;

    public $redis;

    public function __construct()
    {
        $this->host = '0.0.0.0';
        $this->port = '9501';
        $this->server = new swoole_server($this->host,$this->port);
        $this->redis = new redis();
        $this->server->set([
            'worker_num' => 2,
            'task_worker_num' => 2
        ]);
        $this->server->on('receive',array($this,'onReceive'));
        $this->server->on('task',array($this,'onTask'));
        $this->server->on('finish',array($this,'onFinish'));
        $this->server->start();
    }

    public function onReceive($server,$fd,$reactor_id,$data)
    {
        if($data == 'redis'){
            $this->server->task('redis');
        } else {
            $this->server->send($fd,"hello task process");
        }
    }

    public function onTask($server,$task_id,$src_worker_id,$data)
    {
        if($data == 'redis'){
            $this->redis->connect('127.0.0.1');
            return $xxx = $this->redis->get('xxx');
        }
    }

    public function onFinish($server,$task_id,$data)
    {
        echo $data.PHP_EOL;
    }

    public function onClose($fd)
    {
        $this->server->close($fd);
    }
}

$curl = new curl();
