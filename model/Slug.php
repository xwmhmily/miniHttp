<?php

class M_Slug extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'slug';
        parent::__construct();
    }

    public function save($original_name, $detail){
        $detail = json_decode($detail, true);
        if(!$detail || !isset($detail['data'])){
            $m_protocol = Helper::load('Protocols');
            $m_protocol->disable($original_name);
            return;
        }

        $data = $detail['data'];
        $slug = $data['name'];
        unset($data['id']);

        $data = format_array_data_to_json($data);

        $where = [];
        $where['name'] = $slug;
        $exists = $this->where($where)->SelectOne();
        if($exists){
            return $this->Where($where)->update($data);
        }else{
            return $this->Insert($data);
        }
    }

}