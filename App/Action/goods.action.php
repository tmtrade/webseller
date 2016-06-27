<?php
/**
 * 我的收益控制器
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/21 0021
 * Time: 下午 14:10
 */
class GoodsAction extends AppAction{

    public $size = 8;

    /**
     * 得到收益列表数据
     */
    public function index(){
        //获得参数
        $params['status'] = $this->input('status','int',1);
	$params['name']   = $this->input('name', 'string', '');
	$size = $this->input('size','int',20);
	$params['uid']    = $this->userinfo['id'];
        $page = $this->input('page','int',1);
        //得到分页数据
	if($params['status']==1 || $params['status']==2){
	    $res = $this->load('goods')->getList($params, $page, $size);
	}else{
	    $res = $this->load('goods')->usedList($params, $page, $size);
	}
        
        $count = $res['total'];
        $data = $res['rows'];
        //得到分页工具条
        $pager 	= $this->pagerNew($count, $size);
        $pageBar 	= empty($data) ? '' : getPageBarNew($pager);
        $this->set("pageBar",$pageBar);
        $this->set("list",$data);
	$this->set("count",$count);
	$this->set("size",$size);
//	echo "<pre>";
//	print_r($data);
	$this->set("s",$params);
        $this->display();
    }
    
    
    //修改报价
    public function updatePrice(){
	$number = $this->input('number','int',0);
	$price = $this->input('price','int',0);
	$result = array('code'=>'2');
	if($number<=0) $this->returnAjax($result);
	$data =  $this->load('goods')->updatePrice($number,$price,$this->userinfo['id']);
	$this->returnAjax($data);//返回结果
    }
    
    //取消报价或删除报价
    public function cancelPrice(){
	$number = $this->input('number','int',0);
	$result = array('status'=>'2');
	if($number<=0) $this->returnAjax($result);
	$data =  $this->load('goods')->cancelPrice($number,$this->userinfo['id']);
	$this->returnAjax($data);//返回结果
    }

}