<?php namespace inc;

// режим редактирования данных должностей
class jobs extends database {
    
    public function show() {

        switch (FUNC) {
            case 'new':
                $this->job_new();
                exit();
            case 'newcheck':
                $this->job_newcheck();
                exit();
            case 'newsave':
                $this->job_newsave();
                exit();

            case 'edit':
                $this->job_edit();
                exit();
            case 'editcheck':
                $this->job_editcheck();
                exit();
            case 'editsave':
                $this->job_editsave();
                exit();

            case 'move':
                $this->job_move();
                exit();
            case 'movecheck':
                $this->job_movecheck();
                exit();
            case 'movesave':
                $this->job_movesave();
                exit();

            case 'deletecheck':
                $this->job_deletecheck();
                exit();
            case 'deletesave':
                $this->job_deletesave();
                exit();

            default:
                $this->msg404();
                exit();
        }
    }

    protected function job_new() {
        /**
          Страница с формой для создания в справочнике новой должности.
         */
        $smarty = $this->render;

        $smarty->assign('unitid', $this->http['unitid']);
        $smarty->assign('path', $this->get_path_unit($this->http['unitid']));
        $smarty->assign('anons', $this->get_anons());
        $smarty->assign('maket', 'jobnew.htpl');
        $smarty->display('default.htpl');
        exit();
    }

    /**
     * запись новой должности в справочник
     */
    protected function job_newsave() {

        $url = ROOTURL . 'view/unit/unitid/' . $this->http['unitid'];

        if ($this->operator['level'] > 10) {
            $this->job_insert(array(
                'unitid' => $this->http['unitid'],
                'kab' => $this->http['kab'],
                'job' => $this->http['job'],
                'phone' => $this->http['phone'],
                'fax' => $this->http['fax'],
                'order' => $this->http['order'],
                'anonid' => $this->http['anonid'],
                'opid' => $this->operator['opid']));
        } else {
            $this->msg404();
            exit();
        }
        header('Location: ' . $url);
        exit();
    }

    protected function job_edit() {
        /**
          Страница с формой для внесения изменений в справочник.
         */
        $smarty = $this->render;

        if (!$this->mods_clear()) {
            $smarty->assign('maket', 'edit_denied.htpl');
            $smarty->display('default.htpl');
            exit();
        }

        $form_data = $this->get_job($this->http['jobid']);
        $form_data['employer'] = $this->get_employers($this->http['jobid']);
        $smarty->assign('form_data', $form_data);
        $smarty->assign('anons', $this->get_anons());
        $smarty->assign('maket', 'jobedit.htpl');
        $smarty->display('default.htpl');
        exit();
    }

    protected function job_editcheck() {
        /**
         * Форма проверки корректировки должности
         */
        $smarty = $this->render;

        $form_data = $this->http;
        if (!empty($form_data['jmid'])) {
            $form_data = array_merge($form_data, $this->get_jobs_mod($form_data['jmid']));
        }

        $form_data = array_merge($form_data, $this->get_job($form_data['jobid']));

        $form_data['mode'] = 'job';
        $form_data['func'] = 'editsave';

        $anons = $this->get_anons();
        $form_data['anon'] = $anons[$form_data['anonid']];
        $form_data['new_anon'] = $anons[$form_data['new_anonid']];

        $smarty = $this->render;
        $smarty->assign('form_data', $form_data);
        
        $smarty->assign('maket', 'jobeditcheck.htpl');
        $smarty->display('default.htpl');
        exit();
    }

    protected function job_editsave() {
        /**
         *  запись информации в справочник
         */
        $url = ROOTURL . 'view/units/unitid/' . $this->http['unitid'];

        if ($this->operator['level'] > 3) {
            $this->check_admin_level();
            $this->job_backup(array('opid'  => $this->operator['opid'],
                                    'opip'  => OPERATOR_IP,
                                    'jobid' => $this->http['jobid']));
            $this->job_update(array('unitid'=> $this->http['unitid'],
                                    'kab'   => $this->http['kab'],
                                    'job'   => $this->http['job'],
                                    'phone' => $this->http['phone'],
                                    'fax'   => $this->http['fax'],
                                    'order' => $this->http['order'],
                                    'anonid'=> $this->http['anonid'],
                                    'jobid' => $this->http['jobid']));
        } else {
            $this->job_mod( array('cmd'   => 'edit',
                                  'jobid' => $this->http['jobid'],
                                  'unitid'=> $this->http['unitid'],
                                  'kab'   => $this->http['kab'],
                                  'job'   => $this->http['job'],
                                  'phone' => $this->http['phone'],
                                  'fax'   => $this->http['fax'],
                                  'order' => $this->http['order'],
                                  'anonid'=> $this->http['anonid'],
                                  'opid'  => $this->operator['opid']));
            $this->save_ok($url);
        }
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Формирование страницы выбора для перевода должности в другое подразделение
     */
    protected function job_move() {
    	$smarty = $this->render;
    	$smarty->assign( 'move', $this->http );
    	$smarty->assign( 'units', $this->get_units_tree() );
    	$smarty->assign( 'maket', 'job_move.htpl' );
    	$smarty->display( 'default.htpl' );
    	exit();
    }
    /**
     * Форма проверки корректировки должности
     */
    protected function job_movecheck() {
    	$smarty = $this->render;
    	$job_data = $this->get_job($this->http['jobid']);
    	$anons = $this->get_anons();
    	$job_data['anon'] = $anons[$job_data['anonid']];
    
    	$form_data = $this->http;
    	if (!empty($form_data['jmid'])) {
    		$form_data = array_merge($form_data, $this->get_jobs_mod($form_data['jmid']));
    	}
    	
    	$job_data['new_path'] = $this->get_path_unit($this->http['moveto']);
    	$job_data['new_uid'] = $this->http['moveto'];
    	 
    	$smarty = $this->render;
    	$smarty->assign('form_data', $form_data);
    	$smarty->assign('job_data', $job_data);
    	$smarty->assign('maket', 'jobmovecheck.htpl');
    	$smarty->display('default.htpl');
    	exit();
    }
    protected function job_movesave() {
    	$this->job_editsave();
    	exit();
    }
    
    
    /**
     * Форма проверки корректировки должности
     */
    protected function job_deletecheck() {
        $smarty = $this->render;
        $job_data = $this->get_job($this->http['jobid']);
        $anons = $this->get_anons();
        $job_data['anon'] = $anons[$job_data['anonid']];

        $form_data = $this->http;
        if (!empty($form_data['jmid'])) {
            $form_data = array_merge($form_data, $this->get_jobs_mod($form_data['jmid']));
        }

        $smarty = $this->render;
        $smarty->assign('form_data', $form_data);
        $smarty->assign('job_data', $job_data);
        $smarty->assign('maket', 'jobdeletecheck.htpl');
        $smarty->display('default.htpl');
        exit();
    }

    protected function job_deletesave() {
        /**
         *  Удалить должность
         */
        if (empty($this->http['jobid']))
            err('Ошибка вызова функции удаления должности');

        $job = $this->get_job($this->http['jobid']);
        $url = ROOTURL . 'view/units/unitid/' . $job['unitid'] . '/';

        if ($this->operator['level'] > 3) {
            $this->check_admin_level();
            $this->job_backup( array(
                'opid'  => $this->operator['opid'],
                'opip'  => OPERATOR_IP,
                'jobid' => $this->http['jobid'] ));
            $this->job_delete( $this->http['jobid'] );
        } else {
            $this->job_mod( array(
                    'cmd' => 'delete',
                    'jobid' => $this->http['jobid'],
                    'unitid' => $this->http['unitid'],
                    'kab' => $this->http['kab'],
                    'job' => $this->http['job'],
                    'phone' => $this->http['phone'],
                    'fax' => $this->http['fax'],
                    'order' => $this->http['order'],
                    'anonid' => $this->http['anonid'],
                    'opid' => $this->operator['opid']));
            $this->save_ok($url);
        }
        header('Location: ' . $url);
        exit();
    }

}
