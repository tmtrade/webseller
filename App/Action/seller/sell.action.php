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
        $proposer_id = $this->input('proposer_id','int');
        if(!$proposer_id) return array('code'=>1,'msg'=>'申请人不能为空');
        //得到申请人商标数据
        $rst = $this->load('sell')->getPersonTm($proposer_id);
        return array('code'=>0);
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
    
    //excel文件上传
    public function ajaxUploadExcel()
    {
        $msg = array(
            'code'  => 0,
            'msg'   => '',
            'filename'   => '',
            );
        if ( empty($_FILES) || empty($_FILES['fileName']) ) {
            $msg['msg'] = '请上传EXCEL文件';
            $this->returnAjax($msg);
        }
        $obj = $this->load('upload')->uploadExcel('fileName', 'excel');
        if ( $obj->fileurl ){
            $msg = $this->PHPExcelToArr($obj->fileurl);
        }else{
            $msg['msg']     = $obj->msg;
        }
        $this->returnAjax($msg);
    }


    //把文件里面的数据读取出来，然后组成一个数组返回  
    public function PHPExcelToArr($filePath){
	    $SBarr = $this->load('excel')->PHPExcelToArr($filePath);
	    /**商标已传的黑名单  不存在该商标      上传成功的  上传失败的 黑名单**/
	    $saleExists = $saleNotHas = $saleSucess = $saleError = $saleNotContact = array();
	    if($SBarr){
		    if(isset($SBarr['statue']) && $SBarr['statue'] == 1){
			    $data['code']  = 0;
			    $data['msg']   = '上传数量超过100条';
		    }else{
			    foreach($SBarr as $k => $item){
				
				    if((!$item['phone']) || (!$item['name'])){
					    $saleNotContact[] = $item;
					    continue;
				    }
				    $tmInfo = $this->load('sell')->getTmInfo($item['number']);
				    if(!$tmInfo){//不存在该商标
					    $saleNotHas[] = $item;
				    }else{
					    //商标已上传的
					    $saleB = $this->load('sale')->getSaleInfo($item['number']);
					    if($saleB){
						    $saleExists[] = $item;
						    $saleBContact         = $this->load("sell")->existContact($item['number'],UID,$item['phone']);
						    //如果没有这个联系人，就写入这个联系人信息
						    if(!$saleBContact){
							    $result = $this->load('sell')->documentAddSell($item);
							    if($result){
								$saleSucess[] = $item;
							    }else{
								$saleError[] = $item;
							    }
						    }else{
							$saleContact[] = $item;
						    }
					    }else{
						    //开始写入商标
						    $result = $this->load('sell')->documentAddSell($item);
						    if($result){
							    $saleSucess[] = $item;
						    }else{
							    $saleError[] = $item;
						    }
					    }
				    }
			    }
			    $numSucess = count($saleSucess);
			    $data['code']  = 1;
			    $data['alldata'] = count($SBarr);
			    $data['sucdata'] = $numSucess;
			    $data['errdata'] = (count($SBarr)-$numSucess);
			    if($data['errdata'] > 0){
				    $data['filepath'] = $this->load('excel')->upErrorExcel($saleExists, $saleNotHas, $numSucess, $saleError, $saleNotContact,$saleContact);
			    }
		    }
	    }else{
		    //没有商标数据
		    $data['code']  = 0;
		    $data['msg']   = '文件没有数据';
	    }

	    if(file_exists(FILEDIR.$filePath)){
	      unlink(FILEDIR.$filePath);
	    }
	    return $data;
    }
}