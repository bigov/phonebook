<?php

/**
 *   редактирование сотрудников
 */
class Emps extends Db {
    
    public function show() {
        switch ( FUNC ) {
   	    case 'new':
                $this->emp_new();
                exit();
            case 'newcheck':
                $this->emp_newcheck();
                exit();
            case 'newsave':
                $this->emp_newsave();
                exit();
            case 'edit':
                $this->emp_edit();
                exit();
            case 'editcheck':
                $this->emp_editcheck();
                exit();
            case 'editsave':
                $this->emp_editsave();
                exit();
            case 'photopost':
                $this->emp_photopost();
                exit();
            case 'photocheck':
                $this->emp_photocheck();
           	exit();
            case 'photosave':
           	$this->emp_photosave();
           	exit();
            case 'move':
                $this->emp_move();
                exit();
            case 'movecheck':
                $this->emp_movecheck();
                exit();
            case 'movesave':
                $this->emp_movesave();
                exit();
            case 'deletecheck':
                $this->emp_deletecheck();
                exit();
            case 'delete':
                $this->emp_deletesave();
                exit();
            default:
         	$this->msg404();
         	exit();
        }
    }
   
   /**
    * Форма добавления сотрудника на должность
    * 
    *  @var $smarty - объект Smarty
    */
   protected function emp_new() {
      $form_data = $this->http;
      $smarty = $this->render;
      $smarty->assign( 'form_data', $form_data );
      $smarty->assign( 'vacancys', $this->get_vacancys() );
      $smarty->assign( 'maket', 'empnew.htpl' );
      $smarty->display( 'default.htpl' );
      exit();
   }

   /**
    *  Проверка данных перед добавлением новой записи  в справочник.
    */
   protected function emp_newcheck() {
   	$form_data = $this->http;
   	
   	$form_data['mode'] = 'employer';
   	$form_data['func'] = 'save';
	
	if ( !empty( $form_data['nmid'])) {
	$form_data = array_merge( $form_data, 
			$this->get_names_mod( $form_data['nmid'] ));
	}
   	
   	$form_data = array_merge( $form_data,
   			$this->get_job( $form_data['new_jobid'] ));
   	
   	$form_data['path'] = $this->get_path_unit( $form_data['unitid'] );
   
   	$smarty = $this->render;
   
   	// проверить имя сотрудника на уникальность
   	if( $this->employer_exist( $form_data['new_name'] )) {
   		$smarty->assign( 'maket', 'employerexist.htpl' );
   	} else {
   		$smarty->assign( 'maket', 'empnewcheck.htpl' );
   	}
   	
   	$smarty->assign( 'form_data', $form_data );
   	$smarty->display( 'default.htpl' );
   	return;
   }
   
    /**
     *  сохранение нового сотрудника в базе данных
     */
    protected function emp_newsave() {
        $url = ROOTURL . 'view/units/unitid/' . $this->http['unitid'];
   	
   	if ( $this->operator['level'] > 3 ) {
            $this->check_admin_level();
            $id = $this->name_add(
                array('jobid' => $this->http['jobid'],
                      'name'  => $this->http['name'],
                      'phones'=> $this->http['phones'],
                      'email' => $this->http['email']));
            $this->name_backup(
                array('id' => $id,
                      'ip' => OPERATOR_IP,
                      'opid'=>$this->operator['opid'] ));
        } else {
            $this->name_addmod(array(
                'jobid' => $this->http['jobid'],
                'name'  => $this->http['name'],
                'phones'=> $this->http['phones'],
                'email' => $this->http['email'],
                'opid'  => $this->operator['opid']));
            $this->save_ok($url);
        }
        header( 'Location: ' . $url );
   }
   
    /**
     *  внесение изменений в справочник. 
     */ 
    protected function emp_edit() {
        $smarty = $this->render;
   	
        // Если кто-то уже предложил изменить указанного сотрудника - выйти
      	if ( !$this->mods_clear() ) {
            $smarty->assign( 'maket', 'edit_denied.htpl' );
            $smarty->display( 'default.htpl' );
            exit();
	}
	
        $form_data = $this->get_employer( $this->http['id'] );
      	$smarty->assign( 'form_data', $form_data );
       	$smarty->assign( 'maket', 'empedit.htpl' );
       	$smarty->display( 'default.htpl' );
       	exit();
   }
   
   protected function emp_photopost() {
   	/**
   	 * Выбор новой фотографии сотрудника.
   	 * 
   	 */
   	$smarty = $this->render;
   	 
   	// Если кто-то предложил изменить указанного сотрудника - выйти
   	if ( !$this->mods_clear() ) {
   		$smarty->assign( 'maket', 'edit_denied.htpl' );
   		$smarty->display( 'default.htpl' );
   		exit();
   	}
   	$form_data = $this->get_employer( $this->http['id'] );
   	$smarty->assign( 'form_data', $form_data );
   	$smarty->assign( 'maket', 'photopost.htpl' );
   	$smarty->display( 'default.htpl' );
   	exit();
   }
   
   protected function emp_photocheck() {
   /**
    * Получение нового файла с фотографией сотрудника
    * 
    */

   	$err_msg = false;
   	// Куда и с каким именем загружать файл
   	
   	$uploadfile = ABSPATH . "photos". DS . "new". DS; 
   	
   	if ( !is_writable( $uploadfile )) {
   		$err_msg = "Нет доступа для записи в каталог $uploadfile.";
   	}
   	$uploadfile .= "ph" . $this->http['id'] . ".jpg";
   	
   	if ( !is_uploaded_file($_FILES['userfile']['tmp_name'])) {
   		$err_msg = "Загрузка была прервана.";
   	}
   		 
   	if( $_FILES['userfile']['size'] > 110000 ){
   		$err_msg = "Размер файла не должен превышать 100Кб.";
   	}

   	if( !preg_match("/.jpg$/i", $_FILES['userfile']['name'])){
   		$err_msg = "Принимаются только файлы *.jpg.";
   	}   	
   	
   	// проверка размера изображения
   	$size = getimagesize( $_FILES['userfile']['tmp_name'] );
   	if ($size[0] != 150 OR $size[1] != 150)
   	{
   		$err_msg = "Размер изображения должен быть 150х150 пикселей.";
   	}
   	
   	if ($err_msg) die( $err_msg );
   	
   	move_uploaded_file( $_FILES['userfile']['tmp_name'], $uploadfile );
  	 
  	$smarty = $this->render;
  	$form_data = $this->get_employer( $this->http['id'] );
  	$smarty->assign( 'form_data', $form_data );
  	$smarty->assign( 'maket', 'photocheck.htpl' );
  	$smarty->display( 'default.htpl' );
  	exit();
  	 
   }

   protected function emp_photosave() {
   	/**
   	 *  Установка новой фотографии
   	 */
   	
   	$photo_dir = ABSPATH . "photos". DS; 
   	
   	$employer = $this->get_employer( $this->http['id'] );
   	$url = ROOTURL . 'view/units/unitid/' . $employer['unitid'];
   	
   	// новое фото
   	$img_new =  $photo_dir . "new". DS . "ph" . $employer['id'] . ".jpg";
   	// старое фото
   	$img_old =  $photo_dir . "ph" . $employer['id'] . ".jpg";
   	
   	// если фото не было - сразу разместим
   	if ( !file_exists( $img_old )) {
   		copy( $img_new, $img_old );
   		unlink( $img_new );
   		header( 'Location: ' . $url );
   		exit();
   	} else {
   		// Затрахали глюки - пусть меняют сразу
   		unlink( $img_old );
   		copy( $img_new, $img_old );
   		unlink( $img_new );
   		header( 'Location: ' . $url );
   		exit();
   	}
   	
   	// если же фото было, то вначале проверим права доступа
   	if ( $this->operator['level'] > 3 ) {
   		
   		// сохраним резервную копию
   		copy( $img_old, $photo_dir . "old" . DS . "ph" . $employer['id'] . "-" . microtime(true) . ".jpg" );
   		
   		// заменим фото
   		copy( $img_new, $img_old );

   		// удалим загруженый файл
   		unlink( $img_new );
   	
   	} else {
   		$this->save_ok($url);
   	}
   	
   	header( 'Location: ' . $url );
   	exit();
   
   }
   
   /**
    *  Страница с формой для проверки данных перед внесением изменений
    * 
    */ 
    protected function emp_editcheck() {
        $form_data = $this->http;
        if( !empty( $form_data['nmid'] )) {
        	$arr = $this->get_names_mod( $form_data['nmid'] );
            $form_data = array_merge( $form_data, $arr);
            unset($arr);
        }
        $form_data = array_merge( $form_data, $this->get_employer( $form_data['id']));
        if( empty( $form_data['new_job'] )) {
            $new_job = $this->get_job( $form_data['new_jobid'] );
            $form_data['new_unitid'] = $new_job['unitid']; 
            $form_data['new_kab'] = $new_job['kab'];
            $form_data['new_job'] = $new_job['job'];
            $form_data['new_phone'] = $new_job['phone'];
            $form_data['new_fax'] = $new_job['fax'];
            $form_data['new_order'] = $new_job['order'];
            $form_data['new_anonid'] = $new_job['anonid'];
        }
      
        $smarty = $this->render;
      
        // Если меняется имя сотрудника - необходимо проверить его уникальность
        if(( $form_data['name'] != $form_data['new_name'] ) and  
            ( $this->employer_exist( $form_data['new_name'] ))) {
      	
            $smarty->assign( 'form_data', $form_data );
            $smarty->assign( 'maket', 'employerexist.htpl' );
        } else {
            $smarty->assign( 'form_data', $form_data );
            $smarty->assign( 'maket', 'empeditcheck.htpl' );
        }
            $smarty->display( 'default.htpl' );
            return;
    }

    /**
     *  запись информации в справочник
     */
    protected function emp_editsave() {
        $url = ROOTURL . 'view/units/unitid/' . $this->http['unitid'];
      
   	if ( $this->operator['level'] > 3 ) {
            $this->check_admin_level();
            $this->name_backup(array( 
                'id' => $this->http['id'],
                'ip' => OPERATOR_IP,
                'opid'=>$this->operator['opid'] ));
            $this->name_update(
                array('jobid' => $this->http['jobid'],
                      'name'  => $this->http['name'],
                      'phones'=> $this->http['phones'],
                      'email' => $this->http['email'],
                      'id'    => $this->http['id']));
   	} else {
            $this->name_modedit(
                array('id'    => $this->http['id'],
                      'jobid' => $this->http['jobid'],
                      'name'  => $this->http['name'],
                      'phones'=> $this->http['phones'],
                      'email' => $this->http['email'],
                      'opid'  => $this->operator['opid']) );
            $this->save_ok($url);
        }
        header( 'Location: ' . $url );
        exit();
    }

   protected function emp_move() {
   /**
      Перевод сотрудника на новую должность 
      Страница с формой выбора новой должности 
    */ 
   	  
      $form_data = $this->http;
   	  $form_data = array_merge( $form_data, 
   	  		$this->get_employer( $form_data['id'] ));
      
      $smarty = $this->render;
      $smarty->assign( 'vacancys', $this->get_vacancys() );
      $smarty->assign( 'form_data', $form_data );
      $smarty->assign( 'maket', 'form_selectjob.htpl' );
      $smarty->display( 'default.htpl' );
      exit();
   }

   protected function emp_movecheck() {
   /**
    *  Визуальная проверка данных перед внесением изменений.
    */
   	
   	  $form_data = $this->http;
   	  $form_data = array_merge( $form_data, 
   	  		$this->get_employer( $form_data['id'] ));
   	  $form_data['mode'] = 'employer';
   	  $form_data['func'] = 'movesave';
   	  
   	  $new_job = $this->get_job( $this->http['new_jobid'] );
   	  $form_data['new_path'] = $new_job['path'];
   	  $form_data['new_job'] = $new_job['job'];
   	  $form_data['new_jobid'] = $new_job['jobid'];
   	  $form_data['new_unitid'] = $new_job['unitid'];
   	  
   	  $smarty = $this->render;
   	  $smarty->assign( 'form_data', $form_data );
   	  $smarty->assign( 'maket', 'movecheck.htpl' );
   	  $smarty->display( 'default.htpl' );
   	  exit();
   }
   
    /**
     * запись информации в справочник
     */
    protected function emp_movesave() {
        $url = ROOTURL . 'view/units/unitid/';
   	  
        if( $this->operator['level'] > 3 ) {
            $this->check_admin_level();
            $this->name_backup( array( 
                'id' => $this->http['id'],
                'ip' => OPERATOR_IP,
                'opid'=>$this->operator['opid'] ));
         $this->name_move( array (
                'id' => $this->http['id'],
                'jobid' => $this->http['jobid']));
      	 header( 'Location: ' . $url . $this->http['new_unitid'] );
   	  } else {
      	 $this->name_modedit();
         $this->save_ok( $url . $this->http['unitid'] );
      }
      exit();
   }
   
   protected function emp_deletecheck() {
      /**
         Страница с формой для проверки данных перед
         удалением сотрудника из базы данных.
       */
      $form_data = $this->http;
      if( !empty( $this->http['nmid'] )) {
      	$form_data = $this->get_names_mod( $this->http['nmid'] );
      }
      
      //$form_data = $form_data[0];
      
   	  $form_data = array_merge( $form_data, 
   	  		$this->get_employer( $form_data['id'] ));
      
   	  $form_data['func'] = 'deletesave';
   
      $smarty = $this->render;
      $smarty->assign( 'form_data', $form_data );
      $smarty->assign( 'maket','empdeletecheck.htpl' );
      $smarty->display( 'default.htpl' );
      return;
   }
   
    /**
     * Удаление сотрудника из Справочника
     */
    protected function emp_deletesave() {
        $url = ROOTURL . 'view/units/unitid/' . $this->http['unitid'];
   	if ( $this->operator['level'] > 3 ) {
            $this->check_admin_level();
            $this->name_backup( array( 
                'id'  => $this->http['id'],
                'ip'  => OPERATOR_IP,
                'opid'=> $this->operator['opid'] ));
                 
            $this->name_delete( $this->http['id'] );
        } else {
            $empl = $this->get_employer($this->http['id']);
            $this->name_modelete(
                array('id'    => $this->http['id'],
                      'jobid' => $empl['jobid'],
                      'name'  => $empl['name'],
                      'phones'=> $empl['phones'],
                      'email' => $empl['email'],
                      'opid'  => $this->operator['opid']));
            $this->save_ok($url);
        }
        header( 'Location: ' . $url );
    }
}

