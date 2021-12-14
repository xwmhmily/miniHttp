<?php

class M_Portfolios extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'portfolios';
        parent::__construct();
    }

}