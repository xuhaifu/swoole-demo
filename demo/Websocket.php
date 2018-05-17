<?php
/**
 * Created by Websocket.php
 * Author: XHF
 * Date: 2018/5/17
 * Time: 11:20
 */

class Websocket {

    const HOST = '0.0.0.0';
    const PORT = 9502;
    private $ws = null;

    public function __construct()
    {
        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);
        $this->ws->set([
            'worker_num' => 2,
            'task_worker_num' => 2, // 要想使用task必须要指明
        ]);
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on('finish', [$this, 'onFinish']);
        $this->ws->on('close', [$this, 'onClose']);
        $this->ws->start();
    }

    public function onOpen($server, $request)
    {
        echo "server：handshake success with fd：{$request->fd}\n";
    }

    public function onMessage($server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data}\n";

        // 需要投递的任务数据
        $data = [
            'fd' => $frame->fd,
            'msg' => 'task',
        ];
        $server->task($data);

        $server->push($frame->fd, 'this is server');
    }

    // 处理投递的任务方法，非阻塞
    public function onTask($server, $task_id, $worker_id, $data)
    {
        print_r($data);
        // 模拟大量数据的操作
        sleep(10);
        return "task_finish";
    }

    // 投递任务处理完毕调用的方法
    public function onFinish($server, $task_id, $data)
    {
        echo "task_id:{$task_id}\n";
        echo "task finish success:{$data}\n";
    }

    public function onClose($server, $fd)
    {
        echo "Client：close";
    }
}