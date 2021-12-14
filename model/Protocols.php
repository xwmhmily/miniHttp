<?php

class M_Protocols extends Model {
    
    function __construct(){
        $this->table = TB_PREFIX.'protocols';
        parent::__construct();
    }

}