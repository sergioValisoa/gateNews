<?php

namespace App\Service\MetierManagerBundle\Utils;

/**
 * Class Util
 * Classe qui contient les fonctions spécifiques nécéssaires
 */
class Util
{
    /**
     * Génération slug
     * @param string $_string
     * @param string $_separator
     * @return string
     */
    public static function slug($_string, $_separator = '-')
    {
        $_accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $_special_cases = array( '&' => 'and', "'" => '');

        $_string = mb_strtolower(trim($_string), 'UTF-8');
        $_string = str_replace(array_keys($_special_cases), array_values($_special_cases), $_string);
        $_string = preg_replace($_accents_regex, '$1', htmlentities( $_string, ENT_QUOTES, 'UTF-8'));
        $_string = preg_replace("/[^a-z0-9]/u", "$_separator", $_string);
        $_string = preg_replace("/[$_separator]+/u", "$_separator", $_string);

        return $_string;
    }

    /**
     * Récupérer les données en curl
     * @param string $_url
     * @return string
     */
    public static function getDataCurl($_url) {
        $_ch = curl_init();

        curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($_ch, CURLOPT_URL, $_url);
        $_result = curl_exec($_ch);
        curl_close($_ch);
        $_response = json_decode($_result, true);

        return $_response;
    }
}
