<?php

namespace App\Service;

use Statickidz\GoogleTranslate;



class Translation
{

    public function __construct()
    {
    }

    // max number 50000
    public function translateWithGoogleTranslate($fromLang, $toLang, $text) : string
    {
        $trans = new GoogleTranslate();
        return $trans->translate($fromLang, $toLang, $text);
    }

    public function translate($from, $to, $text) : string
    {
        $CLIENT_ID = "FREE_TRIAL_ACCOUNT";
        $CLIENT_SECRET = "PUBLIC_SECRET";

        $postData = array(
        'fromLang' => $from,
        'toLang' => $to,
        'text' => $text
        );

        $headers = array(
        'Content-Type: application/json',
        'X-WM-CLIENT-ID: '.$CLIENT_ID,
        'X-WM-CLIENT-SECRET: '.$CLIENT_SECRET
        );

        $url = 'http://api.whatsmate.net/v1/translation/translate';
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        return $response;
    }
}
