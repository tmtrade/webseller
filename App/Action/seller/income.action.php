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
        $type = $this->input('type','int',0);
        $start0 = $this->input('start','string','');
        $end0 = $this->input('end','string','');
        $page = $this->input('page','int',1);
        $size = $this->input('size','int',20);
        $start = strtotime($start0);
        $end = strtotime($end0);
        //得到分页数据
        $res = $this->load('income')->getPageList($type,$start,$end,$page,$size);
        $count = $res['total'];
        $data = $res['rows'];
        //得到分页工具条
        $pager 	= $this->pagerNew($count, $size);
        $pageBar 	= empty($data) ? '' : getPageBarNew($pager);
        $this->set("pageBar",$pageBar);
        $this->set("list",$data);
        //得到收益总额及销售商品数
        $income_res = $this->load('income')->getIncome($type,$start,$end);
        $this->set('income',$income_res['price']);
        $this->set('count',$income_res['count']);
        //下拉的文字
        if($type==2){
            $this->set('type_income','全部');
        }else if($type==0){
            $this->set('type_income','商标');
        }else{
            $this->set('type_income','专利');
        }
        $this->set('size',$size);
        //得到时间的描述文字
        if($start0 && $end0){
            $date_str = "从{$start0}日 至 {$end0} 日期间";
        }else if($start0 && !$end0){
            $date_str = "从{$start0}日至今";
        }else if(!$start0 && $end0){
            $date_str = "截止到{$end0}";
        }else{
            $date_str = "截止到现在";
        }
        $this->set('start',$start0);
        $this->set('end',$end0);
        $this->set('date_str',$date_str);
        //渲染页面
        $this->display();
    }

}