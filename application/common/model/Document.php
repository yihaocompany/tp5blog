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
class Document extends Base{

	protected $fk = 'doc_id';
	protected $pk = 'id';
	protected $extend_db;

	// 定义需要自动写入时间戳格式的字段
	protected $autoWriteTimestamp = array('create_time','update_time','deadline');

	protected $auto = array('doc_id', 'title', 'description', 'update_time','deadline');
	protected $insert = array('uid', 'attach'=>0, 'view'=>0, 'comment'=>0, 'extend'=>0, 'create_time', 'status');

	protected $type = array(
		'cover_id'  => 'integer',
		'link_id'  => 'integer',
		'level'  => 'integer',
		'comment'  => 'integer',
		'view'  => 'integer',
		'create_time'  => 'integer',
		'update_time'  => 'integer',
	);

	protected function setUidAttr(){
		return session('user_auth.uid');
	}

	protected function setDocIdAttr(){
		return input('id','','intval,trim');
	}

	protected function setDeadlineAttr($value){
		return $value ? strtotime($value) : time();
	}

	protected function setCreateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	/**
	 * 获取数据状态
	 * @return integer 数据状态
	 * @author huajie <banhuajie@163.com>
	 */
	protected function setStatusAttr($value){
		$cate = input('post.category_id');
		$check = db('Category')->getFieldById($cate, 'check');
		if($check){
			$status = 2;
		}else{
			$status = 1;
		}
		return $status;
	}

	protected function getTagsAttr($value){
		if ($value) {
			return explode(',', $value);
		}
	}

	public function extend($name){
		if (is_numeric($name)) {
			$name = db('Model')->where('id', $name)->value('name');
		}
		$this->extend_db = db('Document' . ucfirst($name));
		$name = strtoupper($name);
		//$this->join('__DOCUMENT_' . $name . '__', $this->fk . '=' . $this->pk, 'LEFT');
		$this->dao = db('Document')->alias('d')
			->join('__DOCUMENT_' . $name . '__ dc', 'dc.' . $this->fk . '= d.' . $this->pk, 'RIGHT');
		return $this;
	}

	public function lists($map, $order){
		$list = $this->dao->where($map)->order($order)->paginate(15, false, array(
			'query' => $this->param,
		));
		return $list;
	}

	public function change(){
		/* 获取数据对象 */
		$data = \think\Request::instance()->post();

		if ($data !== false) {
			//增加增加复选框 shu'zu数组保存 
			foreach($data as $key=>$val){
				if(is_array($val)){
					$data[$key] = implode(',', $val);
				}
			}
			/* 添加或新增基础内容 */
			if(empty($data['id'])){ //新增数据
				unset($data['id']);
				$id = $this->validate('document.edit')->save($data); //添加基础内容
				if(!$id){
					return false;
				}else{
					$data['doc_id'] = $this->id;
					$this->extend_db->insert($data);
				}
				$data['id'] = $id;
			} else { //更新数据
				$status = $this->validate('document.edit')->save($data, array('id'=>$data['id'])); //更新基础内容
				if(false === $status){
					return false;
				}else{
					$this->extend_db->where($this->fk, $data['id'])->update($data);
				}
			}
			return $data['id'];
		}else{
			return false;
		}
	}

	public function del($map){
		$result = $this->where($map)->delete();
		$this->extend_db->where(array('doc_id'=>$map['id']))->delete();
		return $result;
	}

	public function detail($id){
		$data = $this->dao->where('id',$id)->find();
		$map = array('model_id'=>$data['model_id'], 'type'=>array('in', 'checkbox'));
		$model_type = db('attribute')->where($map)->column('name');
		foreach($model_type as $val){
			$data->setAttr($val, explode(',', $data[$val]));
		}

		return $data;
	}

	public function recom($id, $field = '*', $limit = 10, $order = 'id desc'){
		$tag = $this->where(array('id'=>$id))->value('tags');
		$map = '';
		if ($tag) {
			$tags = explode(',', $tag);
			foreach ($tags as $item) {
				$where[] = 'tags LIKE "%' . $item . '%"';
			}
			$map = implode(' OR ', $where);
		}
		$list = $this->where($map)->field($field)->limit($limit)->order($order)->select();
		if (empty($list)) {
			return $list;
		}else{
			return $this->field($field)->limit($limit)->order($order)->select();
		}
	}
}