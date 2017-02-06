<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.aiyi.info All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@aiyi.info> <http://www.aiyi.info>
// +----------------------------------------------------------------------

namespace app\common\controller;
use think\Db;
use think\Model;

class Base extends \think\Controller {

	protected $url;
	protected $request;
	protected $module;
	protected $controller;
	protected $action
    ;

	public function _initialize() {
		/* 读取数据库中的配置 */
        cache('db_config_data',null);
		$config = cache('db_config_data');
		if (!$config) {
		    $model=model('Configs');
            $list = $model
                ->select();
            foreach ($list as $item){
                $arr[$item['name']]=$item['value'];
            }
			cache('db_config_data', $arr);
            $config=cache('db_config_data');
		}


        $this->assign(config($config));
		//获取request信息
		//$this->requestInfo();
	}

	public function execute($mc = null, $op = '', $ac = null) {
		$op = $op ? $op : $this->request->module();
		if (\think\Config::get('url_case_insensitive')) {
			$mc = ucfirst(parse_name($mc, 1));
			$op = parse_name($op, 1);
		}

		if (!empty($mc) && !empty($op) && !empty($ac)) {
			$ops    = ucwords($op);
			$class  = "\\addons\\{$mc}\\controller\\{$ops}";
			$addons = new $class;
			$addons->$ac();
		} else {
			$this->error('没有指定插件名称，控制器或操作！');
		}
	}
	/**
	 * 解析数据库语句函数
	 * @param string $sql  sql语句   带默认前缀的
	 * @param string $tablepre  自己的前缀
	 * @return multitype:string 返回最终需要的sql语句
	 */
	public function sqlSplit($sql, $tablepre) {
		if ($tablepre != "sent_") {
			$sql = str_replace("sent_", $tablepre, $sql);
		}

		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

		if ($r_tablepre != $s_tablepre) {
			$sql          = str_replace($s_tablepre, $r_tablepre, $sql);
			$sql          = str_replace("\r", "\n", $sql);
			$ret          = array();
			$num          = 0;
			$queriesarray = explode(";\n", trim($sql));
			unset($sql);
			foreach ($queriesarray as $query) {
				$ret[$num] = '';
				$queries   = explode("\n", trim($query));
				$queries   = array_filter($queries);
				foreach ($queries as $query) {
					$str1 = substr($query, 0, 1);
					if ($str1 != '#' && $str1 != '-') {
						$ret[$num] .= $query;
					}

				}
				$num++;
			}
		}
		return $ret;
	}
	protected function setSeo($title = '', $keywords = '', $description = '') {
		$seo = array(
			'title'       => $title,
			'keywords'    => $keywords,
			'description' => $description,
		);
		//获取还没有经过变量替换的META信息
		$meta = model('SeoRule')->getMetaOfCurrentPage($seo);
		foreach ($seo as $key => $item) {
			if (is_array($item)) {
				$item = implode(',', $item);
			}
			$meta[$key] = str_replace("[" . $key . "]", $item . '|', $meta[$key]);
		}

		$data = array(
			'title'       => $meta['title'],
			'keywords'    => $meta['keywords'],
			'description' => $meta['description'],
		);
		$this->assign($data);
	}
	/**
	 * 验证码
	 * @param  integer $id 验证码ID
	 * @author 郭平平 <molong@aiyi.info>
	 */
	public function verify($id = 1) {
		$verify = new \org\Verify(array('length' => 4));
		$verify->entry($id);
	}
	/**
	 * 检测验证码
	 * @param  integer $id 验证码ID
	 * @return boolean     检测结果
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function checkVerify($code, $id = 1) {
		if ($code) {
			$verify = new \org\Verify();
			$result = $verify->check($code, $id);
			if (!$result) {
				return $this->error("验证码错误！", "");
			}
		} else {
			return $this->error("验证码为空！", "");
		}
	}
	//request信息
	protected function requestInfo() {
		$this->param = $this->request->param();
		defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
		defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
		defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
		defined('IS_POST') or define('IS_POST', $this->request->isPost());
		defined('IS_GET') or define('IS_GET', $this->request->isGet());
		$this->url = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
		$this->assign('request', $this->request);
		$this->assign('param', $this->param);
	}
	/**
	 * 获取单个参数的数组形式
	 */
	protected function getArrayParam($param) {
		if (isset($this->param['id'])) {
			return array_unique((array) $this->param[$param]);
		} else {
			return array();
		}
	}
	/**
	 * 是否为手机访问
	 * @return boolean [description]
	 */
	public function isMobile() {//return true;
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
			return true;
		}

		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if (isset($_SERVER['HTTP_VIA'])) {
			// 找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		}
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}

		}
		// 协议法，因为有可能不准确，放到最后判断
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			// 如果只支持wml并且不支持html那一定是移动设备
			// 如果支持wml和html但是wml在html之前则是移动设备
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
				return true;
			}
		}
		return false;
	}

	public function is_wechat() {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return true;
		}
		return false;
	}
}
