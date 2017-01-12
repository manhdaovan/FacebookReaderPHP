<?php

class FbReader {

    private $connector;
    private $cookieStorage;
    private $httpHeader;
    private $userAgent;

    public function __construct($connector, $cookieFile = null) {
        $this->connector = $connector;
        $this->cookieStorage = $cookieFile;
        $this->httpHeader = ['Accept-Charset: utf-8', 'Accept-Language: en-us,en;q=0.7,bn-bd;q=0.3',
            'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'];
        $this->userAgent = 'user-agent';
    }

    public function connect($homeUrl) {
        curl_setopt($this->connector, CURLOPT_URL, $homeUrl);
        $this->setDefaultConnectorOpts();
        curl_setopt($this->connector, CURLOPT_REFERER, $homeUrl);
        curl_exec($this->connector) or die(curl_error($this->connector));
    }

    public function login($loginUrl, $uname, $pass) {
        $data = 'charset_test=€,´,€,´,水,Д,Є&email=' . urlencode($uname) . '&pass=' . urlencode($pass) . '&login=Login';
        curl_setopt($this->connector, CURLOPT_URL, $loginUrl);
        curl_setopt($this->connector, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->connector, CURLOPT_POST, 1);
        $this->setDefaultConnectorOpts();
        curl_exec($this->connector) or die(curl_error($this->connector));
    }

    public function read($newsfeedUrl) {
        curl_setopt($this->connector, CURLOPT_URL, $newsfeedUrl);
        $this->setDefaultConnectorOpts();
        return curl_exec($this->connector);
    }

    private function setDefaultConnectorOpts() {
        curl_setopt($this->connector, CURLOPT_HEADER, 0);
        curl_setopt($this->connector, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->connector, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($this->connector, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->connector, CURLOPT_HTTPHEADER, $this->httpHeader);
        curl_setopt($this->connector, CURLOPT_COOKIEFILE, $this->cookieStorage);
        curl_setopt($this->connector, CURLOPT_COOKIEJAR, $this->cookieStorage);
        curl_setopt($this->connector, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->connector, CURLOPT_USERAGENT, $this->userAgent);
    }

}
