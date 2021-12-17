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
            unset($data['logo_url']);
            return $this->Where($where)->update($data);
        }else{
            $data['logo_url'] = $this->save_logo($data['logo_url']);
            return $this->Insert($data);
        }
    }

    private function save_logo($logo_url){
        $save_path = APP_PATH.'/public/image';
        $image_data = file_get_contents($logo_url);
        if($image_data){
            $image_name = get_image_name_via_url($logo_url);
            $retval = file_put_contents($save_path.'/'.$image_name, $image_data);
            if($retval){
                return $image_data;
            }
        }

        return null;
    }

}