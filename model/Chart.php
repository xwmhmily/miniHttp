<?php

class M_Chart extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'charts';
        parent::__construct();
    }

    public function save($charts){
        $charts['add_time'] = time();
        $charts['add_date'] = date('Y-m-d H:i:s');
        return $this->Insert($charts);
    }

    // 今天是否抓取过了
    public function has_today_done(){
        $data = $this->Where('add_date', date('Y-m-d'))->SelectOne();
        if($data){
            return true;
        }

        return false;
    }
}