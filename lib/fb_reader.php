<?php

class FbReader {

    private $connector;
    private $cookieStorage;

    public function __construct($connector, $cookieFile = null) {
        $this->connector = $connector;
        $this->cookieStorage = $cookieFile;
    }

    public function connect($homeUrl) {
        $httpHeader = array('Accept-Charset: utf-8', 'Accept-Language: en-us,en;q=0.7,bn-bd;q=0.3', 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
        curl_setopt($this->connector, CURLOPT_URL, $homeUrl);
        curl_setopt($this->connector, CURLOPT_HEADER, 0);
        curl_setopt($this->connector, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->connector, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($this->connector, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->connector, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($this->connector, CURLOPT_COOKIEFILE, $this->cookieStorage);
        curl_setopt($this->connector, CURLOPT_COOKIEJAR, $this->cookieStorage);
        curl_setopt($this->connector, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->connector, CURLOPT_USERAGENT, "user_agent");
        curl_setopt($this->connector, CURLOPT_REFERER, $homeUrl);
        curl_exec($this->connector) or die(curl_error($this->connector));
    }

    public function login($loginUrl, $uname, $pass) {
        $data = 'charset_test=€,´,€,´,水,Д,Є&email=' . urlencode($uname) . '&pass=' . urlencode($pass) . '&login=Login';
        curl_setopt($this->connector, CURLOPT_URL, $loginUrl);
        curl_setopt($this->connector, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->connector, CURLOPT_POST, 1);
        curl_exec($this->connector) or die(curl_error($this->connector));
    }

    public function read($newsfeedUrl) {
        curl_setopt($this->connector, CURLOPT_URL, $newsfeedUrl);
        return curl_exec($this->connector);
    }

}
