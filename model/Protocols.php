<?php

class M_Protocols extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'protocols';
        parent::__construct();
    }

    // 今天是否抓取过了
    public function has_today_done(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $data = $this->Where($where)->SelectOne();
        if($data){
            return true;
        }

        return false;
    }

    public function save_protocols($protocols){
        $protocols['add_time'] = time();
        $protocols['add_date'] = date('Y-m-d H:i:s');
        return $this->Insert($protocols);
    }

}