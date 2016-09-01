<?php
/**
 * 我的收益控制器
 * Created by PhpStorm.
 * User: Far
 * Date: 2016/8/25 0021
 * Time: 下午 14:10
 */
class QuotationAction extends AppAction{

    public $pageTitle   = "商品报价单-一只蝉出售者平台";
    
    public $size = 20;
     
    public $ptype = 8; 

    /**
     * 商品报价单首页
     */
    public function index(){
        $params['name']   = $this->input('name', 'string', '');
        $page = $this->input('page','int',1);
        $size = $this->input('size','int',$this->size);
        //得到分页数据
        $res = $this->load('quotation')->getList($params, $page, $size);
        if($res['total']>0 || $params['name']){
            //得到分页工具条
            $pager 	= $this->pagerNew($res['total'], $size);
            $pageBar 	= empty($res['rows']) ? '' : getPageBarNew($pager);
            $this->set("pageBar",$pageBar);
            $this->set('total',$res['total']);
            $this->set('list',$res['rows']);
            $this->set('name',$params['name']);
            $this->display("quotation/quotation.list.html");
        }else{
            $this->display();
        }
    }

    /**
     *删除报价单
     */
    public function remove(){
        $id = $this->input('id','int');
        if(!$id) $this->returnAjax(array('code'=>1,'msg'=>'参数异常'));
        $rst = $this->load('quotation')->delete($id);
        if($rst){
            $this->load('quotation')->delQuotationItems($id);
            $this->returnAjax(array('code'=>0));
            
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'删除报价单失败'));
        }
    }

    
    //添加商品页面
    public function addgoods(){
         $this->display();
        
    }
    
     //修改商品页面
    public function editgoods(){
         $id = $this->input('id','int');
         $info = $this->load('quotation')->getQuotationInfo($id);
         $number_list = $this->load('quotation')->getQuotationItemByQid($id);
         
         $this->set('info',$info);
         $this->set('number_list',json_encode($number_list['rows']));
         $this->set('id',$id);
         $this->display("quotation/quotation.addgoods.html");
        
    }
    
    /**
     * 获取商标信息
     */
    public function getTminfo()
    {
        $number	= $this->input("number","string");
        $qid	= $this->input("qid","int");
        
        if ( empty($number) ) $this->returnAjax(array('code'=>1,'msg'=>'商标号不能为空'));
        //判断商标是否存在
        $info   = $this->load('sell')->getTmInfo($number);
        if ( empty($info) ) $this->returnAjax(array('code'=>3,'msg'=>'找不到对应商标，请查证重新输入'));

        //不能出售的商标
        $status = array('商标已无效','冻结中');//module里也有一处
        foreach ($status as $s) {
            if( in_array($s, $info['second']) ){
                $this->returnAjax(array('code'=>4,'msg'=>'该商标状态不太适合出售呢'));
            }
        }
        if(!empty($qid)){
            $itemInfo = $this->load('quotation')->getQuotationItemInfo($qid,$number);
        }
        
        
        //正常状态结果
        $data['number']     = $number;
        $data['name']       = $info['name'];
        $data['class_str']  = $info['class'];
        $data['thum']       = mbSub($info['class'],0,18);
        
        //判断是否有包装图
        $is_tminfo = $this->load('sale')->getSaleTmByNumber($number);
        $data['is_tminfo'] = $is_tminfo;
        if(!$is_tminfo){
            $img = $this->load('quotation')->getUserImage($number);
            if(!empty($img)){
                $info['imgUrl'] = $img;
            }
        }
        $data['img']        = $info['imgUrl'];
        
        $this->set('info',$data);
        $this->set('itemInfo',$itemInfo);
        $this->set('label',C('QUOTATION_LABEL'));
        $tm = $this->fetch();
        $this->returnAjax(array('code'=>0,'msg'=>$tm));
    }
    
    /**
     * 添加商标--通过商标号
     */
    public function addNumber(){
        $data = $this->getFormData();	
        $rst = $this->load('quotation')->insertQuotation($data,12,$data['id']);
        $this->returnAjax($rst);
    }
    
    /**
     * 添加商标成功页面
     */
    public function addFinish(){
        $id = $this->input('id','int');
        $this->set('id',$id);
        $this->display();
    }

    /**
     * 
     * 得到图片文件
     * @return bool
     */
    public function getImg(){
        $id = $this->input('id','int');
        if(!$id) exit('非法参数');
        //得到当前报价单的title
        $title = $this->load('quotation')->getTitle($id);
        if(!$title) exit('数据异常,请稍候再试');
        //得到pdf名
        $file = $this->findImg($id);
        //下载文件
        $this->startDown($file,'报价单-'.$title);
    }

    /**
     * 返回pdf文件路径
     * @param $id
     * @return string
     */
    private function findImg($id){
        $file = StaticDir.'png/'.UID.'/'.$id.'.png';
        $dir = dirname($file);
        !file_exists($dir) && mkdirs($dir);//创建文件夹
        //得到文件内容
        if(!is_file($file)){
            $contents 	= file_get_contents(SITE_URL.'quotation/?id='.$id);
            $isPdf	= makePng($contents,$file);
            $isPdf	== 1 && exit('生成图片失败');
        }
        return $file;
    }

    /**
     * 下载文件
     * @since 	2016-01-27
     * @param	string	$pdffile	下载路径
     * @param	string	$downname	文件名称
     */
    private function startDown($pdffile,$downname){
        $fp		= fopen($pdffile,"r");
        $size	= filesize($pdffile);
        $name	= iconv('utf-8', 'gbk',$downname);
        header("Content-type: image/png");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".$size);
        header("Content-Disposition: attachment; filename=".$name.".png"); // 输出文件内容
        echo fread($fp,$size);
        fclose($fp);
    }
    
     //图片上传
    public function ajaxUploadPic()
    {
    	$kb = $this->input('size', 'int', 0);
        $msg = array(
            'code'  => 0,
            'msg'   => '',
            'img'   => '',
            );
        if ( empty($_FILES) || empty($_FILES['fileName']) ) {
            $msg['msg'] = '请上传图片';
            $this->returnAjax($msg);
        }
        if ( $kb > 0 && ($kb*1024 < $_FILES['fileName']['size']) ){
        	$msg['msg'] = "文件大小超过 $kb KB限制";
        	$this->returnAjax($msg);
        }
        $obj = $this->load('upload')->uploadAdPic('fileName', 'img');
        if ( $obj->_imgUrl_ ){
            $msg['code']    = 1;
            $msg['img']     = $obj->_imgUrl_;
        }else{
            $msg['msg']     = $obj->msg;
        }
        $this->returnAjax($msg);
    }

    /**
     * 报价单详情
     */
    public function view(){
        $id = $this->input('id','int');
        $uid = $this->input('u','int');
        if(!$id || !$uid) exit('参数不合法');
        //得到详情数据
        $res = $this->load('quotation')->getDetail($id,$uid);
        $this->set('list',$res);
        if(is_mobile_request()){
            $this->set('label',C('QUOTATION_LABEL2'));
            $this->display('quotation/quotation.wap.html');
        }else{
            $this->set('label',C('QUOTATION_LABEL'));
            if($res['style']==1){
                $this->display('quotation/quotation.index1.html');
            }else{
                $this->display('quotation/quotation.index2.html');
            }
        }
    }

}