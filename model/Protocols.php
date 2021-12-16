<?php

class M_Protocols extends Model {

    private $fields = ['name', 'address', 'symbol', 'url', 'description', 'chain', 'logo', 'audits', 'audit_note', 'gecko_id', 'cmcId', 'category', 'chains', 'module', 'twitter', 'audit_links', 'oracles', 'slug', 'tvl', 'chainTvls', 'change_1h', 'change_1d', 'change_7d', 'staking', 'fdv', 'mcap', 'pool2', 'forkedFrom', 'listedAt', 'audit'];
    
    function __construct(){
        $this->table = TB_PREFIX.'protocols';
        parent::__construct();
    }

    public function get_today_slugs(){
        return $this->Field('name')->Where('status', '=', 1)->Where('add_date', '=', date('Y-m-d'))->Select();
    }

    public function disable($original_name){
        $where = $update = [];
        $where['name'] = $original_name;
        $update['status'] = 0;
        return $this->Where($where)->Update($update);
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

    public function save($protocols){
        foreach($protocols as &$protocol){
            foreach($protocol as $key => $val){
                if(!in_array($key, $this->fields)){
                    unset($protocol[$key]);
                }

                unset($protocol['id']);

                if(is_array($val)){
                    $protocol[$key] = addslashes(json_encode($val, 256));
                }else{
                    $protocol[$key] = addslashes($val);
                }
            }

            $protocol['add_time'] = time();
            $protocol['add_date'] = date('Y-m-d');
            $this->Insert($protocol);
        }

        return true;
    }

    public function remove_today_data(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $this->Where($where)->Delete();
    }

}