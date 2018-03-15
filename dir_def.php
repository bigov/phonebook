<?php
/**
 * Задача файла - определить пути размещения и внести их в константы. Константы
 * необходимы для работы двух разных частей справочника - веб и сокет, поэтому
 * данные определения вынесены в отдельный файл.
 * 
 */

define( 'DS', DIRECTORY_SEPARATOR );
define( 'ABSPATH', dirname(__FILE__) . DS );
define( 'INC_DIR', ABSPATH . 'inc' . DS);