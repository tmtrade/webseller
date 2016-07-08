<?php
/**
 * 兑换信息基础业务模型
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/7 0007
 * Time: 上午 11:53
 */
class ExchangeModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'exchange'           => 'exchange',
    );
    
    //广告栏位已申请的个数
    public function getAdCount()
    {
        $r = array();
	$r['limit'] = 5;
	$r['col']   = array('pages,count(1)as counts');
	
	$r['raw'] = "date>= '".date("Y-m-01")."'";
	$r['in'] = array('isUse' => array(1,3));
	$r['order'] = array('pages'=>'asc');
	$r['group'] = array('pages' => 'asc');
	
        $res = $this->import('exchange')->find($r);
        return $res;
    }
    
    //获取用户最近一次兑换的信息
    public function getOngInfo($uid)
    {
        $r = array();
	$r['eq']['uid'] = $uid;
	$r['order'] = array('date'=>'desc');
        $res = $this->import('exchange')->find($r);
        return $res;
    }
    //计算用户当月兑换个数
    public function getPagesCount($uid, $pages)
    {
        $r = array();
	$r['eq']['uid'] = $uid;
	$r['eq']['pages'] = $pages;
	$r['raw'] = ' date>= '.date("Y-m-01");
	$r['in'] = array('isUse' => array(1,3));
        $res = $this->import('exchange')->count($r);
        return $res;
    }
    
    /**
     * 添加广告兑换信息
     * @param array $data
     */
    public function addExchange($data){
	$data['isUse'] = 3;
	$this->begin('exchange');
	$res = $this->import('exchange')->create($data);
	if($res){
	     $r = $this->load('total')->upTotal($data['uid'],2,$data['amount'],"兑换广告");//修改用户豆豆
	     if($r){
		 $this->commit('exchange');
		 return $r;
	     }else{
		 $this->rollBack('exchange');
	     }
	}
	return $res1;
    }


}