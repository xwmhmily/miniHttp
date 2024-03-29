<?php

class C_Crawler extends Controller {

    public function protocols(){
        $reget = $this->getParam('reget');
        return Crawler::protocols($reget);
    }

    public function protocol_detail(){
        $reget = $this->getParam('reget');
        return Crawler::protocol_detail($reget);
    }

    public function portfolios(){
        $reget = $this->getParam('reget');
        return Crawler::portfolios($reget);
    }

    public function charts(){
        $reget = $this->getParam('reget');
        return Crawler::charts($reget);
    }

    public function chains(){
        $reget = $this->getParam('reget');
        return Crawler::chains($reget);
    }

    public function chain_chart(){
        $reget = $this->getParam('reget');
        return Crawler::chain_chart($reget);
    }
}