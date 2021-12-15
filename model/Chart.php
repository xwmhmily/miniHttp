<?php

class M_Chart extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'charts';
        parent::__construct();
    }

    public function save($charts){
        $charts = json_decode($charts, true);
        foreach($charts as $chart){
            $chart['add_time'] = time();
            $chart['add_date'] = date('Y-m-d H:i:s');
            $this->Insert($chart);
        }

        return true;
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
}