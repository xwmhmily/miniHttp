<?php

class M_Chart extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'charts';
        parent::__construct();
    }

    public function get_charts_by_date($date){
        return $this->Where('add_date', $date)->SelectOne();
    }
}