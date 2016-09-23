<?php

function printFee($msg)
{
    echo "$msg \n\n";
}

function printMsg($msg, $type = 'INFO', $prefixChar = '>>>')
{
    echo "$prefixChar $type: $msg\n\n";
}

function printPost($msg, $type = 'TITLE', $prefixChar = '+++')
{
    $msg = html_entity_decode($msg, ENT_QUOTES);
    printMsg("{$msg} \n", $type, $prefixChar);
}

function printHelpMsg()
{
    printMsg('Type command: "$php my_fb_feeds.php" to read your feed on terminal.');
}

function displayPost($post)
{
    $head = $post->children(0);
    $headTitle = $head->children(0);
    if ($headTitle) {
        if ($headTitle->tag == 'h3')
            $headTitle = $head;
        printPost($headTitle->find('h3', 0)->plaintext);
        $headContent = $headTitle->next_sibling();
        while ($headContent) {
            printPost($headContent->plaintext, '', '>>>');
            foreach ($headContent->find('img') as $img) {
                printPost($img->src, 'IMG', '');
            }
            $headContent = $headContent->next_sibling();
        }
    } else {
        printPost($head->plaintext);
    }
    $activity = $post->children(1);
    $postContent = $head->next_sibling();
    while ($postContent && $postContent->class != $activity->class) {
        printPost($postContent->plaintext, '', '');
        $postContent = $postContent->next_sibling();
    }
    printPost($activity->plaintext, '', '...');
    printFee('----------------------------------');
}
