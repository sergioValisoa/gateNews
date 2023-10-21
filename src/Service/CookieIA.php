<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;


class CookieIA
{

    private RequestStack $request;
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function check(string $name) : bool
    {
        $response = $this->request->getMainRequest()->cookies->get($name);
        if($response) return true;
        return false;
    }

    public function add(string $name, string $value) : void 
    {
        setcookie($name, $value, strtotime('now + 30 days'),"/");
    }

    public function get($name) : string
    {
        return $response = $this->request->getMainRequest()->cookies->get($name);
    }

    public function remove($name) : void
    {
        if($this->check($name)) {
            setcookie($name, null, -1, '/');
        }
    }
}
