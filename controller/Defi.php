<?php

class C_Defi extends Controller {

	private $m_protocols;
    private $m_portfolios;
    private $m_charts;
    private $m_chains;
    
    function __construct(){
        $this->m_charts     = $this->load('Chart');
        $this->m_chains     = $this->load('Chains');
    	$this->m_portfolios = $this->load('Portfolios');
        $this->m_protocols  = $this->load('Protocols');
    }

    public function dashboard(){
        return JSON($this->m_protocols->dashboard());
    }

    public function chains(){
        return JSON($this->m_chains->get_chains_by_date(date('Y-m-d')));
    }

    public function protocols(){
        $chain = $this->getParam('chain');
        return JSON($this->m_protocols->get_protocols_by_chain($chain));
    }

    public function portfolios(){
        return JSON($this->m_portfolios->Select());
    }

    public function charts(){
        return JSON($this->m_charts->Select());
    }
}