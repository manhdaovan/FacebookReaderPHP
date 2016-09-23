<?php

require_once './lib/utils.php';
require_once './vendor/simple_html_dom.php';

// Basic fb account info
$login_email = 'your_email_or_phone_number_or_username';
$login_pass = 'your_password';

// Cookie storage file
$cookie_file = "cookie.txt";

// Request url
$home = 'https://m.facebook.com';
$login = 'https://m.facebook.com/login.php';

// Global variables
$afterCursor = null;
if (isset($argv[1]) && ($argv[1] == '-h' || $argv[1] == '--help')) {
    printHelpMsg();
    exit();
} else {
    $args = split('=', $argv[1]);
    $afterCursor = split('&', $args[1])[0];
}

// Visit homepage to get new cookies
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $home);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Charset: utf-8', 'Accept-Language: en-us,en;q=0.7,bn-bd;q=0.3', 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'));
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "user_agent");
curl_setopt($ch, CURLOPT_REFERER, $home);
curl_exec($ch) or die(curl_error($ch));

printMsg('Request to "http://m.facebook.com" success!');

// Login with given cookies
curl_setopt($ch, CURLOPT_URL, $login);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'charset_test=€,´,€,´,水,Д,Є&email=' . urlencode($login_email) . '&pass=' . urlencode($login_pass) . '&login=Login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_exec($ch) or die(curl_error($ch));
printMsg('Login success!');

// Visit home feed
$feed = 'http://m.facebook.com/stories.php';
if ($afterCursor) {
    curl_setopt($ch, CURLOPT_URL, $feed . '?aftercursorr=' . $afterCursor);
} else {
    curl_setopt($ch, CURLOPT_URL, $feed);
}
$demo = curl_exec($ch);

// Init html parser
$html = new simple_html_dom();
$html->load($demo);
printPost($html->plaintext, 'PREVIEW TEXT');
$mainPage = $html->find('#objects_container', 0);
$readMore = $mainPage->children(0)->children(0)->children(3);
$afterCursor = split('&', split('=', $readMore->children(0)->getAttribute('href'))[1])[0];

// Print post content
foreach ($mainPage->children(0)->children(0)->children(2)->children as $post) {
    displayPost($post);
}

$nextCmd = 'php my_fb_feeds.php --cursor=' . $afterCursor;
printMsg("Get more feed with command:\n\n $nextCmd\n\n");
