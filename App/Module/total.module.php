<?php
/**
 * 用户积分（蝉豆管理）
 * Created by PhpStorm.
 * User: Far
 * Date: 2016/6/27 0007
 * Time: 上午 11:53
 */
class TotalModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'total'		    => 'total',
        'totalLog'          => 'totalLog',
    );
    
    //获取用户蝉豆的记录列表
    public function getTotallogList($params,$page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
	$r['eq'] = array('uid'=>$params['uid']);
	$r['order'] = array('date'=>'desc');
        $res = $this->import('totalLog')->findAll($r);
        return $res;
    }
    
    //获取用户的蝉豆数
    public function getTotal($uid){
	$r['eq'] = array('uid'=>$uid);
	$r['col'] = array("amount");
        $res = $this->import('total')->find($r);
	return $res['amount'];
    }
    

    /**
     * 获取用户的蝉豆数
     * @param type $uid
     * @param type $type 1:增加 2：减少
     * @param type $amount
     * @return type
     */
    public function upTotal($uid,$type,$amount,$note){
	$r['eq'] = array('uid'=>$uid);
	if($type==1){
	    $record = array(
		'amount'    => array('amount', $amount),
	    );
	}else{
	    $record = array(
		'amount'    => array('amount', -$amount),
	    );
	}
	$this->begin('total');
	$res	 = $this->import("total")->modify($record, $r);
	if($res){
	    $date = array(
		'uid'	    => $uid,
		'types'	    => $type,
		'amount'    => $amount,
		'note'	    => $note,
	    );
	    $res1 = $this->addExchangeLog($date);
	    if($res1){
		$this->commit('total');
		return $res1;
	    }else{
		$this->rollBack('total');
	    }
	}
	return $res;
    }
        
    
    /**
     * 添加广告兑换信息
     * @param array $data
     */
    public function addExchangeLog($data){
	$res = $this->import('totalLog')->create($data);
	return $res;
    }
}