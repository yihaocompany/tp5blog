<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.aiyi.info All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@aiyi.info> <http://www.aiyi.info>
// +----------------------------------------------------------------------

namespace app\common\controller;
class Fornt extends Base {
	public function _initialize() {
		parent::_initialize();
		//判读是否为关闭网站
		if (\think\Config::get('web_site_close')) {
			header("Content-type:text/html;charset=utf-8");
			echo $this->fetch('common@default/public/close');exit();
		}
		//设置SEO
		$this->setSeo();
		$this->setHoverNav();
		//主题设置
		$this->setThemes();
	}

}
