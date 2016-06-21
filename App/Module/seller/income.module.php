<?php
/**
 * 我的收益业务模块
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/21 0021
 * Time: 下午 14:23
 */
class IncomeModule extends AppModule{

    public $models = array(
        'income' => 'income',
    );

    /**
     * 得到我的收益分页数据
     * @param $type int 商品类型 0混合,1商标,2专利
     * @param $start int 时间筛选的开始
     * @param $end int 时间筛选的结束
     * @param $page int 页码
     * @param $size int 分页数据大小
     * @return array
     */
    public function getPageList($type,$start,$end,$page,$size){
        $r = array();
        //处理时间问题
        if($start && !$end){
            $end = time();
        }
        if($end && !$start){
            $start = 1000;
        }
        if($start && $end){
            //保证end大于start
            if($start > $end){
                $start = $start ^ $end;
                $end = $start ^ $end;
                $start = $start ^ $end;
            }
            $start += 28800;//加8小时 时区问题
            $end += 115200;//加32小时 定位到当天24点
            $r['scope'] = array('date'=>array($start,$end));
        }
        //其他条件
        if($type!=3){
            $r['eq']['type'] = $type;
        }
        $r['eq']['uid'] = UID;//指定用户
        $r['page']  = $page;
        $r['limit'] = $size;
        $r['order'] = array('date'=>'desc');
        return $this->import('income')->findAll($r);
    }

    /**
     * 得到对应商品类型的收益总额及商品出售数
     * @param $type int 商品类型 0混合,1商标,2专利
     * @param $start int
     * @param $end int
     * @return int
     */
    public function getIncome($type,$start,$end){
        $r = array();
        //处理时间问题
        if($start && !$end){
            $end = time();
        }
        if($end && !$start){
            $start = 1000;
        }
        if($start && $end){
            //保证end大于start
            if($start > $end){
                $start = $start ^ $end;
                $end = $start ^ $end;
                $start = $start ^ $end;
            }
            $start += 28800;//加8小时 时区问题
            $end += 115200;//加32小时 定位到当天24点
            $r['scope'] = array('date'=>array($start,$end));
        }
        //其他条件
        if($type!=3){
            $r['eq']['type'] = $type;
        }
        $r['eq']['uid'] = UID;//指定用户
        $r['limit'] = 100000;
        $r['col'] = array('price');
        $rst = $this->import('income')->find($r);
        if($rst){
            $rst = arrayColumn($rst,'price');
            return array('count'=>count($rst),'price'=>array_sum($rst));
        }
        return  array('count'=>0,'price'=>0);
    }
}