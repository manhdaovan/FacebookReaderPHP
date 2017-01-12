<?php

require_once './lib/utils.php';
require_once './lib/fb_reader.php';
require_once './lib/fb_parser.php';
require_once './vendor/simple_html_dom.php';

/**
 * ====================================
 * Fill your basic FB account info below
 * ====================================
 */
$login_email = 'your_fb_login_username_or_email';
$login_pass = 'your_fb_login_password';

// Cookie storage file
$cookie_file = "cookie.txt";

// Request url
$urlHome = 'https://m.facebook.com';
$urlLogin = 'https://m.facebook.com/login.php';
$urlFeed = 'http://m.facebook.com/stories.php';

// Global variables & get param from command
$afterCursor = null;
if (isset($argv[1]) && ($argv[1] == '-h' || $argv[1] == '--help')) {
    Utils::printHelpMsg();
    exit();
} else {
    $args = split('=', $argv[1]);
    $afterCursor = split('&', $args[1])[0];
    // Build newsfeed url with cursor
    $urlFeed .= '?aftercursorr=' . $afterCursor;
}

// Init connector
$connector = curl_init();
$fbReader = new FbReader($connector, $cookie_file);

// Skip visit home and login if logged in
if (empty($afterCursor)) {
    // Visit homepage to get new cookies
    $fbReader->connect($urlHome);
    Utils::printMsg("Request to -- {$urlHome} -- success!");

    // Login with given cookies, email/username and password
    $fbReader->login($urlLogin, $login_email, $login_pass);
    Utils::printMsg('Login success!');
}

// Fetch newsfeed content
$newsfeedContent = $fbReader->read($urlFeed);

// Init html parser
$parser = new FbParser(new simple_html_dom());
$parser->load($newsfeedContent);

// Display newsfeed content
$parser->displaySummary();
$parser->displayMainPage();

// Display next command
$afterCursor = $parser->getAfterCursor();
$nextCmd = 'php my_fb_feeds.php --aftercursor=' . $afterCursor;
Utils::printMsg("Get more feed with command:\n\n $nextCmd\n\n");
