<?php namespace inc;

class database {

    use dbsqlite3;
    use rendersmarty;

    protected $separator_unit = ' » '; // Разделитель названий в должности
    protected $operator;

    public $http;
    public $page_num;           // номер текущей страницы справочника
    public $maxnames = 64;      // Число строк фамилий на развороте
    public $maxrows = 24;       // Число строк в результатах поиска
    public $render;

    public function __construct() {

        global $request;
        $this->http = $request;
        $this->page_num = $this->http['page'];

        // Инициализация сессии пользователя
        $this->detect_operator();

        // Инициализация шаблона
        $this->render_init();

        // Очистка старых записей
        $this->clear_old_rows();
    }

    /**
     * Создание древовидного списка всех организаций и подразделений
     */
    protected function get_units_tree( $id = 0 ) {
    $full_list = $this->get_branch_assoc( $id );

    foreach( $full_list as $key => $branch ) {
            $sub_list = $this->get_units_tree( $branch['unitid'] );
            if ( count( $sub_list ) > 0 )
      $full_list[$key]['branch'] = $sub_list;
        }
        if ( $id == 0 ) {
          // На выходе из рекурсии добавим в массив иерархии корень дерева
          return array ( 0 =>
          array( 'unitid' => 0,
            //'unit' => ' корень "дерева" - не отображается',
            'order' => 0,
            'branch' => $full_list ));
        }
        return $full_list;
    }

  /**
   * Создание полного списка справочника (для экспорта)
   */
  protected function make_full_list( $unitid = 1 )
  {
    $full_list = $this->get_branch_assoc( $unitid );

    foreach( $full_list as $key => $branch )
    {
      $sub_list = $this->make_full_list( $branch['unitid'] );
      if ( count( $sub_list ) > 0 )
      {
        $full_list[$key]['branch'] = $sub_list;
      }
      $jobs = $this->get_jobs_array( $branch['unitid'] );
      if ( count( $jobs ) > 0 )
      {
        $full_list[$key]['jobs'] = $jobs;
      }
    }

    return $full_list;
    }

    /**
     * Очитска базы данных от устаревших записей
     */
    private function clear_old_rows() {
        $this->jobs_history_clear();
        $this->names_history_clear();
        $this->jobs_mod_clear();
        $this->names_mod_clear();
        $this->operators_clear();
    }

    /**
     *  Если производится административное согласование, то
     *  следует изменить уровень предложившего изменения
     */
    protected function check_admin_level() {
        $url = ROOTURL . 'view/units/unitid/' . $this->http['unitid'];
        if( !empty( $this->http['opid']) ) {

            $this->update_operator_level();
            $this->clear_mods();

            if( $this->http['level']=='dn') {
                header( 'Location: ' . $url );
                exit();
            }
        }
        return;
    }

    /**
     * Очистка таблиц модификаций после проверки предложеных изменений
     */
    private function clear_mods() {
        if( !empty( $this->http['id'] )) {
            $this->delete_names_mods($this->http['id']);
        }
        if( !empty( $this->http['jobid'] )) {
            $this->delete_jobs_mods($this->http['jobid']);
        }
        return TRUE;
    }

    protected function update_operator_level() {
        $operator = $this->http['opid'];
        $ism = $this->http['level'];
        if( $ism=='up' ) {$this->operator_up( $operator );}
        if( $ism=='dn' ) {$this->operator_dn( $operator );}
        return TRUE;
    }

    /**
     * Проверка наличия корректировок в буферных таблицах. Проверка
     * необходима для того, чтобы запретить повторное редактирование записей,
     * пока имеются не одобренные корректировки этой записи в буфере.
     */
    protected function mods_clear() {
        if( !empty( $this->http['id'] )) {
            $id = (int)$this->http['id'];
            if( $this->exist_name_mod( $id ) ){ return false;}
        }
        if( !empty( $this->http['jobid'] )) {
            $jobid = (int)$this->http['jobid'];
            if( $this->exist_job_mod( $jobid ) ){ return false;}
        }
        return true;
    }

}
