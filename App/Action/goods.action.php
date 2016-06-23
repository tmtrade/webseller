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
	$params['uid']    = $this->userinfo['id'];
        $page = $this->input('page','int',1);
        //得到分页数据
        $res = $this->load('goods')->getList($params, $page, $this->size);
        $count = $res['total'];
        $data = $res['rows'];
        //得到分页工具条
        $pager 	= $this->pagerNew($count, $this->size);
        $pageBar 	= empty($data) ? '' : getPageBarNew($pager);
        $this->set("pageBar",$pageBar);
        $this->set("list",$data);
	$this->set("count",$count);
//	echo "<pre>";
//	print_r($data);
	$this->set("s",$params);
        $this->display();
    }

}