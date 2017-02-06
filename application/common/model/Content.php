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
class Content extends Base{

	protected $dao;

	protected $auto = array("update_time");
	protected $insert = array("create_time");
	
	protected $type = array(
		'id'  => 'integer',
		'cover_id'  => 'integer',
		'create_time'  => 'integer',
		'update_time'  => 'integer',
	);

	protected function setUidAttr(){
		return session('user_auth.uid');
	}

	protected function setCreateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	protected function setUpdateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	public function extend($name){
		$this->dao = db($name);
		return $this;
	}

	public function lists($map, $order){
		$list = $this->dao->where($map)->order($order)->paginate(15, false, array(
			'query' => $this->param,
		));
		return $list;
	}

	public function detail($id, $map = array()){
		$map['id'] = $id;
		$this->data = $this->dao->where($map)->find();
		return $this->data;
	}

	public function del($map){
		return $this->dao->where($map)->delete();
	}

	public function change(){
		$data = $this->param;
		if (isset($data['id']) && $data['id']) {
			$where['id'] = $data['id'];
		}
		if (!empty($data)) {
			// 数据自动验证
			if (!$this->validateData($data)) {
				return false;
			}
			// 数据对象赋值
			foreach ($data as $key => $value) {
				$this->setAttr($key, $value, $data);
			}
			if (!empty($where)) {
				$this->isUpdate = true;
			}
		}

		// 数据自动完成
		$this->autoCompleteData($this->auto);

		// 自动写入更新时间
		if ($this->autoWriteTimestamp && $this->updateTime) {
			$this->setAttr($this->updateTime, null);
		}

		if ($this->isUpdate) {
			$result = $this->dao->update($this->data, $where);
		}else{
			$result = $this->dao->insert($this->data);
		}
		return $result;
	}
}