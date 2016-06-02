<?
/**
 * 应用业务组件基类
 *
 * 网络信息
 * 
 * @package	Model
 * @author	void
 * @since	2015-11-20
 */
class NetworkModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'user'		=> 'user',
        'verify'	=> 'verify'
    );
	
    /**
     * 网络信息添加
     * @author  haydn
     * @since   2016-02-25
     * array	$array		提交数据包
     * @return  array		$array
     */
    public function networkJoin($array)
	{
		$pttype				= '';
		$pttypeArr			= array( 1 => '求购', 2 => '出售' );
		if( !empty($array['pttype']) ){
			if( in_array($array['pttype'],$pttypeArr) ){
				$pttype		= $array['pttype'];
			}elseif( array_key_exists($array['pttype'],$pttypeArr) ){
				$pttype		= $pttypeArr[$array['pttype']];
			}else{
				$pttype		= $pttypeArr[1];
			}
		}
		$post['type'] 		= !empty($array['type']) ? $array['type'] : 5;
		$post['referer'] 	= !empty($array['referer']) ? $array['referer'] : '';
		$post['username']	= !empty($array['username']) ? $array['username'] : '';
		$post['source'] 	= !empty($array['source']) ? $array['source'] : 0;//来源站点（0：后台查标[.COM] 2：在线咨询[.COM]  4：400电话咨询）
		$post['company'] 	= !empty($array['company']) ? $array['company'] : '';//公司名称
		$post['pttype'] 	= $pttype; //类型（1：求购 2：出售）
		$post['subject'] 	= !empty($array['subject']) ? $array['subject'] : '';//注册名称
		$post['remarks']	= !empty($array['remarks']) ? $array['remarks'] : '';//备注
		$post['name'] 		= !empty($array['name']) ? $array['name'] : '';//联系人
		$post['address'] 	= !empty($array['address']) ? $array['address'] : '';//客户联系地址
		$post['postcode'] 	= !empty($array['postcode']) ? $array['postcode'] : '';//客户邮编
		$post['tel'] 		= !empty($array['tel']) ? $array['tel'] : '';//电话
		$post['email'] 		= !empty($array['email']) ? $array['email'] : '';//邮件
		$post['ptype'] 		= !empty($array['ptype']) ? $array['ptype'] : '';//专利的类别
		$post['area'] 		= !empty($_GET['area']) ? $_GET['area'] : '';//地区
		$post['sid'] 		= !empty($_GET['sid']) ? $_GET['sid'] : '';
		$data				= $this->importBi('crm')->networkJoin($post);
		return $data;
	}
	
    
}
?>