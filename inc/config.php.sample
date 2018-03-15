<?php

/**
 * Обращение к файлу происходит в первый раз при инициализации окружения. При
 * последующих обращениях благодаря конструкции "if()" инициализации констант
 * больше не происходит, а передаются значения переменных.
 *
 */

if (!defined('INITED'))
{ // Первичная инициализация приложения
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  define( 'ENCODING', 'UTF-8' );
  mb_internal_encoding( ENCODING );
  define( 'PHOTOS', 'photos' . DS );
  define( 'HOSTNAME', $_SERVER["HTTP_HOST"] );
  define( 'MAX_LEVEL', 51 );
  define( 'DEFAULT_MODE', 'job');
  define( 'DEFAULT_FUNC', 'error');
  define( 'DEFAULT_PODR', '711');
  define( 'INITED', true);
} else
{ // Последующие обращения к параметрам конфигурации
  $dbhost="localhost";
  $dbname="db-user-name";
  $dbuser="db-name";
  $dbpass="db-password";
}
