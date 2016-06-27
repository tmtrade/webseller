<?php
/**
 * 站内信基础业务模型
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
	$r['order'] = array('pages'=>'asc');
	
	$r['raw'] = ' date>= '.date("Y-m-01");
	$r['in'] = array('isUse' => array(1,3));
	$r['order'] = array('pages'=>'asc');
	$r['group'] = array('pages' => 'asc');
	
        $res = $this->import('exchange')->find($r);
        return $res;
    }

    
    /**
     * 添加广告兑换信息
     * @param array $data
     */
    public function addExchange($data){
	$sort = $this->getMaxSort($data['pages']);
	$data['isUse'] = 3;
	$data['sort'] = $sort+1;
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
    
    
    /**
     * 获取页面广告最大兑换排序值
     * @param array $data
     */
    public function getMaxSort($pages){
	$r['col']   = array('max(sort)as sort');
	$r['eq'] = array('pages'=>$pages);
        $res = $this->import('exchange')->find($r);
	return $res['sort'];
    }

}