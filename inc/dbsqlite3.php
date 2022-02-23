<?php namespace inc;

/*
 * Трейт c для работы с базой данных MySQL
 *
 */

trait dbsqlite3 {

  private $db = NULL;
  private $last_rows = 0;
  private $last_id = 0;

  /**
   * Закрытие подключения к базе данных
   * @return boolean
   */
  private function db_close()
  {
      if(!is_null($this->db))
      {
          if(!$this->db->close()) 
          {
              err("Не удалось закрыть БД");
              return FALSE;
          }else{
              $this->db = NULL;
              return TRUE;
          }
      }
      err("Попытка закрыть БД без указателя");
      return FALSE;
  }

  private function sqlite_show_err($sql)
  {
    if(!is_null($this->db))
    {
      err($this->db->lastErrorMsg() . " in query=\"" . $sql . "\"");
      $this->db_close();
    }
    exit;
  }

  /**
   * Подключение к указанной в параметре базе. Возвращает линк на базу данных.
   *
   * @param string $name Имя подключаемой базы данных
   */
  private function link()
  {
    if (!is_null($this->db)) return;

    if(!file_exists(\DBNAME))
    {
       err("Database file \"". \DBNAME . "\" not found");
       exit;
    }
    $this->db = new \SQLite3(\DBNAME, SQLITE3_OPEN_READWRITE);
    return;
  }


  protected function num_rows($r)
  {
    if (!is_object($r)) return 0;
    $nrows = 0;
    $r->reset();
    while ($r->fetchArray()) $nrows++;
    $r->reset();
    return $nrows;
  }


  /**
   * Подсчет ТОЛЬКО количества строк, удовлетворяющих условию запроса.
   * Возвращает целое число, БЕЗ ПЕРЕДАЧИ СОДЕРЖИМОГО СТРОК.
   *
   * @param string $query Строка MySQL запроса
   */
  protected function db_num_rows($query)
  {
    if (is_null($this->db)) $this->link();
    if (!$result = $this->db->query($query)) $this->sqlite_show_err($query);
    return $this->last_rows($result);
  }


  /**
   * Выполняет запрос без результата к текущей базе данных
   *
   * @param string $sql Строка MySQL запроса
   */
  protected function sqlite_exec($sql)
  {
      if(READONLY_MODE) return 0;

      if(is_null($this->db)) $this->link();
      if(!$this->db->exec($sql)) $this->sqlite_show_err($sql);
      return TRUE;
  }

  /**
   * Выполняет запрос без результата к текущей базе данных
   *
   * @param string $sql Строка MySQL запроса
   */
  protected function sqlite_insert($sql)
  {
      if(READONLY_MODE) return 0;

      if(is_null($this->db)) $this->link();
      if(!$this->db->exec($sql)) $this->sqlite_show_err($sql);
      $this->last_id = $this->db->lastInsertRowID();
      return $this->last_id;
  }

   /**
    * Выполнение запроса к базе данных на выборку массива
    *
    * @param string $sql Строка MySQL запроса
    */
  protected function sqlite_query_single($sql)
  {
     if(is_null($this->db)) $this->link();
     if (!$result = $this->db->querySingle($sql)) return 0;
     return $result;
  }

   /**
    * Выполнение запроса к базе данных на выборку массива
    *
    * @param string $sql Строка MySQL запроса
    */
  protected function sqlite_query($sql)
  {
     if(is_null($this->db)) $this->link();
     $data = array();

     if ($result = $this->db->query($sql))
     {
         while ($row = $result->fetchArray(SQLITE3_ASSOC)) $data[] = $row;
     }
     else
     {
         $this->sqlite_show_err($sql);
     }
     $this->last_rows = count($data);
     return $data;
  }

    /**
     * Проверка состава параметров запроса. Защита от выполения запросов с
     * неверным количеством параметров, или неправильными параметрами.
     *
     * @param array $params Проверяемый массив параметров
     * @param array $need Массив необходимых имен параметров
     */
    private function check_params($params, $need) {
        if (!is_array($params)) {
            err("ERR: expect array of parameters.");
        }
        if (count($params) < count($need)) {
            err("ERR: expect " . count($need) . " parameters.");
        }
        foreach ($need as $key) {
            if (!array_key_exists($key, $params)) {
                err("ERR: expect parameter name \"$key\".");;
            }
        }
        return TRUE;
    }

    protected function check_name_id( $id ) {
      $sql = "SELECT COUNT(`id`) FROM `names` WHERE `id`='$id';";
      $num = (int) $this->sqlite_query_single($sql);
      if ($num == 1) return true;
      return false;
    }

    /**
     *  Изменение личных данных сотрудника
     */
    protected function name_update( $q ) {
        $this->check_params($q,
            array('jobid', 'name', 'phones', 'email', 'id'));
        if (empty($q['id'])) { return FALSE; }

        $sql = sprintf("UPDATE `names` SET `jobid`=%d, `name`='%s', "
                . "`phones`='%s', `email`='%s' WHERE `id`=%d;",
            $q['jobid'], $q['name'], $q['phones'], $q['email'], $q['id'] );

        $this->sqlite_exec($sql);
        return true;
    }

    /**
     * Добавление в базу данных новой записи о сотруднике
     *
     * @param array $q Ассоциативный массив параметров
     * @return integer
     */
    protected function name_add( $q )
    {
      $this->check_params($q, array('jobid', 'name', 'phones', 'email'));
      $query = sprintf("INSERT INTO `names` "
          ."( `jobid`, `name`, `phones`, `email` ) "
          . "VALUES ( %d, '%s', '%s', '%s' )",
          $q['jobid'], $q['name'], $q['phones'], $q['email'] );
      $this->sqlite_insert($query);
    }

    /**
     *  Регистрация запроса на изменение данных в таблице имен
     */
    protected function name_modedit($q) {
        $this->check_params($q,
            array('id','jobid','name','phones','email','opid'));
        if(empty($q['id'])) { return; }

        $sql = sprintf("INSERT `names_mod` ( `id`,`new_jobid`,`new_name`,"
            . "`new_phones`,`new_email`,`mod`,`opid`,`date` ) VALUES "
            . "(%d, %d, '%s', '%s', '%s', 'edit', %d, date('now'))",
            $q['id'], $q['jobid'], $q['name'], $q['phones'], $q['email'],
            $q['opid']);

        $this->sqlite_insert($sql);
        return true;
    }

    /**
     *  Регистрация запроса на удаление данных в таблице имен
     */
    protected function name_modelete($q) {
        $this->check_params( $q,
            array('id','jobid','name','phones','email','opid'));
        if(empty($q['id'])) { return; }

        $sql = sprintf("INSERT `names_mod` (`id`,`new_jobid`,`new_name`,"
             . "`new_phones`,`new_email`,`mod`,`opid`,`date`) VALUES ("
             . " %d,%d,'%s','%s','%s','delete',%d,NOW())",
            $q['id'], $q['jobid'], $q['name'], $q['phones'], $q['email'],
            $q['opid']);
        $this->db_query_row($sql);
        return true;
    }

    /**
     * Добавление в таблицу запросов нового имени сотрудника
     */
    protected function name_addmod($q) {
        $this->check_params( $q,
            array('jobid', 'name', 'phones', 'email', 'opid'));

        $sql = sprintf("INSERT `names_mod` ( `id`,`new_jobid`, `new_name`,"
                . " `new_phones`, `new_email`, `mod`, `opid`, `date` ) "
                . "VALUES ('0', %d, '%s', '%s', '%s', 'new', %d, NOW() )",
            $q['jobid'], $q['name'], $q['phones'], $q['email'], $q['opid']);

        $this->db_query_row($sql);
        return true;
    }

    /**
     *  Удаление строки из таблицы имен
     */
    protected function name_delete( $id ) {
        if (empty($id)){ err('ERR: name_delete expect ID');}
        $sql = "DELETE FROM `names` WHERE `id`='$id';";
        return $this->sqlite_exec($sql);
    }

    /**
     * Регистрация запроса на изменение данных в таблице должностей
     */
    protected function job_mod($q) {
        $this->check_params($q, array('jobid', 'unitid', 'kab', 'job', 'phone',
              'fax', 'order', 'anonid', 'cmd', 'opid'));
        $sql = sprintf("INSERT `jobs_mod` ( `jobid`, `new_unitid`, `new_kab`,"
             ."`new_job`, `new_phone`, `new_fax`, `new_order`, `new_anonid`,"
             ."`mod`, `opid`, `date` ) VALUES ("
             ." %d, %d, '%s', '%s', '%s', '%s', '%s', %d, '%s', %d, NOW() )",
              $q['jobid'], $q['unitid'], $q['kab'], $q['job'], $q['phone'],
              $q['fax'], $q['order'], $q['anonid'], $q['cmd'], $q['opid']);

        $this->db_query_row($sql);
        return true;
    }

    /**
     * Обновление данных в таблице должностей
     */
    protected function job_update( $q ) {
        $this->check_params($q,
            array('unitid','kab','job','phone','fax','order','anonid','jobid'));

        $sql = sprintf("UPDATE `jobs` SET `unitid`=%d, `kab`='%s', "
                . "`job`='%s', `phone`='%s', `fax`='%s', `order`=%d, "
                . "`anonid`=%d WHERE `jobid`=%d",
            $q['unitid'], $q['kab'], $q['job'], $q['phone'], $q['fax'],
            $q['order'], $q['anonid'], $q['jobid']);

        return $this->sqlite_exec($sql);
    }

    /**
     * Резервное сохранение данных сотрудника перед внесением изменений
     */
    protected function job_backup( $q )
    {
        $this->check_params($q, array('opid', 'opip', 'jobid'));
        $sql = sprintf("INSERT INTO `jobs_history` ( `jobid`, `unitid`, `kab`, `job`, `phone`, "
            . " `fax`, `order`, `anonid`, `opid`, `ip`, `date` ) SELECT `jobid`, "
            . "`unitid`, `kab`, `job`, `phone`, `fax`, `order`, `anonid`, %d, '%s', date('now') "
            . "FROM `jobs` WHERE `jobid`=%d", $q['opid'], $q['opip'], $q['jobid']);
        return $this->sqlite_insert($sql);
    }

    /**
     * Перевод сотрудника на другую должность
     */
    protected function name_move( $q )
    {
        $this->check_params($q, array('id','jobid'));
        $sql = sprintf("UPDATE `names` SET `jobid`=%d WHERE `id`=%d LIMIT 1;",
                $q['jobid'], $q['id']);
        $this->db_query_row($sql);
        return true;
    }

    /**
     *  Резервное сохранение данных сотрудника перед внесением изменений
     *  ip=OPERATOR_IP, id=http[id], opip=http[opid]
     */
    protected function name_backup( $q )
    {
        $this->check_params($q, array('id', 'ip', 'opid'));

        $sql = sprintf("INSERT INTO `names_history` (`id`,`jobid`,`name`,`phones`,"
            . "`email`,`opid`, `ip`, `date` ) SELECT `id`,`jobid`,`name`,"
            . "`phones`,`email`, %d, '%s', date('now') FROM `names` WHERE `id`=%d"
            , $q['opid'], $q['ip'], $q['id']);
        return $this->sqlite_insert($sql);
    }

    /**
     * Вставка должности
     */
    protected function job_insert( $q )
    {
        $this->check_params($q,
            array('unitid','kab','job','phone','fax','order','anonid'));

        $sql = sprintf("INSERT INTO `jobs`"
            ."(`unitid`,`kab`,`job`,`phone`,`fax`,`order`,`anonid`)"
            ." VALUES (%d, '%s', '%s', '%s', '%s', %d, %d)",
            $q['unitid'], $q['kab'], $q['job'], $q['phone'],
            $q['fax'], $q['order'], $q['anonid']);

        return $this->sqlite_insert($sql);
    }

    /**
     * Удаление должности
     */
    protected function job_delete( $jobid ) {
        if (empty($jobid)) {err('Ошибка вызова функции job_delete()');}
        $jobid = (int) $jobid;
        if ($jobid < 1){err('Ошибка вызова функции job_delete()');}
        $sql = "DELETE FROM `jobs` WHERE `jobid`=$jobid;";
        return $this->sqlite_exec($sql);
    }

    /**
     * Получение данных по отделу
     */
    protected function get_unit( $unitid ) {
        $sql = "SELECT * FROM `units` WHERE `unitid`=$unitid;";
        $unit = $this->sqlite_query($sql);
        $unit[0]['unit'] = preg_replace("/'/", "\"", $unit[0]['unit']);
        return $unit[0];
    }

    /**
     * Получения полного названия отдела,
     * включая имя подразделения/блока/группы без URL
     *
     */
    protected function get_txtpath_unit( $unitid )
    {
      if (empty($unitid)) return FALSE;
      $sql = "SELECT * FROM `units` WHERE `unitid`=$unitid;";
      $r = $this->sqlite_query($sql);
      if (!$r) return '';
      $unit = $r[0];
      $full_unit = preg_replace("/'/", "\"", $unit['unit']);
      if ($unit['parent'] == 0)
      {
        return $full_unit;
      }
      else
      {
        $full_unit = $this->get_txtpath_unit($unit['parent'])
                   . $this->separator_unit . $full_unit;
         return $full_unit;
      }
    }

    /**
     * Получения полного названия отдела, включая имя подразделения/блока/группы
     *
     * В данной функции (для удобства пользователей) к названиям отделов
     * автоматически добавляются URL-ссылки. Если необходим список без ссылок,
     * то надо использовать функцию "get_txtpath_unit()"
     */
    protected function get_path_unit( $unitid )
    {
      if (empty($unitid)) return FALSE;
      $sql = "SELECT * FROM `units` WHERE `unitid`=$unitid;";
      $res = $this->sqlite_query($sql);
      if (!$res) return '';
      $unit = $res[0];
      $full_unit = preg_replace("/'/", "\"", $unit['unit']);
      $full_unit = '<a class="unit" href="' . ROOTURL . 'view/units/unitid/'
          . $unitid . '">' . $full_unit . '</a>';

      if ($unit['parent'] == 0)
      {
        return $full_unit;
      }
      else
      {
        $full_unit = $this->get_path_unit($unit['parent'])
                   . $this->separator_unit . $full_unit;
        return $full_unit;
      }
    }

    /**
     * Формирование списка вакансий
     */
    protected function get_vacancys() {
        $sql = "SELECT `jobid`,`unitid`,`job` FROM `jobs` WHERE `jobid` "
            ."NOT IN (SELECT `jobid` FROM `names`) AND `anonid`='0'";
        $rows_arr = $this->sqlite_query($sql);

        $vacancys = array();
        foreach ($rows_arr as $row) {
            $vacancys[$row['jobid']] = $this->get_txtpath_unit($row['unitid'])
                    . ' | ' . $row['job'];
        }
        asort($vacancys, SORT_LOCALE_STRING);
        return $vacancys;
    }

    /**
     * Формирование списка дежурных служб
     */
    protected function get_dutyes() {
        $sql = "SELECT * FROM `jobs` WHERE `jobid` NOT IN (SELECT `jobid` "
            ."FROM `names`) AND (NOT `anonid`='0')";
        $dutyes = $this->sqlite_query($sql);

        foreach ($dutyes as $id => $record) {
            $dutyes[$id]['unit'] = $this->get_path_unit($record['unitid']);
        }

        return $dutyes;
    }

    /**
     *  Создание ассоциативного от unitid массива массива дочерних
     *  подразделений по id родителя
     */
    protected function get_branch_assoc($id = '', $order = "order") {
        $dst_arr = array();
      $branch = $this->get_branch_array($id, $order);
        if (is_array($branch)) {
      foreach ($branch as $unit) {
        $dst_arr[$unit['unitid']] = $unit;
          }
        }
        unset($branch);
        return $dst_arr;
    }

    /**
     *  Получение списка дочерних подразделений
     *  по id родителя
     */
    protected function get_branch_array($id = '', $order = "order") {
        if (empty($id)) { $id = 0; }
        $sql = "SELECT * FROM `units` WHERE `units`.`parent`=$id "
             . "ORDER BY `units`.`$order`;";
        $rows = $this->sqlite_query($sql);
        return $rows;
    }

    /**
     * Проверка, есть ли в справочнике указанное имя
     */
    protected function employer_exist($name) {
        $name = stripslashes($name);
        $name = strip_all($name);
        $found_rows = 0;
        $res = $this->select_by_text($name);
        if (is_array($res)) { $found_rows = $res[0];}

        if ($found_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Комплексные данные из указанной по $nmid модификации
     */
    protected function get_names_mod($nmid = '') {
        $sql = 'SELECT * FROM `names_mod`';
        if (empty($nmid)) {
          return $this->sqlite_query($sql);
        } else {
          $sql = $sql . ' WHERE `names_mod`.`nmid`="' . $nmid . '";';
          return $this->db_query_row($sql);
        }
    }

    /**
     * Комплексные данные из указанной по $jmid модификации
     */
    protected function get_jobs_mod($jmid = '') {
        $sql = 'SELECT * FROM `jobs_mod`';
      if (empty($jmid)) {
          return $this->sqlite_query($sql);
        } else {
          $sql = $sql . ' WHERE `jobs_mod`.`jmid`="' . $jmid . '"';
          return $this->db_query_row($sql);
        }
    }

    /**
     * Cоздание подразделения
     */
    protected function unit_insert( $q )
    {
        $this->check_params($q, array('unit', 'parent', 'order'));
        $sql = sprintf("INSERT INTO \"units\"" .
             "(\"unit\", \"parent\", \"order\")" .
             "VALUES ('%s', '%d', '%d');",
             $q['unit'], $q['parent'], $q['order']);
        return $this->sqlite_insert($sql);
    }

    /**
     * Обновление данных в таблице подразделений
     */
    protected function unit_update( $q )
    {
      $this->check_params($q, array('unit','parent','order','unitid'));
      $sql = sprintf("UPDATE `units` SET `unit`='%s', `parent`=%d, "
          . "`order`=%d WHERE `unitid`=%d;"
          , $q['unit'], $q['parent'], $q['order'], $q['unitid']);
      $this->sqlite_exec($sql);
      return true;
    }

    /**
     * Резервное сохранение данных подразделений
     */
    protected function unit_backup( $q ) {
        $this->check_params($q, array('opid', 'opip', 'unitid'));

        $sql = sprintf("INSERT INTO \"units_history\""
            ."( \"unitid\", \"unit\", \"parent\", \"order\", \"opid\", \"ip\", \"date\")"
            ." SELECT \"unitid\", \"unit\", \"parent\", \"order\", %d, '%s', date('now')"
            ." FROM \"units\" WHERE \"unitid\"=%d"
            , $q['opid'], $q['opip'], $q['unitid']);

        return $this->sqlite_insert($sql);
    }

    /**
     * Проверка наличия вложенных объектов в подразделении
     */
    protected function unit_isempty($unitid) {
        $sql = "SELECT COUNT(`jobid`) FROM `jobs` WHERE `unitid`=$unitid;";
        $t = (int) $this->sqlite_query_single($sql);
        if ($t > 0) return false;

        $sql = "SELECT COUNT(`unitid`) FROM `units` WHERE `parent`=$unitid;";
        $t = (int) $this->sqlite_query_single($sql);
        if ($t > 0) return false;

        return true;
    }

    /**
     * Удаление подразделения
     */
    protected function unit_delete( $unitid ) {
        if (empty($unitid)){ return FALSE;}
        $sql = "DELETE FROM `units` WHERE `unitid`=$unitid;";
        return $this->sqlite_exec($sql);
    }

    /**
     * Выборка из базы данных полной информации о сотруднике по индексу должности
     * Возвращается массив из одного ассоциативного массива данных
     *
     * Если в базе несколько сотрудников на одной должности, то возвращаются все
     * найденные в виде массива из ассоциативных массивов.
     */
    protected function get_employers($job = '') {
        if (empty($job)){ return '';}

        $sql = "SELECT `names`.*, `units`.`unit`, `jobs`.`unitid`,
         `jobs`.`kab`, `jobs`.`job`, `jobs`.`phone`, `jobs`.`fax`, `jobs`.`order`
         FROM `names` LEFT JOIN `jobs` ON `names`.`jobid`=`jobs`.`jobid`
         LEFT JOIN `units` ON `jobs`.`unitid`=`units`.`unitid`
         WHERE `names`.`jobid`={$job} ";

        return $this->sqlite_query($sql);
    }

    /**
     * По индексу должности возвращается строка имен сотрудников,
     * записанных в базе данных на эту должность. Как правило, если
     * сотрудников больше одного - это свидетельствует об ошибке. На
     * одной должности не должно быть более одного сотрудника.
     */
    protected function get_employer_names($job = '') {

        if (empty($job)) {
            return '';
        }
        $employers = $this->get_employers($job);

        $name = '';
        $sp = '';
        $max_i = count($employers);

        for ($i = 0; $i < $max_i; $i++) {
            $name .= $sp . $employers[$i]['name'];
            $sp = ', ';
        }

        return $name;
    }

    /**
     * Выборка из базы данных индексов всех должностей
     */
    protected function select_jobs_id() {
        $sql = "SELECT `jobid` FROM `jobs` WHERE 1";
        $jobs_arr = $this->sqlite_query($sql);

        $jobs_id = array();
        foreach ($jobs_arr as $row) {
            $jobs_id[] = $row['jobid'];
        }
        unset($jobs_arr);
        return $jobs_id;
    }

    /**
     *  Все данные по должности
     */
    protected function get_job($jobid)
    {
        if (empty($jobid)) return null;
        $sql = "SELECT * FROM `jobs` WHERE `jobid`=$jobid LIMIT 1";
        $r = $this->sqlite_query($sql);
        $job = $r[0];
        $job['path'] = $this->get_path_unit($job['unitid']);
        return $job;
    }

    /**
     *  Поиск ID родительского подразделения
     */
    protected function get_parent($id = '') {
        if (empty($id)){ $id = 0;}
        $sql = 'SELECT `parent` from `units` WHERE `unitid`=' . $id . ';';
        $unit = $this->sqlite_query($sql);
        if(count($unit) > 1) err("Ошибка получения ID родительского unit.");
        return $unit[0]['parent'];
    }

    /**
     * Получение по $id подразделения массива должностей и данных сотрудников
     */
    protected function get_jobs_array($unitid = 0) {
        $sql = 'SELECT `jobs`.*,`names`.`id`,`names`.`name`,'
                . '`names`.`phones`,`names`.`email` FROM `jobs` '
                . 'LEFT JOIN `names` ON `names`.`jobid` = `jobs`.`jobid` '
                . 'WHERE `jobs`.`unitid` = "' . $unitid
                . '" ORDER BY `jobs`.`order`;';
        return $this->sqlite_query($sql);
    }

    /**
     * Есть ли ожидающие изменений записи по сотрудникам
     */
    protected function exist_name_mod($id) {
        $sql = "SELECT COUNT(`id`) FROM `names_mod` WHERE `id`='$id';";
        $num = (int) $this->sqlite_query_single($sql);
        if ($num > 0) {
            return true;
        }
        return false;
    }

    /**
     * Есть ли ожидающие изменений записи по сотрудникам
     */
    protected function exist_job_mod($jobid)
    {
        $sql = "SELECT COUNT(`jobid`) FROM `jobs_mod` WHERE `jobid`='$jobid';";
        $num = (int) $this->sqlite_query_single($sql);
        if ($num > 0) {
            return true;
        }
        return false;
    }

    /**
     * ( пока заглушка ) формирование массива, используемого для создания
     * элемента управления "списк опций", назначаемых записям в справочнике,
     * в которых не используются имена сотрудников
     */
    protected function get_anons() {
        return array('0' => 'штатная единица', '1' => 'дежурные службы');
    }

    /**
     * Выборка из базы данных сотрудников, у которых поле имени
     * начинается на указанные буквы
     */
    protected function select_names_by_letters($string, $page_num, $maxnames) {
        // Разбить строку в массив
        $letters = preg_split('/(?<!^)(?!$)/u', $string);

        $from = $page_num * $maxnames;

        $sql_count = "SELECT count(`name`)";
        $sql_list = "SELECT `names`.*, `units`.`unit`, `jobs`.`unitid`, "
            ."`jobs`.`kab`, `jobs`.`job`, `jobs`.`phone`, `jobs`.`fax`";

        $sql = " FROM `names`
            LEFT JOIN `jobs` ON `names`.`jobid`=`jobs`.`jobid`
            LEFT JOIN `units` ON `jobs`.`unitid`=`units`.`unitid`";

        reset($letters);
        $word = "
            WHERE (";
        foreach ($letters as $value) {
            $sql .= $word . '`names`.`name` LIKE "' . $value . '%"';
            $word = ' OR ';
        }
        $sql .= ")";

        // Отбираем только те записи, у которых есть номер телефона
        $sql .= " AND ( LENGTH(`jobs`.`phone`) > 2
                           OR LENGTH(`jobs`.`fax`) > 2
                           OR LENGTH(`names`.`phones`) > 2 )";

        $query = $sql_list . $sql .
            " ORDER BY `names`.`name` ASC LIMIT {$from},{$maxnames};";

        // для постраничного вывода необходимо общее количество строк,
        // удовлетворяющих условию запроса
        $last_rows = $this->sqlite_query_single($sql_count . $sql);

        // страница данных
        $names_array = $this->sqlite_query($query);

        return array($last_rows, $names_array);
    }

    /**
     * Выборка из таблицы names строк, в которых присутствует текст $text
     *
     * Возвращается массив ассоциативных массивов с индексами
     * соответствующими именам полей таблицы
     */
  protected function select_by_text($text='', $maxrows=1, $page=0)
  {
      if (empty($text)) return array(0, null);
      $select_from = $page * $maxrows;
      $text = strip_all($text);
      $text = str_replace(' ', '%', $text);

      // Преобразование номера телефона в регулярное выражение вида
      // ".3.8.4.5.9.0."
      $digits = preg_replace('/[^0-9]/', '', $text);
      $strlen_di = strlen($digits);
      $phone_reg = ".*";
      while($strlen_di > 0)
      {
          $phone_reg = ".?" . $digits[$strlen_di - 1] . $phone_reg;
          --$strlen_di;
      }
      $phone_reg = '.*' . $phone_reg;
      $search_phone = "";
      if(strlen($phone_reg) > 12) $search_phone = 
          "OR `jobs`.`phone` REGEXP '" . $phone_reg . "'";

      $sql = "SELECT `jobs`.*, `names`.`id`,
             `names`.`name`, `names`.`phones`, `names`.`email`,
             `units`.`unit` FROM `jobs`
              LEFT JOIN `names` ON `names`.`jobid`=`jobs`.`jobid`
              LEFT JOIN `units` ON `jobs`.`unitid`=`units`.`unitid`
              WHERE `names`.`name` LIKE '%{$text}%'
              OR `names`.`phones` LIKE '%{$text}%'
              OR `names`.`email` LIKE '%{$text}%'
              OR `jobs`.`kab` LIKE '%{$text}%'
              OR `jobs`.`job` LIKE '%{$text}%'"
            . $search_phone 
            . " OR `jobs`.`fax` LIKE '%{$text}%'
                OR `units`.`unit` LIKE '%{$text}%'";
              //OR `jobs`.`phone` LIKE '%{$text}%'
      $query = $sql
          ." ORDER by `units`.`unitid` DESC LIMIT {$select_from},{$maxrows} ";

      // для постраничного вывода необходимо общее количество
      // строк, удовлетворяющих условию запроса
      $last_rows = count($this->sqlite_query($sql));
      $arr = $this->sqlite_query($query);
      return array($last_rows, $arr);
  }

    /**
     * Выборка по id должности полной информации о сотруднике
     * Возвращается ассоциативный массив данных
     */
    protected function get_empty($jobid = '')
    {
        if (empty($jobid)) return FALSE;
        $sql = "SELECT `jobs`.*, `names`.`id`, `names`.`name`,
            `names`.`phones`, `names`.`email`, `units`.`unit`, `anons`.`anon`
            FROM `jobs` LEFT JOIN `names` ON `names`.`jobid`=`jobs`.`jobid`
            LEFT JOIN `units` ON `units`.`unitid`=`jobs`.`unitid`
            LEFT JOIN `anons` ON `anons`.`anonid`=`jobs`.`anonid`
            WHERE `jobs`.`jobid`=" . $jobid;
        $r = $this->sqlite_query($sql);
        $employer = $r[0];
        $path_unit = $this->get_path_unit($employer['unitid']);
        $employer['path'] = $path_unit;
        return $employer;
    }

    /**
     * Выборка из базы данных полной информации о сотруднике
     * Возвращается ассоциативный массив данных
     */
    protected function get_employer($id = '')
    {
        if (empty($id)) {
            err('Error in function get_employer - will not set ID');
        }

        $sql = "SELECT `jobs`.*, `names`.`id`, `names`.`name`,
            `names`.`phones`, `names`.`email`, `units`.`unit`, `anons`.`anon`
            FROM `jobs` LEFT JOIN `names` ON `names`.`jobid`=`jobs`.`jobid`
            LEFT JOIN `units` ON `units`.`unitid`=`jobs`.`unitid`
            LEFT JOIN `anons` ON `anons`.`anonid`=`jobs`.`anonid`
            WHERE `names`.`id`=" . $id;
        $r = $this->sqlite_query($sql);
        $employer = $r[0];
        $employer['path'] = $this->get_path_unit($employer['unitid']);
        return $employer;
    }

    /**
     * Очистка таблиц запросов на изменения имен
     */
    protected function delete_names_mods($id) {
        if(empty($id)){return;}
        $sql = "DELETE FROM `names_mod` WHERE `id`=$id;";
        $this->db_query($sql);
        return;
    }

    /**
     * Очистка таблиц запросов на изменения должностей
     */
    protected function delete_jobs_mods($jobid) {
        $sql = "DELETE FROM `names_mod` WHERE `new_jobid`=$jobid;";
        $this->db_query($sql);

        $sql = "DELETE FROM `jobs_mod` WHERE `jobid`=$jobid;";
        $this->db_query($sql);
        return;
    }
    /**
     * Увеличение рейтинга оператора
     */
    protected function operator_up( $opid ) {
        $sql = "SELECT * FROM `operators` WHERE `opid`='$opid'";
        $operator = $this->db_query( $sql );

        // администраторы назначаются только вручную
        $level = (int)$operator['level'];
        if ( $level < MAX_LEVEL ) {
            $sql = "UPDATE `operators` SET `level`=(`level`+1) WHERE `opid`='$opid'";
            $this->db_query( $sql );
        }
    }

    /**
   * понижение рейтинга оператора
     */
    protected function operator_dn( $opid ) {
        $sql = "UPDATE `operators` SET `level`=(`level`+1) WHERE `opid`='$opid'";
        $this->db_query( $sql );
    }

    /**
     * Активация учетной записи оператора
     */
    protected function activate_operator( $mail ) {

        // Вначале проверим, нет ли уже такого адреса электронной почты
        // в списке операторов
        $sql = "SELECT MAX(`level`) AS `level` from `operators` WHERE `email`='"
                . $mail . "';";
        $row = $this->db_query_row( $sql );
        $max_level = (int)$row['level'];

        if ( $max_level < 4 ) {$max_level = 4;}

        $sql = 'UPDATE `operators` SET `email`="' . $mail . '", `level`="'
          . $max_level . '" ' . 'WHERE `mpd`="'. OPERATOR_MPD . '"';
        $result = $this->db_query_row( $sql );

        return $result;
    }
    /**
     * Создание новой записи в таблице операторов
     */
    protected function new_operator() {
        $sql = 'INSERT `operators` SET `mpd`="' . OPERATOR_MPD . '"';
        $this->db_query_row( $sql );
        return;
    }

    /**
     * Регистрация посещения оператора
     */
    private function operator_vist_reg() {
        $sql = 'UPDATE `operators` SET `visit`=now(), `ip`="'. OPERATOR_IP
            .'" WHERE `mpd`="' . OPERATOR_MPD . '"';
        $this->db_query( $sql );
        return;
    }

    /**
     * Установка/получение текущего уровня доступа оператора
     */
    private function detect_operator() {

      // ОТКЛЮЧЕНО --->
        $this->operator['level']=52;
        return;
      // ОТКЛЮЧЕНО <---

        $sql = 'SELECT * FROM `operators` WHERE `mpd`="' . OPERATOR_MPD . '"';
        $this->operator = $this->db_query_row( $sql );

        if ( $this->last_rows < 1 ) {
            $this->new_operator();
            $this->operator = $this->db_query_row( $sql );
        }

        // локальный оператор - всегда имеет административные права.
        if ($this->operator['ip']=='::1') $this->operator['level']=52;

        $this->operator_vist_reg();

        return;
    }
    protected function jobs_history_clear() {
        $sql = "DELETE FROM `jobs_history` WHERE `date` < date( 'now','-30 day')";
        $this->sqlite_exec($sql);
        return;
    }

    protected function names_history_clear() {
        $sql = "DELETE FROM `names_history` WHERE `date` < date( 'now','-30 day')";
        $this->sqlite_exec( $sql );
        return;
    }

    protected function jobs_mod_clear() {
        $sql = "DELETE FROM `jobs_mod` WHERE `date` < date( 'now','-30 day')";
        $this->sqlite_exec( $sql );
        return;
    }

    protected function names_mod_clear() {
        $sql = "DELETE FROM `names_mod` WHERE `date` < date( 'now','-30 day')";
        $this->sqlite_exec( $sql );
        return;
    }

    protected function operators_clear() {
        $sql = "DELETE FROM `operators` WHERE `level`=\"0\" "
        . "AND `visit` < date( 'now','-30 day')";
        $this->sqlite_exec( $sql );

        $sql = "DELETE FROM `operators` WHERE `visit` < date( 'now','-90 day')";
        $this->sqlite_exec( $sql );
        return;
    }
}
