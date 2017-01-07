<?php

class Utils {

    static function printMsg($msg, $type = 'INFO', $prefixChar = '>>>') {
        echo "$prefixChar $type: $msg\n\n";
    }

    static function printHelpMsg() {
        static::printMsg('Type command: "$php my_fb_feeds.php" to read your feed on terminal.');
    }

}
