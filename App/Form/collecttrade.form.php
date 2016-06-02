<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class CollectTradeForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'status' => array(
			'field' => 'status',
			'method' => 'doStatus', 
			),
		'regdate' => array(
			'field' => 'regdate',
			'method' => 'doDate', 
			),
		'keyword' => array(
			'field' => 'keyword',
			'method' => 'doKeyword', 
			),
		);	

	/**
	 * 处理页面提交数据
	 * @author	martin
	 * @since	2016/2/18
	 * 
	 * @access	public
	 * @param	string	$value	页面提交数据
	 * @return	int
	 */
	public function doStatus($value)
	{
		$value =  intval($value) > 28 ? 0 : intval($value) ;
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
	public function doKeyword($value)
	{
		return  trim($value);
	}
}
?>