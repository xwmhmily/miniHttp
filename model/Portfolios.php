<?php

class M_Portfolios extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'portfolios';
        parent::__construct();
    }

    public function convert_slug($slug){
        return str_replace(" ", "_", strtolower($slug));
    }

    public function save($slug, $portfolios){
        $portfolios = json_decode($portfolios, true);
        if($portfolios['data']['user_list']){
            foreach($portfolios['data']['user_list'] as $val){
                $val['slug']     = $slug;
                $val['add_time'] = time();
                $val['add_date'] = date('Y-m-d H:i:s');
                $this->Insert($val);
            }
        }

        return true;
    }

    // 今天是否抓取过了
    public function has_today_done(){
        $data = $this->Where('add_date', date('Y-m-d'))->SelectOne();
        if($data){
            return true;
        }

        return false;
    }

}