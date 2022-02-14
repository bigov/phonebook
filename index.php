<?php
declare(encoding='UTF-8');

// -------------------- configure section here ------------------- //
define( 'DBNAME', "db/phones.sqlite");  // файл базы данных
define( 'DEFAULT_PODR', '2');           // индекс подразделения по-умолчанию
define( 'MAX_LEVEL', 51 );
define( 'ENCODING', 'UTF-8' );

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
// -------------------- end configure section -------------------- //

$hostname = 'localhost';
if (isset($_SERVER['HTTP_HOST'])) $hostname = $_SERVER['HTTP_HOST']; 
define( 'DS', DIRECTORY_SEPARATOR );
define( 'ABSPATH', dirname(__FILE__) . DS );
define( 'INC_DIR', ABSPATH . 'inc' . DS);
mb_internal_encoding( ENCODING );
define( 'PHOTOS', 'photos' . DS );
define( 'HOSTNAME', $hostname );
define( 'DEFAULT_MODE', 'job');
define( 'DEFAULT_FUNC', 'error');

require_once( INC_DIR . 'func.php' );
\spl_autoload_register();
inc\set_constants();

    switch ( MODE ) {
      case 'view':
        $book = new inc\viewer();
        break;
	case 'operator':
        $book = new inc\control();
        break;
	case 'job':
	    $book = new inc\jobs();
        break;
      case 'units':
        $book = new inc\units();
        break;
      case 'employer':
        $book = new inc\employees();
        break;
	default:
        $book = new inc\viewer();
    }
    $book->show();
