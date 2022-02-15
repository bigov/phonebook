<?php namespace inc;

//global $request;

/**
 * Установка констант, используемых в работе классов и рендеринга 
 */
function set_constants()
{
	/* Настройка констант текущего оператора
	 */
	$string = md5( $_SERVER["HTTP_USER_AGENT"] . $_SERVER["HTTP_ACCEPT"]
			. $_SERVER["HTTP_ACCEPT_LANGUAGE"] . $_SERVER["REMOTE_ADDR"] );
	define( 'OPERATOR_MPD', $string);
	define( 'OPERATOR_IP', $_SERVER["REMOTE_ADDR"]);
	
	$_dirs = explode("/", urldecode( $_SERVER["SCRIPT_NAME"] )) ;
	$root_folder = "/" . $_dirs[1] . "/"; // ="/phones/"

  define( 'ROOTURL', 'http://' . HOSTNAME . $root_folder );


	global $request;
	$request = array();
	foreach ( $_POST as $key => $val ) $request[ $key ] = $val;
	$url_string = preg_replace( "~$root_folder~", '', urldecode( $_SERVER["REQUEST_URI"] ));
	$url_string = preg_replace( '~/?[^/]*\?[^/]*~', '', $url_string );
	$url_params = explode( '/', $url_string );
	if ( empty( $url_params[0] )) array_shift( $url_params );
	
	define( 'MODE', array_shift( $url_params )); // режим
	
	if ( empty( $request['func'] )) {
		define( 'FUNC', array_shift( $url_params )); // действие
	} else {
		define( 'FUNC', $request['func'] ); // действие
		array_shift( $url_params );
	}
	
	while ( $key = array_shift( $url_params )) {
		$request[ $key ] = array_shift( $url_params );
	}

   	if( empty( $request['page'] )) {
        $request['page'] = 0;
    }
    
	return;
}

/**
 * Регистратор автоматической загрузки классов
 */
function autoload_class($name) {

    $classes_dir = 'inc';		// папка размещения классов
    $classname_prefix = 'class-';	// начало имени файлов классов
    $classname_ext = '.php';		// расширение имени
	
    $classname = ABSPATH . $classes_dir . DIRECTORY_SEPARATOR 
            . $classname_prefix	. strtolower($name) . $classname_ext;

    $tratename = ABSPATH . $classes_dir . DIRECTORY_SEPARATOR
               . strtolower($name) . $classname_ext;
    
    if (file_exists($classname)) {
        include_once( $classname );
        return;
    } elseif (file_exists($tratename)) {
        include_once( $tratename );
        return;
    }

}

/**
 * сервисная функция отладки
 */
function err() {
	
	$a = func_get_args();
	print( '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n"
		. '<html lang="ru">' . "\n"
		. '<head><meta http-equiv="content-type" content="text/html; charset=utf-8" />' . "\n"
		. '</head><body><pre>' . "\n" );
	print( 'debug time: ' . date('Y-m-d H:i:s e' ) . "<br />\n");
	for( $t=0; $t<count($a); $t++ ) {
		if (is_array($a[$t])) {
			print_r($a[$t]);
		} else {
			$a[$t] = (string)$a[$t];
			print( $a[$t] );
		}
		print ("<br />\n");
	}
	print ('</pre></body></html>');
	exit();
	die();
}

/* Формирование алфавитных ярлыков для веб-страниц справочника */
function make_labels(){

	$letters = array ( 'А','Б','В','Г','Д','ЕЖ','ЗИ','К','Л',
			'М','НО','П','Р','С','Т','УФХ','ЦЧ','ШЩ','ЭЮЯ' );

	$max = count( $letters );
	$url= ROOTURL . "view/employers/key/";
	$labels=array();
	for ( $i = 0; $i < $max; $i++ ) {
		$labels[] =	array( 'href'=>$url.$i, 'label'=>$letters[$i] );
	}
	return $labels;
}

function strip_all( $text ) {

	// удалить начальные пробелы
	$text = preg_replace( '/(^\s*)|(\s*$)/', '', $text );

	// удалить лишние пробелы в середине
	$text = preg_replace( '/\s+/', ' ', $text);
	
	// удалить все теги
	$text = strip_tags($text);
	
	return $text;
}

/**
 * Так как функция регистронезависимого поиска в многобайтовой (UTF-8) 
 * кодировке из арсенала PHP глючит, я сделал свой костыль.
 * 
 * Данная функция разбивает строку на части по заданному шаблону.
 * Возвращает массив из двух подстрок в случае успеха.
 * 
 * Если подстрока не найдена - возвращается значение false
 * 
 */
function utf8_spliti_string( $pattern, $utf_string ) {

	// Длина шаблона поиска
	$str_lengh = mb_strlen( $pattern, 'UTF-8' );
	
	// Вначале проверим, не находится ли шаблон в начале строки 
	$substr_z = mb_substr( $utf_string, 0, $str_lengh );
	if ( mb_strtolower( $substr_z, 'UTF-8' ) == 
		 mb_strtolower( $pattern, 'UTF-8' )) 
		return array( '', mb_substr( $utf_string, $str_lengh ) );
	
	// Найдем позицию вхождения шаблона
	$str_start = mb_stripos( $utf_string, $pattern, 0, 'UTF-8' );
	if(!$str_start ) return false; 

	// Начальная часть строки до искомой части
	$substr_a = mb_substr( $utf_string, 0, $str_start );
	// Окончание строки после шаблона
	$substr_b = mb_substr( $utf_string, $str_start + $str_lengh );
	return array( $substr_a, $substr_b );
} 

/**
 * Создает массив ассоциативных массивов из результатов запроса
function mysql_fetch_all( $result ) {
	$return_array = array();
	// До тех пор, пока в результате содержатся ряды, помещаем их в ассоциативный массив.
	// Замечание: если добавить extract($row, EXTR_OVERWRITE); в цикл, вы сделаете
	//            доступными переменные соответствующие названиям индексов
	if (!empty($result))
		while ($row = mysql_fetch_assoc($result)) {
			$return_array[]=$row;
		}
	return $return_array;
}
 */

/**
 * Формирование отображения элемента управления для выбора страниц по
 * номеру, если их в выборке оказалось больше одной
 */
function make_paginator($url, $page, $num_pages) {

	$content = '<ul id="paginator">';

	// Кнопка "НАЗАД"
	$marker_prev = '«';
	if( $page>0 ) {
		$marker_prev = '<a href="'. $url .'page/'. ($page-1) .'/">'
				. $marker_prev . '</a>';
	}
	$content .= '<li>'.$marker_prev.'</li>';

	// Кнопки с номерами страниц
	for( $i=0; $i<$num_pages; $i++ ) {
		$marker_page = $i+1;
		if ($i == $page) {
			$content .= '<li class="active">'.$marker_page.'</li>';
		} else {
			$content .= '<li><a href="' . $url . 'page/' . $i . '/">'. $marker_page .'</a></li>';
		}
	}

	// Кнопка "ВПЕРЕД"
	$marker_next = '»';
	if ( $page < ( $num_pages -1 )) {
		$marker_next = '<a href="'. $url .'page/'. ($page+1) .'/">'
				. $marker_next . '</a>';
	}
	$content .= '<li>'.$marker_next.'</li>';
	$content .= '</ul>' . "\n";

	return $content;
}
