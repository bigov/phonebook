<?php
/**
 * Выборка данных из базы и предоставление информации
 */
class Viewer extends Db {

   /**
    * выбор функции отображения
    */
    private $tabs = 0;
    public function show()
    {

  switch ( FUNC ) {
      case 'printall':
        $this->print_all();
        exit();
      case 'export.csv':
        $this->do_export_csv();
        exit();
      case 'export':
        $this->view_export();
        exit();
      case 'photos':
        $this->view_photos();
        exit();
      case 'duty':
        $this->view_duty();
        exit();
      case 'searchform':
        $this->view_searchform();
        exit();
      case 'search':
        $this->view_search();
        exit();
      case 'units':
        $this->view_units();
        exit();
      case 'employers':
        $this->view_employers();
        exit();
      case 'usercard':
        $this->view_usercard();
        exit();
      case 'download':
        $this->download();
        exit();
      default:
        $this->view_units();
        exit();
  }
   }

    /**
     * Предоставление полного списка для печати данных
     */
    private function print_all() {
      $smarty = $this->render;
      $smarty->assign('phones', $this->make_full_list());
      $smarty->display('print.htpl');
        exit();
    }

   /**
    * Отображение всех фотографий
    */
    private function view_photos(){

      $arr_photos = array();
      $ph_dir = ABSPATH . PHOTOS;
      if ($handle = opendir($ph_dir)) {
        while (false !== ($entry = readdir($handle))) {
          if ( substr($entry, -4) == '.jpg') {
            $id = substr($entry, 2, -4);
            if ($this->check_name_id($id)) {
              $arr_photos[] = $id;
            } else {
              rename($ph_dir.$entry, $ph_dir.'old'.DS.$entry);
            }
          }
        }
        closedir($handle);
      }
      $smarty = $this->render;
      $smarty->assign('photos', $arr_photos);
      $smarty->assign('maket','photos.htpl');
      $smarty->display('default.htpl');
        exit();
   }

   /**
     * Автономная версия справочника
     */
    private function download() {
      //$smarty->assign('phones', $this->make_full_list());

      // получает содержимое файла в строку
      $fdir = ABSPATH . DS. "offline" . DS;
      $filename = $fdir . '1.header.htm';
      $handle = fopen($filename, "r");
      $contents = fread($handle, filesize($filename));
      fclose($handle);

      $d  = ' data:image/gif;base64,'
          . base64_encode(file_get_contents($fdir . 'plus.gif'));
      $contents = str_replace('_URL_PLUS_', $d ,$contents);

      $d = ' data:image/gif;base64,'
          . base64_encode(file_get_contents($fdir . 'minus.gif'));
      $contents = str_replace('_URL_MINUS_', $d ,$contents);

      $d = file_get_contents($fdir . 'jquery.min.js');
      $contents = str_replace('__jquery_min_js__', $d ,$contents);

      $d = file_get_contents($fdir . 'jquery.nestedAccordion.js');
      $contents = str_replace('__jquery_nestedAccordion_js__', $d ,$contents);

      $d = file_get_contents($fdir . 'expand.js');
      $contents = str_replace('__expand_js__', $d ,$contents);

      unset($d);

      header("Content-Description: File Transfer\r\n");
      header("Pragma: public\r\n");
      header("Expires: 0\r\n");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0\r\n");
      header("Cache-Control: public\r\n");
      header("Content-Type: text/plain; charset=UTF-8\r\n");
      header("Content-Disposition: attachment; filename=\"phones.html\"\r\n");

      print $contents;

      print ("<ul id=\"acclist\" class=\"accordion\">\n\t<li id=\"current\"><a href=\"#\">Телефонный справочник</a>");

      // По-умолчанию экспорт выполняется только для базовой ветки справочника
      print $this->make_ul_branch($this->make_full_list(DEFAULT_PODR));
      // Если нужен полный список, то используем id корневой записи таблицы units
      //print $this->make_ul_branch($this->make_full_list(1));

      print ("\n\t</li>\n</ul>\n\n");

      $filename = $fdir . '3.footer.htm';
      $handle = fopen($filename, "r");
      $contents = fread($handle, filesize($filename));
      fclose($handle);
      print $contents;

      exit();
    }

    /**
     * Формирование списка подчиненных подразделений
     *
     * @param array $branch
     * @return string
     */
    private function make_ul_branch ( $branch ) {
      $this->tabs_inc();
      $row = "";
      $this->tabs_inc();
      foreach ($branch as $i => $val) {
        $row = $row . $this->indent() . "<li><a href=\"#i\">" . $val['unit']. "</a>";
          if (isset($val['jobs'])) {
          $row = $row . $this->make_ul_jobs($val['jobs']);
        }
        if (isset($val['branch'])) {
          $row = $row . $this->make_ul_branch($val['branch']);
        }

        $row = $row . $this->indent() . "</li>";
      }
      $this->tabs_dec();

      $res = $this->indent() . "<ul>" .$row. $this->indent() . "</ul>";
      $this->tabs_dec();

      return $res;
    }

    /**
     * Формирование списка последнего уровня (должности/сотрудники)
     *
     * @param array $jobs
     * @return string
     */
    private function make_ul_jobs ($jobs) {
      $this->tabs_inc();
      $row = "";
      $this->tabs_inc();
      $color = 'first';
      foreach ($jobs as $i => $job) {

        if (empty($job['name'])) $job['name'] = 'данные отсутствуют';
        $dd = '<strong>'.$job['name'].'</strong><br> ';
        if (!empty($job['kab'])) $dd .= 'каб. ' . $job['kab'] . ', ';
        if (!empty($job['phone'])) $dd .= ' телефон: ' . $job['phone'] . ', ';
        if (!empty($job['fax'])) $dd .= $job['fax'] . ', ';
        if (!empty($job['phones'])) $dd .= ' доп.тел.: ' . $job['phones'] . ', ';
        if (!empty($job['email'])) $dd .= '<a class="m" href="mailto:' . $job['email']
                           .'">'.$job['email'].'</a>';
        $row = $row
          . $this->indent()."<li><dl class=\"$color\"><dt>". mb_strtolower($job['job']) ."<dt>"
          . $this->indent()."<dd>$dd</dd></dl></li>";

        if ($color == 'first') {
          $color = 'second';
        } else {
          $color = 'first';
        }
      }
      $this->tabs_dec();
      $res = $this->indent() . "<ul>" . $row . $this->indent() ."</ul>";
      $this->tabs_dec();
      return $res;
    }

  /**
   * Оформление отступами списков в коде HTML страницы
   * @return string
   */
  private function indent() {
    $n = $this->tabs;
    //символ отступа
    $sp = "\t";
    while( $n>0 ) {
      $sp = $sp . "\t";
      --$n;
    }
    return "\n". $sp;
  }
  /**
   * Увеличить отступ
   */
  private function tabs_inc() {
    $this->tabs = $this->tabs + 1;
  }
  /**
   * Уменьшить отступ
   */
  private function tabs_dec() {
    if ($this->tabs > 0)
      $this->tabs = $this->tabs - 1;
    else
      $this->tabs = 0;
  }

    /**
     *  Экспорт данных юнита 'unitid' в формате CSV
     */
    public function do_export_csv() {
        $jobs_id = $this->select_jobs_id();
        $export = '"Подразделение","Должность","Фамилия","Телефоны"'."\r\n";
        foreach ( $jobs_id as $jobid) {
            $row = $this->get_empty($jobid);

            $phone = $this->add( $row['phone'], $row['fax'] );
            $phone = $this->add( $phone, $row['phones'] );

            $export .= $this->csv( $row['path'] ) . ','
                     . $this->csv( $row['job'] )  . ','
                     . $this->csv( $row['name'] ) . ','
                     . $this->csv( $phone ) . "\r\n";
        }

        header("Content-Description: File Transfer\r\n");
        header("Pragma: public\r\n");
        header("Expires: 0\r\n");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0\r\n");
        header("Cache-Control: public\r\n");
        header("Content-Type: text/plain; charset=UTF-8\r\n");
        header("Content-Disposition: attachment; filename=\"phones.csv\"\r\n");

        print($export);
        exit();
    }

    /**
     * Добавление текста через точку с запятой. Если исходная строка была
     * пустая, то текст добавляется без разделителя.
     *
     * //@param type $src
     * //@param type $new
     * @return string
     */
    private function add($src_txt, $add_txt) {

        if( strlen($src_txt) < 4) {
            return $add_txt;
        }
      if( strlen($add_txt) < 4) {
          return $src_txt;
      }
      return "$src_txt; $add_txt";
  }

  /**
   *  - удаление из текста ссылок, кавычек и запятых
   *  - обрамление двойными кавычками
   */
  private function csv( $text ){
      $remove = array("'","\"");
      $text = str_replace($remove, "", $text);
      $text = str_replace(",", ";", $text);
      $text = str_replace(" » ", ": ", $text);

      /* удаление ссылок */
      $text = str_replace("</a>", "", $text);
      $text = preg_replace('/<a\s*(.*?)>/i', '', $text);
      return '"'. $text .'"';
  }

  /**
   *  Страница экспорта данных - общий список подразделений юнита 'unitid'
   */
  public function view_export() {
      $smarty = $this->render;
        $smarty->assign('rows', $this->get_branch_assoc($this->http['unitid']));
        $smarty->display('export.htpl');
        return;
    }


    /**
     * Отображение данных карточки пользователя/должности
     */
    public function view_searchform() {
      $smarty = $this->render;
      $smarty->display('search_form.htpl');
      exit();
    }

    /**
     * Отображение данных карточки пользователя/должности
     */
    public function view_usercard() {

        if(!empty($this->http['id'])) {
            $emp = $this->get_employer( $this->http['id'] );
        } else {
            $emp = $this->get_empty( $this->http['jobid'] );
        }

        $emp['name'] = mb_strtoupper( $emp['name'] , 'UTF-8');

      if(!empty($emp['kab'])) $emp['kab'] = ' (каб. ' . $emp['kab'] . ')';

      if( !empty( $emp['id'] )) {
        $url_edit = ROOTURL . 'employer/edit/id/' . $emp['id'];
      } else {
        $url_edit = ROOTURL . 'job/edit/jobid/' . $emp['jobid'];
      }

      $img = PHOTOS . 'ph' . $emp['id'] . '.jpg';
      if ( !file_exists($img) ) $img = 'img/' . 'ph0.gif';
      $img = ROOTURL . $img;

      $smarty = $this->render;
      $smarty->assign('url_edit', $url_edit);
      $smarty->assign('img', $img);
      $smarty->assign('emp', $emp);
      $smarty->display('usercard.htpl');
      exit();
    }

    /**
     * Отображение результатов текстового поиска по всей базе данных
     */
    public function view_search() {
        $query = $_REQUEST['query'];
        $page = 0;
        if( isset($_REQUEST['page'])){ $page = $_REQUEST['page'];}

        $replacement = '<span class="selected">'. $query . '</span>';
        list( $sum_rows, $arr_names ) =
              $this->select_by_text( $query, $this->maxrows, $page );
        if( $sum_rows > 0 ) {
          //var_dump($arr_names);
          //echo _fname_;
          foreach($arr_names as $k => $arr_name ) {
            //var_dump($arr_name); continue;
              //!!! $arr_names[$k]['unit'] = $this->get_path_unit($arr_name['unitid']);
            foreach( $arr_name as $key => $name ) {
              if( $key=='job'  or $key=='name' or $key=='unit' ) {
                // Выделение указанного текста
                $slitted_name = utf8_spliti_string( $query, $name );
                if( is_array( $slitted_name )) {
                  $arr_names[$k][$key] = $slitted_name[0] . $replacement . $slitted_name[1];
                }
              }
            }
          }
        }

        $smarty = $this->render;
        $smarty->assign('replacement', $replacement);
        $smarty->assign('rows', $arr_names);
        $cache_id = urlencode( $query . $page );

        if ($sum_rows > $this->maxrows) {
            $num_pages = ceil( $sum_rows / $this->maxrows );
            $paginator = make_paginator( '?query='.$query, $page, $num_pages );
            $smarty->assign('paginator', $paginator);
        }
        $smarty->assign('maket','search.htpl');
        $smarty->display('default.htpl', $cache_id);
        return;
    }

    /**
     *  Страница по-умолчанию - общий список организаций
     */
    public function view_units() {
        if ( empty( $this->http['unitid'])) {
            $unitid = DEFAULT_PODR;
      } else {
            $unitid = $this->http['unitid'];
      }
        $units = $this->get_units( $unitid );
        $smarty = $this->render;
        $smarty->assign('rows', $units);
        $smarty->display('default.htpl', $unitid);
        return;
    }

    /**
     * Список дежурных служб (выбор должностей с установленным
     * атрибутом "дежурная служба")
     */
    public function view_duty() {
        $content = $this->get_dutyes();
        $duty = array();
        foreach ( $content as $key=>$rec ) {
            $row = "$rec[unit] | $rec[job] | $rec[kab] $rec[phone], $rec[fax]";
            $duty[] = $row;
        }
        sort($duty);
        $smarty = $this->render;
        $smarty->assign('duty', $duty);
        $smarty->assign('maket','duty.htpl');
        $smarty->display('default.htpl');
        return;
    }

    /**
     * отображение страницы справочника
     */
    public function view_employers () {
        list( $rows, $paginator ) = $this->get_phones_list();

        foreach ($rows as $i => $row) {
            if(strlen($row['phone']) < 4) {
                if(strlen($row['fax'] > 4)) {
                    $rows[$i]['phone']=$row['fax'];
                } elseif(strlen($row['phones'] > 4)) {
                    $rows[$i]['phone']=$row['phones'];
                }
            }
        }

        $smarty = $this->render;

        $smarty->assign( 'maxrows', $this->maxnames/2 ); // количество отображаемых строк на странице
        $smarty->assign( 'rows', $rows );
        $smarty->assign( 'paginator', $paginator );
        $cache_id = urlencode( $this->http['key']
              . $this->page_num );

        $smarty->assign('maket','employers.htpl');
        $smarty->display('default.htpl', $cache_id);

        return;
    }

    /**
     *  построение списка телефонных номеров для алфавитной страницы
     */
    public function get_phones_list() {
        $key = $this->http['key'];
        $labels = make_labels();
        $letters = $labels[$key];
        list( $num_rows, $names_array ) = $this->select_names_by_letters(
          $letters['label'], $this->page_num, $this->maxnames );

        // Количество страниц (разворотов), необходимых для отображения выборки
        $num_pages = ceil($num_rows/$this->maxnames);
        $paginator = '';
        if ($num_pages > 1) {
            $paginator = make_paginator( ROOTURL . "view/employers/key/$key/",
                $this->page_num, $num_pages );
        }
        return array( $names_array, $paginator );
    }

    /**
     * Построение многомерного массива, отображающего положение указанного
     * в $unitid подразделения в общей иерархии базы данных с отображением
     * ( только для данного подразделения ) списка имеющихся у него должностей
     * и одного уровня подчиненных подразделений.
     */
    public function get_units( $unitid, $branch='' ) {
      // id родительского подразделения
      $parent = $this->get_parent( $unitid );

      // Список подразделений, в число которых входит указанное по $unitid
      $units = $this->get_branch_assoc( $parent );

        if( empty( $branch )) {
            $units[$unitid]['jobs'] = $this->get_jobs_array( $unitid );
            $units[$unitid]['branch'] = $this->get_branch_assoc( $unitid );

            // Cворачивание раздела пo клику на развернутом
            // заголовке - просто переход на родительский уровень.
            if( $units[$unitid]['parent'] > 0 ) {
            $units[$unitid]['unitid'] = $units[$unitid]['parent'];
            }

        } else { $units[$unitid]['branch'] = $branch; }

        if( $parent>0 ){ $units = $this->get_units( $parent, $units );}

        return $units;
    }
}
