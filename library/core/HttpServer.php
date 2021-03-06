<?php
/**
 * HttpServer
 */

class HttpServer {

    private $server;

    public function __construct() {
        $config = Config::get();
        
        $ip   = $config['http']['ip'];
        $port = $config['http']['port'];
        $this->server = new swoole_http_server($ip, $port);

        $c = [];
        foreach($config['common'] as $key => $val){
            $c[$key] = $val;
        }

        $this->server->set($c);
        $this->server->on('task',         ['Task',   'onTask']);
        $this->server->on('finish',       ['Task',   'onFinish']);
        $this->server->on('request',      ['Hooker', 'onRequest']);
        $this->server->on('WorkerStop',   ['Hooker', 'onWorkerStop']);
        $this->server->on('WorkerError',  ['Hooker', 'onWorkerError']);
        $this->server->on('WorkerStart',  ['Hooker', 'onWorkerStart']);
        $this->server->on('ManagerStart', ['Hooker', 'onManagerStart']);

        // 是否需要监听额外的端口
        if(isset($config['http']['listen_ip'])){
            $this->server->addlistener($config['http']['listen_ip'], $config['http']['listen_port'], SWOOLE_SOCK_TCP);
        }

        if($config['http']['enable_static_handler']){
            $this->server->set([
                'enable_static_handler' => true,
                'document_root' => $config['http']['document_root'],
            ]);
        }
    }

    public function start() {
        Server::setInstance($this->server);
        Server::setServerType(Server::TYPE_HTTP);

        if(strtoupper(PHP_OS) == Server::OS_LINUX){
            swoole_set_process_name(APP_NAME.'_master');
        }

        Logger::log('======= HTTP master start =======');
        $this->server->start();
    }
}