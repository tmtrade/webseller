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
    
    public $size = 8;
     
    public $ptype = 8; 

    /**
     * 商品报价单首页
     */
    public function index(){

        $this->display();
    }
    
    
   

}