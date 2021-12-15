<?php

class C_Crawler extends Controller {

    public function protocols(){
        $reget = $this->getParam('reget');
        Crawler::protocols($reget);
        return "Working hard to fetch protocols data, pls wait";
    }

    public function chart(){
        $reget = $this->getParam('reget');
        Crawler::charts($reget);
        return "Working hard to fetch chart data, pls wait";
    }
}