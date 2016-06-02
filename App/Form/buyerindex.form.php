<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class BuyerIndexForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'type' => array(
			'field' => 'type',
			'method' => 'doState', 
			),
		'startdate' => array(
			'field' => 'startdate',
			'method' => 'doTime', 
			),
		'enddate' => array(
			'field' => 'enddate',
			'method' => 'doTime', 
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
	public function doState($value)
	{
		$value = intval($value);
		//$value = $value > 5 ? 0 : $value;
		return  $value;
	}	
}
?>