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
    
    //获取报价商品的数据
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('id','title','created');
        $r['raw'] = ' 1 ';
        
        if ( !empty($params['name']) ){
            $r['like']['title'] = $params['name'];
        } 
        
        $r['order'] = array('created'=>'desc');
        $res = $this->import('quotation')->findAll($r);
        return $res;
    }
}
?>