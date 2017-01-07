<?php

class FbParser {

    private $parser;

    public function __construct($_parser) {
        $this->parser = $_parser;
    }

    public function load($content) {
        $this->parser->load($content);
    }

    /**
     * Display all newsfeed content in plaintext
     */
    public function displaySummary() {
        echo $this->formatContent($this->plaintext(), 'SUMMARY TEXT');
    }

    /**
     * Display each post/event in separately block
     */
    public function displayMainPage() {
        $mainPage = $this->parser->find('#root', 0);
        // Some magic happen here!
        // sometimes div contains newsfeed is 3rd child
        // sometimes 2nd.
        $newsfeeds = $mainPage->children(0)->children(3);
        if (count($newsfeeds->children()) <= 1) {
            $newsfeeds = $mainPage->children(0)->children(2);
        }

        foreach ($newsfeeds->children() as $post) {
            $this->displayPost($post);
        }
    }

    public function getAfterCursor() {
        // Fetch last a tag of #root div
        $seeMoreLink = end($this->parser->find('#root', 0)->find('a'));
        $params = explode('?', $seeMoreLink->href)[1];
        return explode('&', explode('=', $params)[1])[0];
    }

    private function plaintext() {
        return $this->parser->plaintext;
    }

    private function formatContent($content, $type = 'TITLE', $prefixChar = '+++') {
        $content = html_entity_decode($content, ENT_QUOTES);
        return "$prefixChar $type: $content\n\n";
    }

    private function displayPost($post) {
        switch ($post->tag) {
            case 'div':
                $this->displayDivPost($post);
                break;
            case 'iframe':
                $this->displayIframePost($post);
                break;
            default :
                break;
        }
    }

    private function displayDivPost($post) {
        $content = "-------------------------------\n\n";
        foreach ($post->children() as $child) {
            $content .= $child->plaintext . "\n\n";
            foreach ($child->find('img') as $img) {
                if (!$this->isResourceImg($img)) {
                    $content .= "IMG: {$img->src}\n\n";
                }
            }
        }
        echo $this->formatContent($content, 'POST', '>>>');
    }

    private function displayIframePost($post) {
        
    }

    private function isResourceImg($img) {
        return strstr($img, 'fbcdn.net/rsrc.php') || strstr($img, 'fbcdn.net/images/emoji.php');
    }

}
