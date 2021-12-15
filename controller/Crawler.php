<?php

class C_Crawler extends Controller {

    public function protocols(){
        Crawler::protocols();
        return "Working hard to fetch protocols data, pls wait";
    }

    public function chart(){
        Crawler::charts();
        return "Working hard to fetch chart data, pls wait";
    }
}