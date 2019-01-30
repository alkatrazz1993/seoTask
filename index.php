<?php

function __autoload($classname)
{
    $filename = $classname . ".php";
    include_once($filename);
}
include_once 'helpers/simple_html_dom.php';

$messages = new Messages();
$subjectsMessages = $messages->getSubjectsMessages();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Темы писем</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <script type="text/javascript" src="js/bootstrap.js"></script>

</head>
<body>

    <div class="col-12 mx-auto mb-3">
        <h1>Темы писем</h1>
    </div>
    <div class="col-12 mx-auto text-left">
        <?php if( !empty( $subjectsMessages )) { ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>subject</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ( $subjectsMessages as $key => $subject ) {
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
</body>
</html>