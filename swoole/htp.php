<?php
//
//use think\Container;
//
//$http = new Swoole\Http\Server("0.0.0.0", 9501);
//
//$http->set(array(
//    'document_root' => '/www/tp5/public',
//    'enable_static_handler' => true,
//    "static_handler_locations" => ['/static'],
//    'worker_num' => 4,
//));
//
//$http->on('WorkerStart', function ($serv, $worker_id){
//    require __DIR__ . '/../thinkphp/base.php';
//});
//
//$http->on('request', function ($request, $response) {
//    ob_start();
//    Container::get('app')->run()->send();
//    $content = ob_get_contents();
//    ob_start();
//    $response->end($content);
//});
//$http->start();


class Htp {
    CONST HOST = "0.0.0.0";
    CONST PORT = 9501;

    public $http = null;
    public function __construct() {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set(
            [
                'document_root' => '/www/tp5/public', // v4.4.0以下版本, 此处必须为绝对路径
                'enable_static_handler' => true,
                "static_handler_locations" => ['/static'],
                'worker_num' => 4,
                'task_worker_num' => 4,
                'task_enable_coroutine'=> true
            ]
        );

        $this->http->on("workerstart", [$this, 'onWorkerStart']);
        $this->http->on("request", [$this, 'onRequest']);
        $this->http->on("task", [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on("close", [$this, 'onClose']);

        $this->http->start();
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server,  $worker_id) {
        require __DIR__ . '/../thinkphp/base.php';
    }

    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response) {
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
        }
//        var_dump($request);
        $request->server['argv'] = $_SERVER['argv'];
        $request->server['argc'] = $_SERVER['argc'];
        $_SERVER  =  [];
        if(isset($request->server)) {
            foreach($request->server as $k => $v) {
                $_SERVER[ $k=='argv'|| $k=='argc' ? $k : strtoupper($k) ] = $v;
            }
        }
        if(isset($request->header)) {
            foreach($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        $_GET = [];
        if(isset($request->get)) {
            foreach($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_POST = [];
        if(isset($request->post)) {
            foreach($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

//        return $this->http->task('2');



        $_REQUEST['http_server'] = $this->http;

        ob_start();
        // 执行应用并响应
        try {
            \think\Container::get('app')->run()->send();
        }catch (\Exception $e) {
            // todo
        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, Swoole\Server\Task $task) {
//        go(function(){
//        $db = new Swoole\Coroutine\MySQL();
//        $db->connect([
//            'host' => '127.0.0.1',
//            'port' => 3306,
//            'user' => 'root',
//            'password' => '',
//            'database' => 'swoole',
//        ]);
//        $res = $db->query("select * from stest limit 1");
//        var_dump($res);
////        });
//
//        //完成任务，结束并返回数据
//        $task->finish(123);

        // 分发 task 任务机制，让不同的任务 走不同的逻辑
//        $obj = new app\common\lib\task\Task;
//
//        $method = $data['method'];
//        $flag = $obj->$method($data['data']);
        /*$obj = new app\common\lib\ali\Sms();
        try {
            $response = $obj::sendSms($data['phone'], $data['code']);
        }catch (\Exception $e) {
            // todo
            echo $e->getMessage();
        }*/

        return 1; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {
        echo "clientid:{$fd}\n";
    }
}

new Htp();