<?php

class C_User extends Controller {

	private $m_user;
    
    function __construct(){
        // 本控制器需要登录验证，我们就用中间件的 Auth, Importer 来处理
        $this->middleware(['Auth', 'Importer']);
    	$this->m_user = $this->load('User');
    }

    // Profile
    // URL: http://127.0.0.1:9100/api/user
    public function index(){
        $user = [];
        $user['team'] = 'Lakers';
        $user['username'] = 'Kobe';

        $rep['code'] = 1;
        $rep['data']['user'] = $user;
        return JSON($rep);
    }

    // Upload
    public function upload(){
        $file = $this->getParam('file');
        Logger::log('File is => '.$file);
        return 'File is => '.$file;
    }

}