<?php

class C_Defi extends Controller {

	private $m_protocols;
    private $m_portfolios;
    private $m_chart;
    
    function __construct(){
    	$this->m_protocols = $this->load('Portfolios');
        $this->m_chart = $this->load('Chart');
        $this->m_protocols = $this->load('Protocols');
    }

    public function log(){
        return __METHOD__;
    }
}