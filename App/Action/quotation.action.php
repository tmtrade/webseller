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
        if($res['total']>0){
            //得到分页工具条
            $pager 	= $this->pagerNew($res['total'], $size);
            $pageBar 	= empty($res['rows']) ? '' : getPageBarNew($pager);
            $this->set("pageBar",$pageBar);
            $this->set('total',$res['total']);
            $this->set('list',$res['rows']);
            $this->display("quotation/quotation.list.html");
        }else{
            $this->display();
        }
    }

    /**
     *删除报价单
     */
    public function remove(){
        $id = $this->input('id','$id',0);
        $rst = $this->load('quotation')->delete($id);
        if($rst){
            $this->returnAjax(array('code'==0));
        }else{
            $this->returnAjax(array('code'==1,'msg'=>'删除报价单失败'));
        }
    }

    /**
     * 得到pdf文件
     * @return bool
     */
    public function getPdf(){
        $id = $this->input('id','int');
        if(!$id) exit('非法参数');
        //得到当前报价单的title
        $title = $this->load('quotation')->getTitle($id);
        if(!$title) exit('数据异常,请稍候再试');
        //得到pdf名
        $file = $this->findPdf($id);
        //下载文件
        $this->startDownPDF($file,'报价单-'.$title);
    }

    /**
     * 返回pdf文件路径
     * @param $id
     * @return string
     */
    private function findPdf($id){
        $file = StaticDir.'pdf/'.UID.'/'.$id.'.pdf';
        $dir = dirname($file);
        !file_exists($dir) && mkdirs($dir);//创建文件夹
        //得到文件内容
        if(!is_file($file)){
            $contents 	= file_get_contents(SITE_URL.'quotation/?id='.$id);
            $isPdf	= makePDF($contents,$file);
            $isPdf	== 1 && exit('生成PDF失败');
        }
        return $file;
    }

    /**
     * 下载文件
     * @since 	2016-01-27
     * @param	string	$pdffile	下载路径
     * @param	string	$downname	文件名称
     */
    private function startDownPDF($pdffile,$downname){
        $fp		= fopen($pdffile,"r");
        $size	= filesize($pdffile);
        $name	= iconv('utf-8', 'gbk',$downname);
        header("Content-type: application/pdf");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".$size);
        header("Content-Disposition: attachment; filename=".$name.".pdf"); // 输出文件内容
        echo fread($fp,$size);
        fclose($fp);
    }

}