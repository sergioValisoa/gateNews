<?php

namespace App\Service;

use App\Service\CookieIA;

class DataPost {


    // CHECK IF USER IS CONNECTED

    private $dataPosts;
    private CookieIA $cookieIA;
    public function __construct(CookieIA $cookieIA)
    {
        $this->dataposts = [];
        $this->cookieIA = $cookieIA;
        
    }

    public function getDataPosts($name = "da-ia") : array
    {
        $this->dataPosts = json_decode($this->cookieIA->get($name));
        return $this->dataPosts;
    }

    public function addDataPosts(int $idPost) : void 
    {
        $this->dataPosts = $this->getDataPosts();
        array_push($this->dataPosts, $idPost);
        $this->dataPosts = array_unique($this->dataPosts);
        $lenghtDataPosts = count($this->dataPosts);
        if ($lenghtDataPosts > 3) {
            $this->dataPosts = array_slice($this->dataPosts, $lenghtDataPosts - 3, 3);
        }
        $this->cookieIA->add("da-ia", json_encode($this->dataPosts));
    }

}