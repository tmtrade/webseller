<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/12 0012
 * Time: 下午 17:54
 */
class MessegeAction extends AppAction{

    public $size = 7;

    /**
     * 站内信列表
     */
    public function index(){
        $page 	= $this->input('page', 'int', '1');
        //得到站内信分页数据信息
        $rst = $this->load('messege')->getMsg($page,$this->size);
        $count = $rst['total'];
        $data = $rst['data'];
        //得到分页工具条
        $pager = $this->pagerNew($count, $this->size);
        $pageBar 	= empty($data) ? '' : getPageBarNew($pager);
        $this->set("pageBar",$pageBar);
        $this->set("list",$data);
        $this->display();
    }

    /**
     * 站内信详情
     */
    public function view(){
        //暂时无站内信详情-------------跳转到首页-----------------
        $this->redirect('','/');
        $id = $this->input('id','int');
        $msginfo = $this->load('messege')->viewMsg($id);
        $this->set('msginfo',$msginfo);
    }

    /**
     * 链接类型的站内信,添加已读状态
     */
    public function read(){
        $id = $this->input('id','int');
        $rst = $this->load('messege')->modifyMsg($id);
        if($rst){
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'修改信息状态失败'));
        }
    }

    /**
     * 删除指定站内信
     */
    public function remove(){
        $id = $this->input('id','int');
        $rst = $this->load('messege')->deleteMsg($id);
        if($rst){
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'删除失败'));
        }
    }
}