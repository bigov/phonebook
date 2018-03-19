<?php

// режим редактирования данных должностей
class Units extends Db {

    public function show() {
        if ( $this->operator['level'] < 50 ) {
            $this->msg404();
            exit();
    }
      switch ( FUNC ) {
        case 'list':
          $this->units_list();
          exit();
        case 'new':
            $this->units_new();
            exit();
        case 'newbranch':
            $this->units_newbranch();
            exit();
         case 'newsave':
            $this->units_newsave();
            exit();
         case 'edit':
            $this->units_edit();
            exit();
         case 'editcheck':
            $this->units_editcheck();
            exit();
         case 'editsave':
            $this->unit_backup(array('opid'  => $this->operator['opid'],
                                     'opip'  => OPERATOR_IP,
                                     'unitid'=> $this->http['unitid'] ));
            $this->units_editsave();
            exit();
         case 'move':
            $this->units_move();
            exit();
         case 'movecheck':
            $this->units_movecheck();
            exit();
         case 'movesave':
            $this->units_movesave();
            exit();
         case 'deletecheck':
            $this->units_deletecheck();
            exit();
         case 'deletesave':
            $this->units_deletesave();
            exit();
         case 'addjob':
          $url = ROOTURL . 'job/new/unitid/' . $this->http['unitid'];
          header( 'Location: ' . $url );
            exit();
         default:
            $this->msg404();
            exit();
      }
   }

  protected function units_list() {
  /**
   * Отображение списка подразделений для выбора операций редактирования
   */
    $smarty = $this->render;
    $smarty->assign( 'units', $this->get_units_tree() );
    $smarty->assign( 'maket', 'units_list.htpl' );
    $smarty->display( 'default.htpl' );

      exit();
    }

    /**
     * Отображение формы создания нового подразделения.
     */
    protected function units_new() {
    $unitid = $this->http['unitid'];
    $smarty = $this->render;
        $smarty->assign( 'parent', $unitid );
        $smarty->assign( 'path', $this->get_path_unit( $unitid ));
        $smarty->assign( 'maket', 'unitnew.htpl' );
        $smarty->display( 'default.htpl' );
        exit();
   }

    /**
     * Отображение формы  создания новой структуры.
     */
    protected function units_newbranch() {
    $unitid = 0; //$this->http['unitid'];
    $smarty = $this->render;
        $smarty->assign( 'parent', $unitid );
        $smarty->assign( 'path', $this->get_path_unit( $unitid ));
        $smarty->assign( 'maket', 'branchnew.htpl' );
        $smarty->display( 'default.htpl' );
        exit();
   }

    /**
     *  запись новой должности в справочник
     */
    protected function units_newsave() {
        $new_unitid = $this->unit_insert(
            array('unit'  => $this->http['unit'],
                  'parent'=> $this->http['parent'],
                  'order' => $this->http['order'] ));
        $url = ROOTURL . 'units/edit/unitid/' . $new_unitid;
  header( 'Location: ' . $url );
  exit();
    }

    /**
     * Страница с формой для внесения изменений в справочник.
     */
    protected function units_edit() {
        $smarty = $this->render;

    if ( !$this->mods_clear() ) {
            $smarty->assign( 'maket', 'edit_denied.htpl' );
            $smarty->display( 'default.htpl' );
            exit();
    }

  $unit_rec = $this->get_unit( $this->http['unitid'] );
  $unit_rec['path']  = $this->get_path_unit( $unit_rec['parent'] );

  $jobs = $this->get_jobs_array($this->http['unitid']);

        $smarty->assign( 'unit_rec', $unit_rec );
        $smarty->assign( 'jobs', $jobs );
        $smarty->assign( 'maket', 'unitedit.htpl' );
        $smarty->display( 'default.htpl' );
        exit();
    }

    protected function units_editcheck() {
    /**
     * Форма проверки корректировки должности
     */

    $form_data = $this->http;
    $form_data = array_merge( $form_data, $this->get_unit( $form_data['unitid'] ));

    $smarty = $this->render;
    $smarty->assign( 'form_data', $form_data );
    $smarty->assign( 'maket', 'uniteditcheck.htpl' );
    $smarty->display( 'default.htpl' );
    exit();

   }

    /**
     *   запись информации в справочник
     */
    protected function units_editsave() {
        $url = ROOTURL . 'view/units/unitid/' . $this->http['unitid'];
        $this->unit_update(array('unit'  => $this->http['unit'],
                                  'parent'=> $this->http['parent'],
                                  'order' => $this->http['order'],
                                  'unitid'=> $this->http['unitid']));
    header( 'Location: ' . $url );
    exit();
    }

    protected function units_move() {

    $smarty = $this->render;
    $smarty->assign( 'move', $this->http );

    $smarty->assign( 'path', $this->get_path_unit( $this->http['new_parent'] ));
    $smarty->assign( 'units', $this->get_units_tree() );
    $smarty->assign( 'maket', 'unit_move.htpl' );
    $smarty->display( 'default.htpl' );

    exit();
    }

    /**
     * Форма проверки корректировки должности
     */
    protected function units_movecheck() {

        $form_data = $this->http;
    $form_data = array_merge( $form_data, $this->get_unit( $form_data['unitid'] ));

  $smarty = $this->render;
    $smarty->assign( 'path_old', $this->get_path_unit( $form_data['parent']) );
    $smarty->assign( 'path_new', $this->get_path_unit( $form_data['moveto']) );
    $smarty->assign( 'form_data', $form_data );
    $smarty->assign( 'maket', 'unitmovecheck.htpl' );
    $smarty->display( 'default.htpl' );
    exit();

   }

   /**
    * Подтверждение удаления должности
    */
   protected function units_deletecheck() {
  $unitid = $this->http['unitid'];
    $smarty = $this->render;

  if( $this->unit_isempty( $unitid )) {
    $smarty->assign( 'maket', 'unitdeletecheck.htpl' );
  } else {
    $smarty->assign( 'maket', 'unitnotempty.htpl' );
  }

    $smarty->assign( 'unitid', $unitid );
    $smarty->assign( 'path', $this->get_path_unit( $unitid ));
    $smarty->display( 'default.htpl' );
    exit();

   }

    /**
     * Удалить должность
     */
    protected function units_deletesave() {
        $unit = $this->get_unit( $this->http['unitid'] );
  $parentid = $unit['parent'];
    $url = ROOTURL . 'view/units/unitid/' . $parentid;
        $this->unit_backup(array('opid'  => $this->operator['opid'],
                                 'opip'  => OPERATOR_IP,
                                 'unitid'=> $this->http['unitid'] ));
    $this->unit_delete( $this->http['unitid'] );
  header( 'Location: ' . $url );
  exit();
   }
}
