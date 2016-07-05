<?php
/**
 * 我的商品基础业务模型
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
        $r['col']   = array('tid','class','number','name','(select price from t_sale_contact where saleId=t_sale.id and uid='.$params["uid"].') as price','(select date from t_sale_contact where saleId=t_sale.id and uid='.$params["uid"].') as date');
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
        $r['col']   = array('number','name','memo','date');
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
    
    //获取出售中和审核中的数据个数
    public function getSaleCount($uid,$type)
    {
	$r = array();
        $r['eq']['uid'] = $uid;
	$r['eq']['isVerify'] = $type;//是否审核（1：是，2：否）
        $res = $this->import('contact')->count($r);
        return $res;
    }
    
    //获取交易完成或驳回的数据个数
    public function getSaleCancelCount($uid,$type)
    {
	$r = array();
        $r['eq']['uid'] = $uid;
	if($type==3){
	    $r['eq']['type'] = $type;//类型（1：出售成功，2：后台删除，3：后台驳回，4：自行取消（已审核））
	}else{
	    $r['in'] = array('type' => array(1,2,4));
	}
        $res = $this->import('userSaleHistory')->count($r);
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
    
        
    /**
     * 取消报价
     * @param type $number
     * @param type $uid
     * @param type $type 1:出售中的取消  2：审核中的取消
     * @return type
     */
    public function cancelPrice($number,$uid){
	$param = array(
		'uid'           => $uid,//联系人信息ID
		'number'         => $number,//底价
	    );
	//调用接口
	$data = $this->importBi('sale')->cancelContact($param);
	return $data;//返回结果
    }

}