<?php

class M_Contract_user extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'contract_user';
        parent::__construct();
    }

    public function save($slug, $data){
        $data = json_decode($data, true);
        if(!$data){
            return;
        }
        
        $data = format_array_data_to_json($data['data']);

        $where = [];
        $where['slug'] = $slug;
        $exists = $this->Where($where)->Total();
        if($exists){
            return $this->Where($where)->Update($data);
        }else{
            $data['slug'] = $slug;
            return $this->Insert($data);
        }
    }

}