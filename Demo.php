<?php
/**
 * User: sethink
 */

namespace Demo;

include_once "./src/Db.php";
include_once "./src/db/Query.php";
include_once "./src/db/Builder.php";
include_once "./src/MysqlPool.php";

use sethink\swooleOrm\Db;
use sethink\swooleOrm\MysqlPool;
use swoole;

class Demo
{
    protected $server;

    protected $MysqlPool;

    public function __construct()
    {

        $this->server = new Swoole\Http\Server("0.0.0.0", 9501);
        $this->server->set(array(
            'worker_num'    => 4,
            'max_request'   => 50000,
            'reload_async'  => true,
            'max_wait_time' => 30,
        ));

        $this->server->on('Start', function ($server) {});
        $this->server->on('ManagerStart', function ($server) {});
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('WorkerStop', function ($server, $worker_id) {});
        $this->server->on('open', function ($server, $request) {});
        $this->server->on('Request', array($this, 'onRequest'));
        $this->server->start();
    }

    public function onWorkerStart($server, $worker_id)
    {
        $config    = [
            'host'      => '127.0.0.1',
            'port'      => 3306,
            'user'      => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'database'  => 'test',
            'prefix'    => 'sethink_',
            'poolMin'   => 5,
            'clearTime' => 60000,
            'clearAll'  => 300000,
        ];
        $this->MysqlPool = new MysqlPool($config);
        unset($config);
        $this->MysqlPool->clearTimer($server);
    }

    public function onRequest($request, $response)
    {

        $rs = Db::init($this->MysqlPool)
            ->name('test')
            ->select();
        var_dump($rs);
    }
}


new Demo();