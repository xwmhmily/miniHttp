<?php

class M_Portfolios extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'portfolios';
        parent::__construct();
    }

    public function save($slug, $portfolios){
        $portfolios['slug']     = $slug;
        $portfolios['add_time'] = time();
        $portfolios['add_date'] = date('Y-m-d H:i:s');
        return $this->Insert($portfolios);
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