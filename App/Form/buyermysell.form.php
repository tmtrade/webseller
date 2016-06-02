<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class BuyerMysellForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'status' => array(
			'field' => 'status',
			'method' => 'doState', 
			),
		'keywords' => array(
			'field' => 'keywords',
			'method' => 'doName', 
			),
		'regdate' => array(
			'field' => 'regdate',
			'method' => 'doDate', 
			),
		'startprice' => array(
			'field' => 'startprice',
			'method' => 'doStartprice', 
			),
		'endprice' => array(
			'field' => 'endprice',
			'method' => 'doEndprice', 
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
	/**
	 * 处理页面提交数据
	 * @author	haydn
	 * @since	2016/3/1
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doName($value)
	{
		$value = addslashes($value);
		return $value;
	}
	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/2/18
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doDate($value)
	{
		$value = $value  == 'asc' ? 'asc' : 'desc';
		return  $value;
	}
	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/2/18
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doStartprice($value)
	{
		$value = addslashes($value);
		return  $value;
	}
	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/2/18
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doEndprice($value)
	{
		$value = addslashes($value);
		return  $value;
	}
}
?>