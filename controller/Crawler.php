<?php

class C_Crawler extends Controller {

    public function protocols(){
        Crawler::protocols();
        return "Working hard to fetch protocols data, pls wait";
    }

    /*
    public function portfolios(){
        $protocols = $this->m_protocols->Select();
        if(!$protocols){
            Logger::warn("NO PROTOCOLS FOUND");
            return "NO PROTOCOLS FOUND";
        }

        foreach($protocols as $slug){
            Crawler::slug($slug);
        }

        return "Working hard to fetch portfolios data, pls wait";
    }
    */

    public function chart(){
        Crawler::charts();
        return "Working hard to fetch chart data, pls wait";
    }
}