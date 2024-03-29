<?php

class M_Portfolios extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'portfolios';
        parent::__construct();
    }

    public function save($slug, $portfolios){
        $portfolios = json_decode($portfolios, true);
        if($portfolios['data']['user_list']){
            foreach($portfolios['data']['user_list'] as $val){
                $val['slug']     = $slug;
                $val['add_time'] = time();
                $val['add_date'] = date('Y-m-d');
                $this->Insert($val);
            }
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