<?php

Class Messages extends getContent
{
    private $messages;
    private $testNumberMsg;
    private $subjectsMessages;
    public $errors;
    public $noFullContent;
    public $countMessagesErrorCode;
    public $getSubjectsMessagesErrorCode;

    public function __construct()
    {
        parent::__construct();

        $this->errors = array();
        $this->messages = array();
        $this->testNumberMsg = 60;
        $this->subjectsMessages = array();
        $this->noFullContent = 'При запросе страница загрузилась не полностью. Отсутсвует закрывающий тег html';
        $this->countMessagesErrorCode = 'Результат запроса на количество писем вернулся с кодом: ';
        $this->getSubjectsMessagesErrorCode = 'Результат запроса на выборку тем писем вернулся с кодом: ';
    }

    public function totalNumberOfMessages()
    {
        $content = $this->getVendorContent(1);

        if ($content['http_code'] == "200") {

            if ($this->checkCloseTagHtml($content['content'])) {

                $html = str_get_html($content['content']);
                if ($html->innertext != '' and count($html->find("span.msglist-title__counter")) and count($html->find("table.msglist tr.js-messageline"))) {

                    $spanElements = $html->find("span.msglist-title__counter");
                    $totalNumberOfMessages = $spanElements[0]->plaintext;
                    $this->messages['totalNumber'] = $totalNumberOfMessages;

                    $numberOfMessagesPerPage = count($html->find("table.msglist tr.js-messageline"));
                    $this->messages['perPage'] = $numberOfMessagesPerPage;
                }
                unset($content);

            } else {
                array_push($this->errors, $this->noFullContent);
                return $this->errors;
            }

        } else {
            array_push($this->errors, $this->countMessagesErrorCode . $content['http_code']);
            return $this->errors;
        }

        return $this->messages;

    }

    public function getSubjectsMessages()
    {
        $messages = $this->totalNumberOfMessages();

        if (strstr($messages[0], $this->countMessagesErrorCode) || strstr($messages[0], $this->noFullContent)) {
            return $this->errors;
        }

        $countPages = $this->countPages((int)$messages['totalNumber'], (int)$messages['perPage']);

        for ($pageNumber = 1; $pageNumber <= $countPages - $this->testNumberMsg; $pageNumber++) {

            $content = $this->getVendorContent($pageNumber);

            if ($content['http_code'] == "200") {

                if ($this->checkCloseTagHtml($content['content'])) {

                    $this->parseMessages($content);

                } else {
                    array_push($this->errors, $this->noFullContent);
                    return $this->errors;
                }

            } else {
                array_push($this->errors, $this->getSubjectsMessagesErrorCode . $content['http_code']);
                return $this->errors;
            }

        }

        return $this->subjectsMessages;
    }

    public function checkCloseTagHtml($content)
    {
        return stristr($content, '</html>') ? true : false;
    }

    public function parseMessages($content)
    {
        if (!empty($content['content'])) {

            $html = str_get_html($content['content']);
            if ($html->innertext != '' and count($html->find("span.messageline__subject"))) {

                foreach ($html->find('span.messageline__subject') as $subjects) {

                    array_push($this->subjectsMessages, $subjects->plaintext);
                }
            }
            unset($content);
        }

    }

    public function countPages($totalMessages, $messagesPerPage)
    {
        return floor($totalMessages / $messagesPerPage);
    }


}