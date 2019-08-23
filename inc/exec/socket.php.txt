#!/usr/local/bin/php -q
<?php
error_reporting(E_ALL);

/* Позволяет скрипту ожидать соединения бесконечно. */
set_time_limit(0);

/* Включает скрытое очищение вывода так что мы получаем данные
 * как только они появляются. */
ob_implicit_flush();

$address = 'localhost';
$port = 10080;

define( 'INITED', TRUE);
// ключ для работы через сокет
define( 'OPERATOR_MPD', "bdf60ca61881154386a536493db38a39" );

require_once( '../../dir_def.php' );
require( INC_DIR . 'conf.php' );
require_once( INC_DIR . 'func.php' );
spl_autoload_register( 'autoload_class' );

//Пробуем создать класс DB
$db = new DbRead();

$employer = $db->get_employer(1049);
$es=  serialize($employer);
print_r($es);
echo "\nok\n\n";
exit();

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "Не удалось выполнить socket_create(): причина: " 
        . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "Не удалось выполнить socket_bind(): причина: " 
        . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "Не удалось выполнить socket_listen(): причина: " 
        . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "Не удалось выполнить socket_accept(): причина: " 
            . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Отправляем инструкции. */
    $msg = "\n"
         . "---------------------------------\n"
         . " Консоль управления Справочником \n"
         . "---------------------------------\n\n"
        ."Допустимые команды управления:\n\n"
            . "    exit   отключиться от консоли\n"
            . "    down   выключить сервис консоли\n";
    socket_write($msgsock, $msg, strlen($msg));

    do {
        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
            echo "Не удалось выполнить socket_read(): причина: " 
                . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        if (!$buf = trim($buf)) {
            continue;
        }
        if ($buf == 'exit') {
            break;
        }
        if ($buf == 'down') {
            socket_close($msgsock);
            break 2;
        }
        $talkback = "Вы сказали '$buf'.\n";
        socket_write($msgsock, $talkback, strlen($talkback));
        echo "$buf\n";
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);
