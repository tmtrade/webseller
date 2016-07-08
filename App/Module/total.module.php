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
     *  变更用户的蝉豆数
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
		$params = array();
		$params['title'] = "你的蝉豆产生了变化，点击查看详情";
		$params['type'] = 2;
		$params['sendtype'] = 1;
		$params['content'] = "/exchange/?page=1&t=1";
		$params['uids'] = $uid;//当前用户
		$this->load('messege')->createMsg($params);
		$this->commit('total');
		return $res1;
	    }else{
		$this->rollBack('total');
	    }
	}
	return $res;
    }
    
    
    /**
     *  变更用户的商品审核通过数
     * @param type $uid
     * @param type $type 1:增加 2：减少
     * @return $res
     */
    public function updatePassCount($uid,$type){
	$r['eq'] = array('uid'=>$uid);
	if($type==1){
	    $record = array(
		'pass_count'    => array('pass_count', 1),
	    );
	}else{
	    $record = array(
		'pass_count'    => array('pass_count', -1),
	    );
	}
	$res	 = $this->import("total")->modify($record, $r);
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