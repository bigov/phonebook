<?php

class Control extends Db {

    public function show() {

        switch ( FUNC ) {
            case 'status':
                $this->__status();
                exit();
            case 'activate':
                $this->__activate();
                exit();
            case 'setup':
                $this->__setup();
                exit();
            case 'fotos':
                $this->__fotos();
                exit();
            default:
                $this->__status();
            exit();
        }

    }

    /**
     * Отображение информации о наличии фотографий сотрудников
     */
    protected function __fotos() {
    $smarty = $this->render;
    $units_tree = $this->get_units_tree();

    // Массив всех подразделений
  $units = $units_tree[0]['branch'];
  unset($units_tree);

  // Заполнение массива (списка пустых) фамилиями записей у каторых нет фото
  $units = $this->fill_emptys($units);
  // Удаление из вывода отделов, в которых нет списка пустых
  $units = $this->unset_miss_emptys($units);
  // Повторный проход для удаления из вывода блоков/групп/отделов, в которых
  //нет списка пустых.
  $units = $this->unset_miss_emptys($units);
  //  err($units);

    $smarty->assign( 'units', $units );
    $smarty->assign( 'maket', 'fotos_list.htpl' );
    $smarty->display( 'default.htpl' );

    exit();
   }

    /**
     * Для уменьшения объема выводимых данных на странице проверки наличия
     * фотографий удалить из массива все элементы (названия отделов), у
     * которых нет подчиненных ветвей и нет списка отсутствия фотографий.
     *
     * //@param unknown $units
     */
    private function unset_miss_emptys($units_array) {
        reset($units_array);
  while (list($unitid, $unit) = each($units_array)){
            if (isset($unit)) {
                if (is_array($unit)
                && !isset($unit['emptys'])
                && !isset($unit['branch']) ) {
                    unset($units_array[$unitid]);
    }
            }
            if (isset($unit['branch'])) {
                $units_array[$unitid]['branch'] =
    $this->unset_miss_emptys($unit['branch']);

    if (count($units_array[$unitid]['branch']) == 0) {
                    unset($units_array[$unitid]['branch']);
    }
            }
  }
    return $units_array;
    }

   /**
    * Для массива подразделений проверяет есть или нет файлы фотографий
    * сотрудников подразделения. Если фотографий нет, то к подразделению
    * дописывается массив с id и именами сотрудников у которых отсутствуют
    * фотографии.
    *
    * @param array $units_array
    */
   private function fill_emptys($units_array) {

    reset($units_array);
    while (list($unitid, $unit) = each($units_array))
    {
      // Получить список сотрудников
      $jobs_array = $this->get_jobs_array($unitid);
      $emptys = array();

      // Для каждого сотрудника проверить наличие файла фотографии
      while (list(, $name_array) = each($jobs_array)) {
        // пропустить вакантные должности
        if (!isset($name_array['id'])) continue;

        $filename = ABSPATH . PHOTOS . 'ph' . $name_array['id'] . '.jpg';
        if (!file_exists($filename)) {
          $emptys[ $name_array['id'] ] = $name_array['name'];
        }
      }

      if (count($emptys) > 0) {
        $units_array[$unitid]['emptys'] = $emptys;
      }

      unset($emptys);

      // Если есть дочерние ветви - рекурсивно пройти
      if (isset($unit['branch'])) {
        $units_array[$unitid]['branch'] = $this->fill_emptys($unit['branch']);
      }
    }
    return $units_array;
   }

   /**
    * Страница настройки
    */
   protected function __status() {
      $smarty = $this->render;
      $rows = $this->get_names_mod();
      $smarty->assign( 'names', $rows );
      unset($rows);
      $rows = $this->get_jobs_mod();
      $smarty->assign( 'jobs', $rows );
      unset($rows);
      $smarty->assign( 'maket', 'settings.htpl' );
      $smarty->display( 'default.htpl' );
      exit();
   }

    /**
     *  Настройка активации рабочего места
     */
    private function __setup() {
  if( empty( $this->http[ $this->operator['mpd']] )){
            header( 'Location: ' . ROOTURL . 'employer/404/' );
            exit();
    }

    $mail_to = $this->http[ $this->operator['mpd']];
    $this->activate_operator( $mail_to );
    header( 'Location: ' . ROOTURL . 'operator/status/' );
    exit();
    }
}
