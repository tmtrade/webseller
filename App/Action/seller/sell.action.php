<?php
/**
 * 商品出售控制器
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/24 0024
 * Time: 上午 9:36
 */
class SellAction extends AppAction{

    /**
     * 加载首页
     */
    public function index(){
        $this->display();
    }

    /**
     * 通过商标号添加商品
     */
    public function number(){
        $this->display();
    }

    /**
     * 添加商标--通过商标号
     */
    public function addNumber(){
        $params = $_POST;
        $rst = $this->load('sell')->addSell($params,20);
        $this->returnAjax($rst);
    }

    /**
     * 获取商标信息
     */
    public function getTminfo()
    {
        $number	= $this->input("number","string");
        if ( empty($number) ) $this->returnAjax(array('code'=>1,'msg'=>'商标号不能为空'));
        //判断用户是否已出售过
        $isSale         = $this->load("sell")->existContact($number,UID);
        if($isSale) $this->returnAjax(array('code'=>2,'msg'=>'您已经对该商标提出过报价'));
        //判断商标是否存在
        $info   = $this->load('sell')->getTmInfo($number);
        if ( empty($info) ) $this->returnAjax(array('code'=>3,'msg'=>'找不到对应商标，请查证重新输入'));
        //不能出售的商标
        $status = array('已无效','冻结中');
        foreach ($status as $s) {
            if( in_array($s, $info['second']) ){
                $this->returnAjax(array('code'=>4,'msg'=>'该商标状态不太适合出售呢'));
            }
        }
        //正常状态结果
        $data['code'] = 0;
        $data['name'] = $info['name'];
        $data['img']	= $info['imgUrl'];
        $this->returnAjax($data);
    }

    /**
     * 通过申请人添加商品
     */
    public function person(){
        $this->display();
    }

    /**
     * 得到申请人模糊查询的申请人列表
     * @return array
     */
    public function getPerson(){
        $person = $this->input('person','string','');
        if(!$person) return array('code'=>1,'msg'=>'申请人不能为空');
        $rst = $this->load('sell')->getPerson($person);
        return array('code'=>0);
    }

    //得到申请人的商标数据
    public function getPersonTm(){

    }

    /**
     * 通过申请人提交的商标号
     */
    public function addPerson(){
        $params = $_POST;
        $rst = $this->load('sell')->addSell($params,50);
        $this->returnAjax($rst);
    }

    /**
     * 通过excel文档添加商品
     */
    public function document(){
        $this->display();
    }
}