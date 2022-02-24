<?php
declare(encoding='UTF-8');
$cfg = 'db/config.php';
if(!file_exists($cfg)) copy("config.php.init", $cfg);
require_once( $cfg );
define( 'ABSPATH', dirname(__FILE__) . DS );
define( 'INC_DIR', ABSPATH . 'inc' . DS);
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
