<?
/**
* 商标相关信息
*
* 查询商标基础信息、图片、状态等
*
* @package	Module
* @author	Xuni
* @since	2015-10-22
*/
class TrademarkModule extends AppModule
{
    public $models = array(
        'img'		=> 'imgurl',
        'tm'		=> 'trademark',
    );

    /**
    * 获取商标图片地址
    *
    * @author   Xuni
    * @since    2015-10-22
    *
    * @access   public
    * @return   void
    */
    public function getImg($number)
    {
        $default = '/Static/images/img1.png';
        if ( intval($number) <= 0 ) return $default;
        $r['eq']    = array('trademark_id'=>$number);
        $img        = $this->import('img')->find($r);
        return empty($img) ? $default : $img['url'];
    }
}
?>