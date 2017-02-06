<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.aiyi.info All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@aiyi.info> <http://www.aiyi.info>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
* 设置模型
*/
class Document extends Base{

	protected $rule = array(
		'title'   => 'require',
	);
	
	protected $message = array(
		'title.require'   => '字段标题不能为空！',
	);
	
	protected $scene = array(
		'add'   => 'title',
		'edit'   => 'title'
	);
}