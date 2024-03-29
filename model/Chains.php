<?php

class M_Chains extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'chains';
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

    public function remove_today_data(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $this->Where($where)->Delete();
    }

    public function save($m_protocols){
        $chains = $m_protocols->get_today_chains();

        $final_chains = [];
        if($chains){
            foreach($chains as $chain){
                foreach($chain as $val){
                    $val = json_decode($val, true);
                    foreach($val as $v){
                        if(!in_array($v, $final_chains)){
                            $final_chains[] = $v;
                        }
                    }
                }
            }

            if($final_chains){
                foreach($final_chains as $chain){
                    $c = [];
                    $c['name'] = $chain;
                    $c['add_date'] = date('Y-m-d');
                    $this->Insert($c);
                }
            }
        }

        return true;
    }

    public function get_chains_by_date($date){
        $where = [];
        $where['add_date'] = $date;
        $data = $this->Field("name")->Where($where)->Select();

        $chains = [];
        if($data){
            foreach($data as $val){
                $chains[] = $val['name'];
            }
        }

        return $chains;
    }

}