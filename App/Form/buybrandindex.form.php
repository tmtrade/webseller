<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class BuyBrandIndexForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'account' => array(
			'field' => 'account',
			'method' => 'doAccount', 
			),
		'tid' => array(
			'field' => 'tid',
			'method' => 'doTid', 
			),
		'trademark' => array(
			'field' => 'trademark',
			'method' => 'doTrademark', 
			),
		'class' => array(
			'field' => 'class',
			'method' => 'doClass', 
			),
		'remarks' => array(
			'field' => 'remarks',
			'method' => 'doRemarks', 
			),
		'subject' => array(
			'field' => 'subject',
			'method' => 'doSubject', 
			),	
		'tel' => array(
			'field' => 'tel',
			'method' => 'doTel', 
			),	
		'callback' => array(
			'field' => 'callback',
			'method' => 'doCallback', 
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
	public function doAccount($value)
	{
		$value = addslashes($value);
		return $value;
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
	public function doTid($value)
	{
		$value = intval($value);
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
	public function doTrademark($value)
	{
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
	public function doClass($value)
	{
		$value =  intval($value) > 45 ? 0 : intval($value) ;
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
	public function doRemarks($value)
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
	public function doCallback($value)
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
	public function doSubject($value)
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
	public function doTel($value)
	{
		$value = addslashes($value);
		return  $value;
	}
	
	
}
?>