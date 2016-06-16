<?php
/**
 * 出售者用户控制器
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/12 0012
 * Time: 上午 11:27
 */
class MemberAction extends SellerAction{

    /**
     * 加载登录页面
     */
    public function index(){
        //用户是否登录
        if($this->isLogin){
            $this->redirect('','/member/main');//跳转到后台首页
        }
        $this->display();
    }

    /**
     * 出售者首页
     */
    public function main(){
        $this->display();
    }

    /**
     * 用户登录(ajax)
     */
    public function login(){
        //获得数据
        $type = $this->input('type','int');
        $account = $this->input('account','string');
        $code = $this->input('code','string');
        if($type){//密码登录
            $rst = $this->load('member')->passwordLogin($account,$code);
            if($rst){
                $this->returnAjax(array('code'=>0,'url'=>'/member/main'));
            }else{
                $this->returnAjax(array('code'=>1,'msg'=>'密码错误'));
            }
        }else{//验证码登录
            $rst = $this->load('member')->codeLogin($account,$code);
            if($rst==0){
                $this->returnAjax(array('code'=>0,'url'=>'/member/main'));
            }else if($rst==1){
                $this->returnAjax(array('code'=>1,'msg'=>'创建用户失败'));
            }else if($rst==2){
                $this->returnAjax(array('code'=>2,'msg'=>'创建创建超凡网账户失败'));
            }else{
                $this->returnAjax(array('code'=>3,'msg'=>'验证失败'));
            }
        }
    }

    /**
     * 发送手机验证码
     * @return int
     * @return string json结果
     */
    public function sendCode(){
        $mobile = $this->input('mobile','int');
        if(!$mobile){
            $this->returnAjax(array('code'=>3,'msg'=>'手机号码为空'));
        }
        $type = $this->input('type','int');
        if($type){
            $type = 'validBind';//绑定手机校验码
        }else{
            $type = 'newValid';//动态登录验证码
        }
        $rst = $this->load('member')->sendMsg($mobile,$type);
        if($rst){
            $this->returnAjax(array('code'=>0));
        }else if($rst==1){
            $this->returnAjax(array('code'=>1,'msg'=>'发送间隔小于一分钟'));
        }else if($rst==2){
            $this->returnAjax(array('code'=>2,'msg'=>'发送失败'));
        }else{
            $this->returnAjax(array('code'=>3,'msg'=>'手机号码为空'));
        }
    }

    /**
     * 退出登录
     */
    public function loginOut(){
        $this->load('member')->loginOut();
        $this->redirect('','/member/index');
    }

    /**
     * 加载验证手机页面
     */
    public function mobilePage(){

    }

    /**
     * 验证手机
     */
    public function checkPhone(){
        $phone = $this->input('account','int');
        $code = $this->input('code','int');
        if($phone && $code){
            $rst = $this->import('member')->bindMobile($phone,$code);
            if($rst==0){
                $this->returnAjax(array('code'=>0,'url'=>'/member/main'));
            }else if($rst==1){
                $this->returnAjax(array('code'=>1,'msg'=>'验证失败'));
            }else{
                $this->returnAjax(array('code'=>2,'msg'=>'超凡网更新失败'));
            }
        }else{
            $this->returnAjax(array('code'=>3,'msg'=>'参数错误'));
        }

    }

    /**
     * 加载验证邮箱页面
     */
    public function emailPage(){

    }

    /**
     * 验证邮箱
     */
    public function checkEmail(){

    }

    /**
     * 加载修改密码页面
     */
    public function editPassPage(){

    }

    /**
     * 修改密码
     */
    public function editPass(){

    }
}