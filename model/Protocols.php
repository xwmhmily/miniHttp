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
        foreach($protocols as $protocol){
            $protocol['chains']    = json_encode($protocol['chains']);
            $protocol['chainTvls'] = json_encode($protocol['chainTvls']);
            $protocol['add_time']  = time();
            $protocol['add_date']  = date('Y-m-d H:i:s');
            $this->Insert($protocol);
        }

        return true;
    }

}