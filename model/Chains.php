<?php

class M_Chains extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'chains';
        parent::__construct();
    }

    public function save($m_protocols){
        $chains = $m_protocols->get_today_chains();

        $final_chains = [];
        if($chains){
            foreach($chains as $chain){
                foreach($chain as $key => $val){
                    if(!in_array($val, $final_chains)){
                        $final_chains[] = $val;
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
    }

}