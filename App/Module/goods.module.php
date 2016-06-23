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
    
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('number','name','(select price from t_sale_contact where saleId=t_sale.id and uid='.$params["uid"].') as price','date');
	$r['eq']['status'] = $params['status'];
	
        $r['raw'] = ' 1 ';
        
        if ( !empty($params['name']) ){
            $r['like']['name'] = $params['name'];
        } 
        
        $r['raw'] .= " AND `id` IN (select distinct(`saleId`) from t_sale_contact where uid={$params['uid']}) ";
        $r['order'] = array('date'=>'desc');
        $res = $this->import('sale')->findAll($r);
	foreach($res['rows'] as &$v){
	    $v['pic'] = $this->getSaltTminfoByNumber($v['number']);
	}
        return $res;
    }
    
    /**
     * 根据商标号得到商标的包装信息(无美化图,获得商标图)
     * @param $number
     * @return array|bool
     * @throws SpringException
     */
    public function getSaltTminfoByNumber($number){
        $r['eq'] = array('number'=>$number);
        $info = $this->import('saleTminfo')->find($r);
        //无销售数据
        if($info==false){
            $info = array();
            $info['alt1'] = '';
            $info['embellish'] = $this->load('trademark')->getImg($number);
            return $info;
        }
        //返回包装数据
        if($info['embellish']){
            $info['embellish'] = TRADE_URL.$info['embellish'];
        }else{
            $info['embellish'] = $this->load('trademark')->getImg($number);
        }

        return $info;
    }

}