<?php

$config = [
	'common' => [
		'app_name'                 => 'Mini_Http',
		'app_version'              => '2.0',
		'tb_pk'                    => 'id',
		'tb_prefix'                => 'sl_',
		'tb_suffix_sf'             => '_',
		'user'                     => 'www',
		'group'                    => 'www',
		'backlog'                  => 128,
		'daemonize'                => 1,
		'worker_num'               => 4,
		'task_ipc_mode'            => 1,
		'task_worker_num'          => 1,
		'open_cpu_affinity'        => 1,
		'dispatch_mode'            => 2,
		'heartbeat_idle_time'      => 120,
		'heartbeat_check_interval' => 30,
		'open_eof_check'           => TRUE,
		'package_eof'              => "\r\n",
		'open_length_check'        => true,
		'package_length_type'      => 'N',
		'package_length_offset'    => 8,
  		'package_body_offset'      => 16,
		'log_level'                => 3,
		'error_level'              => 2,
		'module'                   => 'api',
		'log_method'               => 'file',
		'pid_file'                 => APP_PATH.'/log/swoole.pid',
		'stat_file'                => APP_PATH.'/log/swoole.stat',
		'log_file'       => APP_PATH.'/log/swoole_error_'.date('Y-m-d').'.log',
		'mysql_log_file' => APP_PATH.'/log/swoole_mysql_'.date('Y-m-d').'.log',
	],

	'http' => [
		'ip'     => '*',
		'port'   => 9200,

		'enable_static_handler' => true,
		'document_root' => APP_PATH.'/public',
	],

	'mysql' => [
		'required' => true,
		'db'   => 'test',
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => 'root',
		'pwd'  => '123456',
		'max'  => 2,
		'log_sql' => true,
	],

	'mysql_slave' => [
		'db'   => 'test',
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => 'root',
		'pwd'  => '123455',
	],
	
	'redis' => [
		'required' => true,
		'db'   => '0',
		'host' => '127.0.0.1',
		'port' => 6379,
		'pwd'  => '123456',
	],

	'process' => [
		'Mini_Swoole_importer'=> [
			'num' => 1, 
			'mysql' => true,
			'redis' => true,
			'callback' => ['Importer', 'run'],
		],
	],
];

return $config;