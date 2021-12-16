<?php

class M_Contract_user extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'contract_user';
        parent::__construct();
    }

    public function save($slug, $data){
        $data = json_decode($data, true);
        if(!$data || !isset($data['data'])){
            return;
        }
        
        $data = format_array_data_to_json($data['data']);
        $data['slug']     = $slug;
        $data['add_time'] = time();
        $data['add_date'] = date('Y-m-d');
        return $this->Insert($data);
    }

}