<?php

class C_Defi extends Controller {

	private $m_protocols;
    private $m_portfolios;
    private $m_chart;
    
    function __construct(){
    	$this->m_portfolios = $this->load('Portfolios');
        $this->m_chart = $this->load('Chart');
        $this->m_protocols = $this->load('Protocols');
    }

    public function protocols(){
        return JSON($this->m_protocols->Select());
    }

    public function portfolios(){
        return JSON($this->m_portfolios->Select());
    }

    public function chart(){
        return JSON($this->m_chart->Select());
    }
}