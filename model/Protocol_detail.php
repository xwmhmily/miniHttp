<?php

class M_Protocol_detail extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'protocol_detail';
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

    public function save($original_name, $data){
        $data = json_decode($data, true);
        if(!$data || !isset($data['chainTvls'])){
            return;
        }

        $data = $data['chainTvls'];
        foreach($data as $key => $val){
            // key: Avalanche, Harmony, Ethereum
            // val & k: tvl, tokensInUsd, tokens
            foreach($val as $k => $v){
                // $v = json_decode($v, true);
                $i = [];
                $i['slug']  = $original_name;
                $i['chain'] = $key;
                $i['key']   = $k;
                $i['data']  = json_encode($v);
                $i['add_date'] = date('Y-m-d');
                $this->Insert($i);
            }
        }

        return true;
    }

    public function remove_today_data(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $this->Where($where)->Delete();
    }

}