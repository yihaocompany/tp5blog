<?php
/**
 * Created by PhpStorm.
 * User: ieasy
 * Date: 2017/2/11
 * Time: 1:28
 */

namespace app\adminstation\controller;


class Config  extends   \app\common\controller\AbminBase
{
    public function _initialize(){
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function index(){
        return $this->fetch();
    }
    public function logout(){
        session_destroy();
        $this->redirect(url('adminstation/Index/index' ,'' ,'') ,20 ,'正在跳转，请稍候......' );
    }

}