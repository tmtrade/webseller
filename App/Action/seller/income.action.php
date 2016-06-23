<?php
/**
 * 我的收益控制器
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/21 0021
 * Time: 下午 14:10
 */
class IncomeAction extends AppAction{

    /**
     * 得到收益列表数据
     */
    public function index(){
        //获得参数
        $type = $this->input('type','int',3);
        $start = $this->input('start','string','');
        $end = $this->input('end','string','');
        $page = $this->input('page','int',1);
        $size = $this->input('size','int',20);
        $start = strtotime($start);
        $end = strtotime($end);
        //得到分页数据
        $res = $this->load('income')->getPageList($type,$start,$end,$page,$size);
        $count = $res['total'];
        $data = $res['data'];
        //得到分页工具条
        $pager 	= $this->pagerNew($count, $size);
        $pageBar 	= empty($data) ? '' : getPageBarNew($pager);
        $this->set("pageBar",$pageBar);
        $this->set("list",$data);
        //得到收益总额及销售商品数
        $income_res = $this->load('income')->getIncome($type,$start,$end);
        $this->set('income',$income_res['price']);
        $this->set('count',$income_res['count']);
        $this->set('size',$size);
        //渲染页面
        $this->display();
    }

}