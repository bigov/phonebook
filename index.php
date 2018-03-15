<?php

if( !isset( $phones_open )) {
    $phones_open = true;
    require_once 'dir_def.php';
    require( INC_DIR . 'conf.php' );
    require_once( INC_DIR . 'func.php' );
    set_constants();
    spl_autoload_register( 'autoload_class' );
    unregister_GLOBALS();
	
    switch ( MODE ) {
      case 'view':
      	$book = new Viewer();
        break;
			case 'operator':
        $book = new Control();
        break;
			case 'job':
        $book = new Jobs();
        break;
      case 'units':
        $book = new Units();
        break;
      case 'employer':
        $book = new Emps();
        break;
	default:
        $book = new Viewer();
    }
    $book->show();
}

