<?php
    function authMailRu($page)
    {
        $url = 'https://auth.mail.ru/cgi-bin/auth';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // отправляем на
        curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.0.4; Desire HD Build/IMM76D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19');
        curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);// таймаут4
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_REFERER, "https://e.mail.ru/login");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/my_cookies.txt'); // сохранять куки в файл
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/my_cookies.txt');
        curl_setopt($ch, CURLOPT_POST, 1); // использовать данные в post
        $login='seotest';
        $password='1825dec14';
        $domain = 'bk.ru';
        $postField = array(
            'Domain' => $domain,
            'Login' => $login,
            'Password' => $password
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        curl_exec($ch);

        $url2 ="https://m.mail.ru/messages/inbox?page=$page";
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);// таймаут4
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_REFERER, "https://auth.mail.ru/cgi-bin/auth?Domain=$domain&Login=$login&Password=$password");
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/my_cookies.txt');


        $output = curl_exec($ch);

        curl_close( $ch );

        return $output;
    }

    function parseContent()
    {
        require_once 'helpers/simple_html_dom.php';

        $messages = countMessages();
        $countPages = $messages['total'] / $messages['perPage'];

echo $countPages;
exit;

        for ($i = 1; $i <= 15; $i++) {

            $content = authMailRu($i);

            $subjectsMail = array();

            if ($content) {
                $html = str_get_html($content);



                if ($html->innertext != '' and count($html->find("span.messageline__subject"))) {

                    foreach ($html->find('span.messageline__subject') as $theme) {

                        array_push($subjectsMail, $theme->plaintext);
                    }

                }
            }
        }

        return $subjectsMail;
    }

    function countMessages()
    {
        require_once 'helpers/simple_html_dom.php';

        $content = authMailRu(1);

        $totalAndMessagesPerPage = array();

        if ($content) {

            $html = str_get_html($content);

            if ($html->innertext != '' and count($html->find("span.msglist-title__counter"))) {

                $spanElements = $html->find("span.msglist-title__counter");
                $numberOfMessagesPerPage = count($html->find("table.msglist tr.js-messageline"));
                $totalAndMessagesPerPage['total'] = $spanElements[0];
                $totalAndMessagesPerPage['perPage'] = $numberOfMessagesPerPage;


            }
        }



        return $totalAndMessagesPerPage;


    }

    $subjectsMail = parseContent();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Темы писем</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <script type="text/javascript" src="js/bootstrap.js"></script>

</head>
<body>
    <div class="container bg-faded content-center">
        <div class="row">
            <div class="col-8 mx-auto mb-5">
            </div>
            <div class="col-8 mx-auto text-left">
                <?php if( !empty( $subjectsMail )) { ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>subject</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ( $subjectsMail as $key => $subject ) {
                                if( !empty( $subject )) { ?>
                                <tr>
                                    <td><? echo $key ?></td>
                                    <td><? echo $subject ?></td>
                                </tr>
                                <?} else { ?> <p>Результатов не найдено.</p><?
                            }}?>
                            </tbody>
                        </table>
                    </div>

                <? } ?>

            </div>
        </div>
    </div>
</body>
</html>