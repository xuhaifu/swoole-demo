<?php
/**
 * Created by mysql.php
 * Author: XHF
 * Date: 2018/5/17
 * Time: 14:14
 */

class AsyncMysql {
    private $dbSource = null;
    private $dbConfig = null;
    public function __construct()
    {
        $this->dbSource = new swoole_mysql();
        $this->dbConfig = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'xhf',
            'password' => '123456',
            'database' => 'test',
            'charset' => 'utf8', //指定字符集
            'timeout' => 2,  // 可选：连接超时时间（非查询超时时间），默认为SW_MYSQL_CONNECT_TIMEOUT（1.0）
        ];
    }

    public function execute($id, $name)
    {
        // 连接mysql
        $this->dbSource->connect($this->dbConfig, function ($db, $result) use ($id, $name) {
            if ($result === false) {
                var_dump($db->error, $db->errno);
                die;
            }
            $sql = "UPDATE `people` SET `firstname` = '{$name}' WHERE `peopleid` = {$id}";
            echo $sql.PHP_EOL;
            $db->query($sql, function ($db, $result) {
                if ($result === false) {
                    var_dump($db->connect_error);
                } elseif ($result === true) { // sql为非查询语句
                    echo "执行非查询语句\n";
                    var_dump($db->affected_rows);
                } else { // sql为查询语句
                    echo "执行查询语句\n";
                    print_r($result);
                }
                $db->close();
            });
        });
    }

}

$obj = new AsyncMysql();
$obj->execute(1, 'ronaldo7');
echo "start\n";