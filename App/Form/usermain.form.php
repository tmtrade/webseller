<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class UserMainForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'nickname' => array(
			'field' => 'nickname',
			'method' => 'doTime', 
			),
		'name' => array(
			'field' => 'name',
			'method' => 'doTime', 
			),
		'sex' => array(
			'field' => 'sex',
			'method' => 'doSex', 
			),
		'follow' => array(
			'field' => 'follow',
			'method' => 'doFollow', 
			),
		);
	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/1/19
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doTime($value)
	{
		return trim($value);
	}

	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/1/19
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doSex($value)
	{
		$value = intval($value);
		return  $value == 1 ? 1 : 0;
	}
	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/1/19
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doFollow($value)
	{
		$value = rtrim($value,',');
		return  $value;
	}
	
}
?>