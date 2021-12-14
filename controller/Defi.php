<?php

class C_Defi extends Controller {

	private $m_protocol;
    private $m_portfolios;
    private $m_chart;
    
    function __construct(){
    	
    }

    public function log(){
        return __METHOD__;
    }
}