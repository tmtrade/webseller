<?
/**
 * 应用公用表单组件
 *
 * 表单数据收集
 *
 * @package	Form
 * @author	Xuni
 * @since	2015-11-11
 */
class QuotationAddNumberForm extends AppForm
{
	
	/**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'id'        => array( 'field' => 'id', 'method' => 'fieldInt', ),
        'title' 	=> array( 'field' => 'title', 'method' => 'fieldString', ),
        'desc'    	=> array( 'field' => 'desc', 'method' => 'fieldString', ),
        'number' 	=> array( 'field' => 'number', 'method' => 'fieldName', ),
        'price'    	=> array( 'field' => 'price', 'method' => 'fieldName', ),
        'label'   	=> array( 'field' => 'label', 'method' => 'fieldName', ),
        'sort'          => array( 'field' => 'sort', 'method' => 'fieldName', ),
        'image'          => array( 'field' => 'image', 'method' => 'fieldName', ),
        'mobile'         => array( 'field' => 'mobile', 'method' => 'fieldString', ),
        'name'          => array( 'field' => 'name', 'method' => 'fieldString', ),
        'qq'            => array( 'field' => 'qq', 'method' => 'fieldString', ),
        'avatar'        => array( 'field' => 'avatar', 'method' => 'fieldInt', ),
        'style'         => array( 'field' => 'style', 'method' => 'fieldInt', ),
        'is_add'        => array( 'field' => 'is_add', 'method' => 'fieldInt', ),
        
    );

    /**
     * 处理字符串
 	 * @author	Xuni
 	 * @since	2015-11-11
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldString($value)
    {
        return empty($value) ? '' : urldecode(htmlspecialchars(trim($value)));
    }

    /**
     * 处理数字
 	 * @author	Xuni
 	 * @since	2015-11-11
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldInt($value)
    {
        return intval(trim($value));
    }
    
    public function fieldName($value)
    {
        return $value;
    }
}
?>