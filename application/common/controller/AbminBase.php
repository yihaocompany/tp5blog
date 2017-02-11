<?php
/**
 * Created by PhpStorm.
 * User: ieasy
 * Date: 2017/2/11
 * Time: 1:31
 */
namespace app\common\controller;
class AbminBase extends Base {
    public function _initialize() {
       if(!session('username')){
            $this->redirect(url('adminstation/Index/index' ,'' ,'') ,20 ,'正在跳转，请稍候......' );
            exit;
        }
        parent::_initialize();
    }

}