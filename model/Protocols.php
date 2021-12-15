<?php

class M_Protocols extends Model {

    private $fields = ['name', 'address', 'symbol', 'url', 'description', 'chain', 'logo', 'audits', 'audit_note', 'gecko_id', 'cmcId', 'category', 'chains', 'module', 'twitter', 'audit_links', 'oracles', 'slug', 'tvl', 'chainTvls', 'change_1h', 'change_1d', 'change_7d', 'staking', 'fdv', 'mcap', 'pool2', 'forkedFrom', 'listedAt', 'audit'];
    
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
            if($key == 'id'){
                unset($protocols[$key]);
            }

            if(!in_array($key, $this->fields)){
                unset($protocols[$key]);
            }
        }

        foreach($protocol as $key => $val){
            if(is_array($val)){
                $protocol[$key] = addslashes(json_encode($val, 256));
            }else{
                $protocol[$key] = addslashes($val);
            }

            $protocol['add_time'] = time();
            $protocol['add_date'] = date('Y-m-d H:i:s');
            $this->Insert($protocol);
        }

        return true;
    }

}