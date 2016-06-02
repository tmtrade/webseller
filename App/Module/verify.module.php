<?
/**
 * 应用业务组件基类
 *
 * 存放业务组件公共方法
 * 
 * @package	Model
 * @author	void
 * @since	2015-11-20
 */
class VerifyModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'verify'		=> 'verify',
    );
	
    /**
     * 获取最新验证码
     * @author  martin
     * @since   2016-01-18
     * @param   string     $account  登录账户
     * @param   string     $account  验证码
     * @param   int        $cateId   账户标识(1邮件、2手机、3用户名)
     * @return  bool
     */
    public function getVerify( $account, $verify, $cateId = 2 )
    {
        $type		= $type == 1 ? 1 : 0;
		$r['eq']	= array('object'=>$account ,'type' => $cateId);
		$r['raw']	= "endDate > " . time();
		$r['limit']	= 1;
		$r['order']	= array('id' => 'desc');
		$data		= $this->import('verify')->find($r);
		if(empty($data)){
			$code = array( 'code' => 2, 'mess' => '请点击重新获取验证码');
		}elseif($verify != $data['verify']){
			$code = array( 'code' => 2, 'mess' => '验证码错误，请重新输入');
		}elseif($data['isUse'] == 1){
			$code = array( 'code' => 2, 'mess' => '验证码已使用，请重新获取');
		}elseif($data['endDate'] < time()){
			$code = array( 'code' => 2, 'mess' => '验证码已过期，请重新获取');
		}else{
			$code = array( 'code' => 1, 'mess' => $data['verify'],'id' => $data['id']);
		}
        return $code;
    }
    /**
     * 获取验证码id
     * @author  haydn
     * @since   2016-03-25
     * @param   string     	$account  	登录账户
     * @param   string     	$account  	验证码
     * @param   int        	$cateId   	账户标识(1邮件、2手机、3用户名)
     * @return  int			$id			验证码id
     */
    public function getCodeId( $account, $verify, $cateId = 2 )
    {
		$r['eq']	= array('object'=>$account ,'type' => $cateId,'verify' => $verify);
		$r['limit']	= 1;
		$r['order']	= array('id' => 'desc');
		$data		= $this->import('verify')->find($r);
		$id			= !empty($data['id']) ? $data['id'] : 0;
        return $id;
    }
    /**
     * 验证最新验证码的时效(60秒内防止重复发送)
     * @author  martin
     * @since   2016-01-18
     * @param   string     	$account  	登录账户
     * @param   string     	$account  	验证码
     * @param   int        	$cateId   	账户标识(1邮件、2手机、3用户名)
     * @return  bool		$isSend		返回（1：发送 0：不发送）
     */
    public function verifyCodeAging( $account, $cateId = 2 )
    {
		$r['eq']	= array('object'=>$account ,'type' => $cateId,'isUse' => 0);
		$r['raw']	= "endDate > " . time();
		$r['limit']	= 1;
		$r['order']	= array('id' => 'desc');
		$data		= $this->import('verify')->find($r);
		$isSend		= 1;
		if( !empty($data) ){
			$lastTime = $data['created'];
			(TIME - $lastTime < 60) && $isSend = 0;
		}
		return $isSend;
    }
    /**
     * 发送验证码
     * @author  martin
     * @since   2016-01-18
     * @param   string     $account  登录账户
     * @param   int        $cateId   账户标识(1邮件、2手机、3用户名)
     * @param   string     $smstemp  发送短信模板
     * @return  bool
     */
    public function sendCode($account,$cateId,$smstemp = 'newValid')
    {
    	$isSend 	= $this->verifyCodeAging( $account, $cateId);
    	$id			= 0;
    	if( $isSend == 1 ){
			$password 	= getRandChar(4,true);
			$id 		= $this->import('verify')->add($account,$password,$cateId);
			if( $id > 0 ){
				$this->load('register')->sendPassword($account,$password,$cateId,$smstemp);
			}
    	}
		return $id;
    }
    /**
     * 把验证码设置成使用
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$id  验证码id
     * @return  void
     */
    public function setCodeUse($id)
    {
		$r['eq']	= array('id' => $id);
		$data		= array('isUse' => 1);
		return $this->import('verify')->modify($data, $r);
    }
}
?>