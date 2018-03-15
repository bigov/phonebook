<?php
/* 
 * Включаемый блок кода для работы со Smarty
 */

trait RenderSmarty {

    /**
     *  Инициализация объекта шаблона на базе Smarty
     */
    private function render_init() {
        require( ABSPATH . 'inc/Smarty/Smarty.class.php' );
        $this->render = new Smarty;
        $this->render->debugging = false;

        //    $smarty->caching = true;
        //    $smarty->cache_lifetime = 120;
        $this->render->caching = false;
        $this->render->cache_lifetime = 0;
      
        $this->render->setTemplateDir(ABSPATH . 'templates');
        $this->render->setCompileDir(ABSPATH . 'templates_c');
        $this->render->setConfigDir(ABSPATH . 'configs');
        $this->render->setCacheDir(ABSPATH . 'cache');

        $this->render->assign( 'level', $this->operator['level'] );
        $this->render->assign('labels', make_labels());
        $this->render->assign('ROOTURL', ROOTURL );
      
   }  
   
    /**
     *  Страница подтверждения
     */
    protected function save_ok( $url ) {
        $smarty = $this->render;
        $smarty->assign( 'url', $url );
        $smarty->assign( 'maket','save_ok.htpl' );
        $smarty->display( 'default.htpl' );
        exit();
    }
   
    /**
     * Страница ошибки
     */
    protected function msg404() {
        $smarty = $this->render;
        $smarty->assign( 'maket','404.htpl' );
        $smarty->display( 'default.htpl' );
        exit();
    }
    
}