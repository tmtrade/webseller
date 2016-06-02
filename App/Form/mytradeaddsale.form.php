<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class MytradeAddsaleForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'number' => array(
			'field' => 'number',
			'method' => 'toString', 
			),
		'name' => array(
			'field' => 'name',
			'method' => 'toString', 
			),
		'phone' => array(
			'field' => 'phone',
			'method' => 'toString', 
			),
		'price' => array(
			'field' => 'price',
			'method' => 'doInt', 
			),
		'pricetype' => array(
			'field' => 'pricetype',
			'method' => 'doInt', 
			),
		'saleType' => array(
			'field' => 'saleType',
			'method' => 'doInt', 
			),
		);

	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/3/2
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function toString($value)
	{
		return trim($value);
	}

	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/3/2
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doInt($value)
	{
		return intval($value);
	}
	
}
?>