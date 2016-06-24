<?php
/**
 * 站内信基础业务模型
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/7 0007
 * Time: 上午 11:53
 */
class GoodsModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'sale'		    => 'sale',
        'contact'           => 'saleContact',
        'history'           => 'saleHistory',
	'saleTminfo'        => 'saleTminfo',
        'userSaleHistory'   => 'userSaleHistory',
    );
    
    //获取出售中和审核中的数据
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('tid','class','number','name','(select price from t_sale_contact where saleId=t_sale.id and uid='.$params["uid"].') as price','date');
        $r['raw'] = ' 1 ';
        
        if ( !empty($params['name']) ){
            $r['like']['name'] = $params['name'];
        } 
        
        $r['raw'] .= " AND `id` IN (select distinct(`saleId`) from t_sale_contact where uid={$params['uid']} and isVerify={$params['status']}) ";
        $r['order'] = array('date'=>'desc');
        $res = $this->import('sale')->findAll($r);
	foreach($res['rows'] as &$v){
	    $v['pic'] = $this->load('sale')->getSaltTminfoByNumber($v['number']);
	}
        return $res;
    }
    
    //获取交易完成或驳回的数据
    public function usedList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('number','name','(select memo from t_sale_history where saleId=t_user_sale_history.saleId) as memo','date');
        $r['eq']['uid'] = $params['uid'];
	$r['raw'] = ' 1 ';
        
        if ( !empty($params['name']) ){
            $r['like']['name'] = $params['name'];
        } 
        if ( $params['status']==3 ){
            $r['raw'] .= "and type={$params['status']}";
	} else{
	    $r['raw'] .= "and type!=3";
	}
	$r['order'] = array('date'=>'desc');
        $res = $this->import('userSaleHistory')->findAll($r);
	foreach($res['rows'] as &$v){
	    $v['pic'] = $this->load('sale')->getSaltTminfoByNumber($v['number']);
	}
        return $res;
    }
    
   //修改报价
    public function updatePrice($number,$price,$uid){
	$res = $this->load('sale')->getSaleContactByUid($number, $uid);
	$param = array(
		'cid'           => $res['id'],//联系人信息ID
		'price'         => $price,//底价
	    );
	//调用接口
	$data = $this->importBi('sale')->updateContactPrice($param);
	return $data;//返回结果
    }

}