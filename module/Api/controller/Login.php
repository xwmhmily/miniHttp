<?php

class C_Login extends Controller {

	private $m_user;
    
    function __construct(){
    	$this->m_user = $this->load('User');
    }

    // http demo
    // URL: http://127.0.0.1:9100/api/login
    public function index(){
        $captcha  = $this->getParam('captcha');
        $username = $this->getParam('username');
        $password = $this->getParam('password');

        $this->response->write('Username => '.$username.'<br />');
        $this->response->write('Password => '.$password.'<br />');
        $this->response->write('Captcha => '.$captcha.'<br />');
        return;
    }

    // URL: http://127.0.0.1:9100/api/login/logout
    public function logout(){
        // Destroy the token or session
        return __METHOD__;
    }

}