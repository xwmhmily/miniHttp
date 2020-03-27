<?php

class C_Http extends Controller {

	private $m_user;
	private $m_news;
    
    function __construct(){
    	$this->m_user = $this->load('User');
        $this->m_news = $this->load('News');
    }

    public function plugin(){
        $token = $this->getParam('token', FALSE);
        $retval = Registry::get('Permission')->checkPermission($token);
        if(!$retval){
            return 'Bad token !';
        }

        $i18n = Registry::get('I18N');
        $username_text = $i18n->translate('username');
        $password_text = $i18n->translate('password');
        $this->response->write('English username_text => '.$username_text.'<br />');
        $this->response->write('English password_text => '.$password_text.'<br />');

        $username_text = $i18n->translate('username', 2);
        $password_text = $i18n->translate('password', 2);
        $this->response->write('Chinese username_text => '.$username_text.'<br />');
        $this->response->write('Chinese password_text => '.$password_text.'<br />');

        $this->response->end('__DONE__');
    }

    public function log(){
        Logger::debug('This is a debug msg');
        Logger::info('This is an info msg');
        Logger::warn('This is a warn msg');
        Logger::error('This is an error msg');
        Logger::fatal('This is a fatal msg');
        Logger::log('This is a log msg');

        $level = Config::get('common', 'error_level');
        return 'Current error_level is => '.$level;
    }

    // 测试onError事件
    // 为了避免由于exception, error 导致worker 退出后客户端一直收不回复的问题
    // 使用 try...catch(Throwable) 来处理
	public function onError(){
        $result = $this->m_player->SelectOne();
        return 'Result is => '.$result;
	}

    // Ping and Pong
    public function ping(){
        return 'PONG';
    }

    // get Config with key
    public function configAndKey(){
        $redis_config = Config::get('redis');
        return JSON($redis_config).'<br />';

        $redis_host = Config::get('redis', 'host');
        return JSON('Host is '.$redis_host).'<br />';

        $redis_port = Config::get('redis', 'port');
        return JSON('Port is '.$redis_port);
        return JSON($redis_config);
    }

    // Get all users
    public function users(){
        $users = $this->m_user->SelectAll();
        return JSON($users);
    }

    public function limitUsers(){
        $news = $this->m_user->Limit()->Select();
        return JSON($news);
    }

    // MySQL 压力测试
    public function stress(){
        $max = 10000;
        $start_time = Logger::getMicrotime();
        for($i = 1; $i <= $max; $i++){
            $users = $this->m_user->Select();
        }
        $end_time = Logger::getMicrotime();
        $cost = $end_time - $start_time;
        return 'Time => '.$cost.', TPS => '.$max/$cost;
    }

    // SelectAll
    public function all(){
        $users = $this->m_user->SelectAll();
        return JSON($users);

        $news = $this->m_news->Select();
        return JSON($news);

        $one_news = $this->m_news->SelectOne();
        return JSON($one_news);
    }

    // Mix common sql and transaction test
    public function mix(){
        $users = $this->m_user->SelectAll();
        $this->response->write(JSON($users));

        $news = $this->m_news->Select();
        $this->response->write(JSON($news));

        $one_news = $this->m_news->SelectOne();
        $this->response->write(JSON($one_news));

        $this->response->write(PHP_EOL.'======= HERE IS TRANSACTION ========='.PHP_EOL);

        $this->m_user->BeginTransaction();
        $users = $this->m_user->SelectAll();
        $news = $this->m_news->Select();

        if($users && $news){
            $this->m_news->Commit();
            $this->response->write(JSON($users));
            $this->response->write(JSON($news));
        }else{
            $this->m_news->Rollback();
            $this->response->write('ERRORRRRRRRRRRRRRR');
        }

        return;
    }

    // Transaction
    public function transaction(){
        $this->m_user->BeginTransaction();
        $user = $this->m_user->SelectOne();
        $news = $this->m_news->Select();

        if($user && $news){
            $this->m_news->Commit();
            $this->response->write('Master user => '.JSON($user)."<br />");
            $this->response->write('Master news => '.JSON($news)."<br />");
        }else{
            $this->m_news->Rollback();
            $this->response->write('ERRORRRRRRRRRRRRR');
        }

        $field = ['id', 'username', 'password'];
        $where = ['id' => 2];
        $user = $this->m_user->SetDB('SLAVE')->ClearSuffix()->Field($field)->Where($where)->SelectOne();
        $this->response->write('Slave => '.JSON($user)."<br />");

        $where = ['status' => 1];
        $order = ['id' => 'DESC'];
        $user = $this->m_user->SetDB('SLAVE')->Suffix(38)->Field($field)->Where($where)->Order($order)->Limit(10)->Select();
        $this->response->write('Slave with suffix => '.JSON($user)."<br />");
        return;
    }

    // Security
    public function security(){
        return JSON($this->request);
    }

    // Autoload
    public function rabbit(){
        $rabbit = new RabbitMQ();
        return 'A Rabbit is running happily now';
    }

    public function selectOne(){
        $news = $this->m_user->SelectOne();
        return JSON($news);
    }

    public function pagination(){
        return JSON($this->m_user->Limit()->Select());
    }
    
    // 测试 MySQL 自动断线重连
    public function reconnect(){
        $i = 1;
        $max = 1000;
        while($i <= $max){
            $m_user = $this->m_user->SelectOne();
            if(!$m_user){
                $m_user = 'Stop reconnecting';
                Logger::log($m_user);
                $retval = $this->response->write($m_user);
                break;
            }else{
                $m_user = JSON($m_user);
            }

            $retval = $this->response->write($i.' => '.$m_user.'<br />');
            if(!$retval){
                break;
            }

            $where  = ['id' => 1035];
            $m_user = $this->m_user->Where($where)->SelectOne();
            $retval = $this->response->write('Another '.JSON($m_user).'<br />');

            $i++; sleep(1);
        }

        return;
    }

    // MySQL slave
    public function slave(){
        $m_user = $this->load('User');

        $i = 1;
        while($i <= 3){
            $user = $m_user->SetDB('SLAVE')->SelectOne();
            $this->response->write('Slave first => '.JSON($user).'<br />');

            $user = $m_user->SetDB('MASTER')->SelectOne();
            $this->response->write('Master => '.JSON($user).'<br />');

            $field = ['id', 'username'];
            $where = ['id' => 2];
            $user = $m_user->SetDB('SLAVE')->Field($field)->Where($where)->SelectOne();
            $this->response->write('Slave again => '.JSON($user).'<br />');

            $field = ['id', 'username'];
            $user = $m_user->SetDB('SLAVE')->SelectByID($field, 2);
            $this->response->write('Slave by ID => '.JSON($user).'<br />');

            $field = ['id', 'username', 'password'];
            $user = $this->load('User')->SetDB('SLAVE')->Field($field)->Suffix(38)->SelectOne();
            $this->response->write('Slave with suffix => '.JSON($user).'<br />');

            $i++; sleep(1);
        }

        return;
    }

    // 测试SQL 报错
    public function sql(){
        $field = ['id', 'usernamex'];
        $order = ['id' => 'DESC'];
        $users = $this->m_user->Field($field)->Order($order)->Select();
        if(!$users){
            $this->response->write('NO USERS FOUND'.'<br />');
        }else{
            $this->response->write(JSON($users).'<br />');
        }

        $users = $this->m_user->SelectAll();
        $this->response->write('Users => '.JSON($users).'<br />');

        $user = $this->m_user->SelectByID('', 24);
        $this->response->write('User => '.JSON($user).'<br />');
        $this->response->end();
    }

    // Suffix
    public function suffix(){
        $user = $this->load('User')->Suffix(38)->ClearSuffix()->Suffix(52)->SelectOne();
        return 'Suffix user => '.JSON($user);
    }

    // Redis and MySQL with Master / slave
    public function connector(){
        for($i = 1; $i <= 100; $i++){
            $this->response->write('=============='.$i.'===================<br />');

            // Master
            $news = $this->m_news->Select();
            $this->response->write(' Master => '.JSON($news).'<br />');

            $users = $this->m_user->SetDB('MASTER')->SelectAll();
            $this->response->write(' Master => '.JSON($users).'<br />');

            // Master
            $user = $this->m_user->SelectByID('', 2);
            $this->response->write(' Master => '.JSON($user).'<br />');

            $key = $this->getParam('key');
            $val = Cache::get($key);
            $this->response->write(' Redis => '.$val.'<br />');

            // Suffix
            $user = $this->load('User')->SetDB('SLAVE')->Suffix(38)->SelectOne();
            $this->response->write(' Suffix user => '.JSON($user).'<br />');

            // What if errors occur
            $user = $this->load('User')->SetDB('SLAVE')->Suffix(52)->SelectOne();
            $this->response->write(' Suffix user => '.JSON($user).'<br />');

            // Master
            $user = $this->m_user->SelectByID('', 1);
            $this->response->write(' Master => '.JSON($user).'<br />');

            // Change Master to Slave, just call the SetDB()
            $user = $this->m_user->SetDB('SLAVE')->SelectByID('', 1);
            $this->response->write(' Slave => '.JSON($user).'<br />');

            $this->response->write(PHP_EOL.'==============='.$i.'============'.'<br />');

            sleep(1);
        }
        return;
    }

    // Redis
    public function redis(){
        $key = $this->getParam('key');
        $this->response->write('Key => '.$key.'<br />');
        
        if($key){
            $i = 1;
            while($i < 10){
                $val = Cache::get($key);
                $this->response->write(date('Y-m-d H:i:s'). ' => '.$val.'<br />');
                $i++; sleep(1);
            }
        }else{
            $this->response->write('Key is required !');
        }
        
        return;
    }

    public function param(){
        return $this->getParam('username');
    }

    public function QueryOne(){
        $username = $this->getParam('username');
        $user = $this->m_user->getUserByUsername($username);
        return JSON($user);
    }

    public function multiInsert(){
        $user = $users = [];

        $user['username'] = 'Kobe';
        $user['password'] = md5('Lakers');
        $users[] = $user;

        $user['username'] = 'Curry';
        $user['password'] = md5('Warriors');
        $users[] = $user;

        $user['username'] = 'Thompson';
        $user['password'] = md5('Warriors');
        $users[] = $user;

        return $this->m_user->multiInsert($users);
    }

    public function timer(){
        $timerID = Timer::add(2000, [$this, 'tick'], ['xyz', 'abc', '123']);
        return 'Timer has been set, id is => '.$timerID;
    }

    public function tick($timerID, $args){
        Logger::log('Args in '.__METHOD__.' => '.JSON($args));
        Timer::clear($timerID);
        return;
    }

    public function task(){
        $args   = [];
        $args['callback'] = ['Importer', 'Run'];
        $args['param']    = ['Lakers', 'Swoole', 'Westlife'];
        $taskID = Task::add($args);
        return 'Task has been set, id is => '.$taskID;
    }
}