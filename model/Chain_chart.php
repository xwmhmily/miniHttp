<?php

class M_Chain_chart extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'chain_chart';
        parent::__construct();
    }

    public function save($charts){
        $charts = json_decode($charts, true);
        foreach($charts as $chart){
            $chart['add_time'] = time();
            $chart['add_date'] = date('Y-m-d');
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

    public function remove_today_data(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $this->Where($where)->Delete();
    }
}