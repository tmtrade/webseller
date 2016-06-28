<?php
/**
 * 我的广告兑换和积分记录
 * Created by PhpStorm.
 * User: Far
 * Date: 2016/6/24 0021
 * Time: 下午 14:10
 */
class ExchangeAction extends AppAction{

    public $size = 20;

    /**
     * 得到收益列表数据
     */
    public function index(){
        //获得参数
	$params['uid']    = $this->userinfo['id'];
        $page = $this->input('page','int',1);
        //获取蝉豆记录
	$res = $this->load('total')->getTotallogList($params, $page, $this->size);
        $count = $res['total'];
        $data = $res['rows'];
        //得到分页工具条
        $pager 	= $this->pagerNew($count, $this->size);
        $pageBar 	= empty($data) ? '' : getPageBarNew($pager);
	
	//获取广告兑换剩余数
	$adCountList = $this->load('exchange')->getAdCount();
	$this->set("adCountList",$adCountList);
	$this->set('ad_config',C('ADCONFIG'));
        $this->set("pageBar",$pageBar);
        $this->set("list",$data);
	$this->set("s",$params);
        $this->display();
    }
    
    //广告兑换
    public function addEx(){
	$params['pages']    = $this->input('pages','int',0);
	$params['note']	    = $this->input('note', 'string', '');
	$params['phone']    = $this->input('phone', 'string', '');
	$params['qq']	    = $this->input('qq', 'int', '');
	$params['uid']	    = $this->userinfo['id'];
	
	$code = 2;
	if(empty($params['pages'])) $this->returnAjax(array("code"=>$code,"msg"=>"数据错误"));
	
	$adConfig = C('ADCONFIG');
	$params['amount'] = $adConfig[$params['pages']]['amount'];
	$res =  $this->load('exchange')->addExchange($params);
	if($res){
	    $code = 1;
	}
	$this->returnAjax(array("code"=>$code,"msg"=>$msg));
    }

}