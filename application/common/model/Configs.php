<?php
namespace app\common\model;
class Configs extends Base{
	protected $name = "configs";
	//protected $auto = array('update_time', 'icon'=>1, 'status'=>1);

/*	protected $type = array(
		'icon'  => 'integer',
	);*/

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