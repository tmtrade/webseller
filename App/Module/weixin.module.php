<?
/**
 * 应用业务组件基类
 *
 * 微信处理
 * 
 * @package	Model
 * @author	void
 * @since	2015-11-20
 */
class WeixinModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'user'		=> 'user',
        'verify'	=> 'verify'
    );
	
    /**
     * 获取绑定二维码
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$uid  	 当前登录id
     * @return  array		$array
     */
    public function getBinDing($uid)
    {
       $array     = $this->importBi('weixin')->bindweixin($uid);
       return $array;
    }
    /**
     * 获取绑定微信id
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$uid	当前登录id
     * @return  int			$wxid	返回微信id
     */
    public function getBinDingId($uid)
    {
    	$data	= $this->import("user")->get($uid);
    	$wxid 	= !empty($data['wenxinId']) && $data['wenxinId'] > 0 ? $data['wenxinId'] : 0;
    	return $wxid;
    }
    /**
     * 获取绑定二维码
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$uid	当前登录id
     * @return  int			$code	返回状态(1:解绑成功 0：失败)
     */
    public function unBindweixin($uid)
    {
       $array	= $this->importBi('weixin')->unBindweixin($uid,2);
       $code	= $array['message'] == 'success' ? 1 : 0;
       return $code;
    }
    /**
     * 给设置用户微信id(或解绑)
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$uid	用户id
     * @param   int			$wxid	微信id
     * @return  void
     */
    public function setUserWxId($uid,$wxid)
    {
		$r['eq']	= array('id' => $uid);
		$data		= array('wenxinId' => $wxid);
		return $this->import('user')->modify($data, $r);
    }
    /**
     * 发送微信验证码
     * @author  haydn
     * @since   2016-02-25
     * @param   string		$account	用户手机
     * @param   int			$wxid		微信id
     * @return  void
     */
    public function sendWeiXinCode($account)
	{
		$cateId		= isCheck($account);
		$password 	= getRandChar(4,true);
		$id 		= $this->import('verify')->add($account,$password,$cateId);
		if( $id > 0 ){
			$title	= $cateId == 1 ? '邮件验证码' : '';
			$this->load('register')->sendPassword($account,$password,$cateId,'weixin',$title);
		}
		return $id;
	}
	/**
     * 获取语言
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$code	code编码
     * @return  string		$msg	对应话术
     */
    public function getWXCodeMsg($code)
	{
		switch($code){
			case 1;
				$msg = '发送成功';
			break;
			case 2;
				$msg = '未绑定微信';
			break;
			case 3;
				$msg = '手机不存在';
			break;
			case 4;
				$msg = '验证码发送失败请稍后再试';
			break;
			default;
				$msg = '服务器繁忙请稍后再试';
		}
		return $msg;
	}
    
}
?>