<?php

class C_Crawler extends Controller {

    public function protocols(){
        $force_to_get = $this->getParam('force_to_get');
        Crawler::protocols($force_to_get);
        return "Working hard to fetch protocols data, pls wait";
    }

    public function chart(){
        $force_to_get = $this->getParam('force_to_get');
        Crawler::charts($force_to_get);
        return "Working hard to fetch chart data, pls wait";
    }
}