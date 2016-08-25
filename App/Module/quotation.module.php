<?
/**
 * 商品报价单
 * 
 * 查询、创建
 *
 * @package	Module
 * @author	Far
 * @since	2016-08-25
 */
class quotationModule extends AppModule
{
	
    /**
     * 引用业务模型
     */
    public $models = array(
        'quotation'         => 'quotation',
        'quotationItems'    => 'quotationItems',
        'userImage'         => 'userImage',
    );
}
?>