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
        foreach($protocols as $key => $protocol){
            $protocol[$key]['chainTvls'] = json_encode($protocol['chainTvls']);
            $protocol[$key]['add_time']  = time();
            $protocol[$key]['add_date']  = date('Y-m-d H:i:s');
        }
        
        return $this->Insert($protocols);
    }

}