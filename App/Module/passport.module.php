<?
/**
 * 用户账号登录、验证、修改
 *
 * 用户账号登录、验证、修改
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-11-05
 */
class PassportModule extends AppModule
{
    private $codeTime   = 600;//手机验证码有效时间(秒)
    private $mvName     = 'mvCode'; //手机验证码名称
    private $mbNo       = 'mbNo'; //验证时的手机号

    /**
     * 通过手机号进行注册并发送随机密码
     *
     * 
     * @author  martin
     * @since   2016/1/20
     *
     * @return  bool
     */
    public function RegTempUser($mobile,  $length=8)
    {
        if ( $length <= 0 || $length > 20 ){
            $length = 8;
        }
        $pass		= getRandChar($length, true);//生成8位随机密码
        if ( isCheck($mobile) != 2 ) return false;
        $msgTemp	= C('MSG_TEMPLATE');
        $content	= sprintf($msgTemp['valid'], $pass);
        $res		= $this->importBi('Message')->sendMsg($mobile, $content);
		if(isset($res['code']) && $res['code'] == 1){
			$res['verify'] = $pass;
		}
        return $res;
    }

    /**
     * 给邮箱发送验证码邮件【无效方法，暂留2016/3/8】
     *
     * @author  martin
     * @since   2016/1/20
     *
     * @return  bool
     */
    public function RegEmailUser($email,  $length=8)
    {
        if ( $length <= 0 || $length > 20 ){
            $length = 8;
        }
        $pass		= getRandChar($length, true);//生成8位随机密码
        if ( isCheck($email) != 1 ) return false;

		$url		= urlParamEncode("m=".$email."&time=".time());
		$url		= USER_CENTRE . "user/changeEmail/?m=".$url;
		$this->set('code' ,$pass);
		$this->set('url' ,$url);
		$content	= $this->fetch('user/email.template.html');
		$data['type'] = 1;
		/*
        $msgTemp	= C('MSG_TEMPLATE');
        $content	= sprintf($msgTemp['valid'], $pass);
		*/
        $res		= $this->importBi('Message')->sendMail($email, '更换邮箱验证码', $content,$name, '知友');
		
		
		if(isset($res['code']) && $res['code'] == 1){
			$res['verify'] = $pass;
		}
        return $res;
    }

    /**
     * 给邮箱发送验证码邮件
     *
     * @author  martin
     * @since   2016/1/20
     *
     * @return  bool
     */
	public function sendEmail($email, $title, $content, $name = '', $from='超凡网')	
	{
		return $this->importBi('Message')->sendMail($email, $title, $content,$name, $from);
	}

	/**
	 * 修改登陆手机
	 *
	 * @author	void
	 * @since	2014-11-17
	 *
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$mobile		新手机号
	 *
	 * @return	array
	 */
	public function changeMobile($userId, $mobile)
	{
		return $this->importBi('passport')->changeMobile($userId, $mobile);
	}

	/**
	 * 修改登陆账户
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$account	新登录账户
	 * @param	int		$cateId		账户标识(1邮件、2手机)
	 * @return	int     -1账户只能为邮件或手机、0失败或异常、1成功
	 */
	public function changeEmail($userId, $mobile)
	{
		return $this->importBi('passport')->changeEmail($userId, $mobile);
	}
	
	/**
	 * 修改密码
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$password	登陆密码
	 * @param	string	$newPwd		新密码
	 * @return	int     (-1账户不存在或密码错误、0失败或异常、1成功)
	 */
	public function changePwd($userId, $password, $newPwd)
	{
		return $this->importBi('passport')->changePwd($userId, $password, $newPwd);
	}


	/**
	 * 修改用户昵称
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$nickname	用户昵称
	 * @return	int     -1账户只能为邮件或手机、0失败或异常、1成功
	 */
	public function changeNickname($userId, $nickname)
	{
		return $this->importBi('passport')->changeNickname($userId, $nickname);
	}
}
?>