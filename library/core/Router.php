<?php

class Router {

	const FUNCTION_INDEX           = 'index';
	const FUNCTION_ROUTER_STARTUP  = 'routerStartup';
	const FUNCTION_ROUTER_SHUTDOWN = 'routerShutdown';

	public static function routerStartup(){
		$objs = Registry::getAll();
		if($objs){
			foreach($objs as $obj){
				if(method_exists($obj, self::FUNCTION_ROUTER_STARTUP)){
					call_user_func([$obj, self::FUNCTION_ROUTER_STARTUP]);
				}
			}
		}
	}

	public static function routerShutdown(){
		$objs = Registry::getAll();
		if($objs){
			foreach($objs as $obj){
				if(method_exists($obj, self::FUNCTION_ROUTER_SHUTDOWN)){
					call_user_func([$obj, self::FUNCTION_ROUTER_SHUTDOWN]);
				}
			}
		}
	}

	public static function parse($data){
		return self::parseHttpRouter($data);
	}

	private static function parseHttpRouter($uri){
		$config  = Config::get('common');

		$modules = [];
		if(isset($config['module'])){
			$modules = explode(',', $config['module']);
		}

        $module = $controller = $action = '';
        $request_uri = explode('/', $uri);

        if(in_array($request_uri[1], $modules)){
            $module = trim($request_uri[1]);

            if(isset($request_uri[2])){
                $controller = trim($request_uri[2]);
            }

            if(isset($request_uri[3])){
                $action = trim($request_uri[3]);
            }
        }else{
            if(isset($request_uri[1])){
                $controller = trim($request_uri[1]);
            }

            if(isset($request_uri[2])){
                $action = trim($request_uri[2]);
            }
		}
		
		if(!$module){
            $module = self::FUNCTION_INDEX;
        }

        if(!$controller){
            $controller = self::FUNCTION_INDEX;
        }

        if(!$action){
            $action = self::FUNCTION_INDEX;
        }
		
		$retval = [];
		$retval['module'] = $module;
		$retval['controller'] = $controller;
		$retval['action'] = $action;
		return $retval;
	}

}