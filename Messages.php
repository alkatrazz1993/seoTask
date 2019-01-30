<?php

Class Messages extends getContent
{
    private $subjectsMessages;
    private $messages;

    public function __construct()
    {
        parent::__construct();

        $this->subjectsMessages = array();
        $this->messages = array();
    }

    function totalNumberOfMessages()
    {
        //require_once 'helpers/simple_html_dom.php';

        $content = $this->getVendorContent(1);

        if ($content) {

            $html = str_get_html($content['content']);
            if ($html->innertext != '' and count($html->find("span.msglist-title__counter")) and count($html->find("table.msglist tr.js-messageline"))) {

                $spanElements = $html->find("span.msglist-title__counter");
                $totalNumberOfMessages = $spanElements[0]->plaintext;
                $this->messages['totalNumber'] = $totalNumberOfMessages;

                $numberOfMessagesPerPage = count($html->find("table.msglist tr.js-messageline"));
                $this->messages['perPage'] = $numberOfMessagesPerPage;
            }
        }

        return $this->messages;

    }

    function getSubjectsMessages()
    {
        //require_once 'helpers/simple_html_dom.php';

        $messages = $this->totalNumberOfMessages();
        $countPages = $this->countPages((int)$messages['totalNumber'], (int)$messages['perPage']);

        for ($pageNumber = 1; $pageNumber <= $countPages - 70; $pageNumber++) {

            $content = $this->getVendorContent($pageNumber);

            if ($content) {

                $html = str_get_html($content['content']);
                if ($html->innertext != '' and count($html->find("span.messageline__subject"))) {

                    foreach ($html->find('span.messageline__subject') as $subjects) {

                        array_push($this->subjectsMessages, $subjects->plaintext);
                    }
                }
            }
            unset($content);
        }

        return $this->subjectsMessages;
    }

    public function countPages($totalMessages, $messagesPerPage)
    {
        return floor($totalMessages / $messagesPerPage);
    }


}