<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.aiyi.info All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@aiyi.info> <http://www.aiyi.info>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
* 设置模型
*/
class Category extends Base{

	protected $name = "Category";
	protected $auto = array('update_time', 'icon'=>1, 'status'=>1);

	protected $type = array(
		'icon'  => 'integer',
	);

	public function change(){
		$data = input('post.');
		if ($data['id']) {
			$result = $this->save($data,array('id'=>$data['id']));
		}else{
			unset($data['id']);
			$result = $this->save($data);
		}
		if (false !== $result) {
			return true;
		}else{
			$this->error = "失败！";
			return false;
		}
	}

	public function info($id, $field = true){
		return $this->db()->where(array('id'=>$id))->field($field)->find();
	}
}