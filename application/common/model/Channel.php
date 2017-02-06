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
class Channel extends Base{

	protected $type = array(
		'id'  => 'integer',
	);

	protected $auto = array('update_time', 'status'=>1);
	protected $insert = array('create_time');
}