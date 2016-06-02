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
class UserModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'user'		=> 'user',
		'changelogs'=> 'changelogs',
    );
	/**
	 * 获取业务对象(系统对接时使用)
	 * @author	void
	 * @since	2015-11-20
	 *
	 * @access	public
	 * @param	string	$name	业务代理类名
	 * @return	object  返回业务对象
	 */
	public function index()
	{


	}

	/**
	 * 获取业务对象(系统对接时使用)
	 * @author	void
	 * @since	2015-11-20
	 *
	 * @access	public
	 * @param	string	$name	业务代理类名
	 * @return	object  返回业务对象
	 */
	public function getNameById($id = 0)
	{
		if($id == 0){
			$id = Session::get("UserId");
		}
		$data   = $this->getInfoById($id);
		if(empty($data)){
			return "";
		}else{
			return $data['firstname'];
		}
	}

	/**
	 * 获取业务对象(系统对接时使用)
	 * @author	void
	 * @since	2015-11-20
	 *
	 * @access	public
	 * @param	string	$name	业务代理类名
	 * @return	object  返回业务对象
	 */
	public function getInfoById($id)
	{
		$data					= $this->import("user")->get($id);
		$data['lasttime']		= $this->load('sessions')->getloginDate($data['id'],$data['lastDate']);
		if(!empty($data['mobile'])){//隐藏电话号码
			$data['mobile_hide']= substr_replace($data['mobile'],"****",3,4);
		}
		if(!empty($data['email'])){//隐藏电话号码
			$first				= strpos($data['email'],'@');
			$length				= ($first - 1) > 4 ? 4 : ($first - 1);
			$strat				= ($first - 4) > 1 ? ($first - 4) : 1 ;
			$data['email_hide']	= substr_replace($data['email'],"****", $strat, $length);
		}

		$data['firstname']		= empty($data['nickname']) ? ( empty($data['name']) ? ( empty($data['mobile_hide']) ? $data['email_hide'] : $data['mobile_hide'] ) : $data['name'] ): $data['nickname'];

		$data['specname']		= empty($data['nickname']) ? ( empty($data['name']) ? ( empty($data['mobile']) ? $data['email'] : $data['mobile'] ) : $data['name'] ): $data['nickname'];//不隐藏的用户名
		return $data;
	}

	/**
	 * 保存用户信息
	 * 超凡网用户调用接口修改超凡网信息
	 * 保存成功记录修改日志
	 * @author	martin
	 * @since	2016/1/20
	 *
	 * @access	public
	 * @param	string	$user	用户ID
	 * @param	string	$data	用户信息
	 * @return	object  返回业务对象
	 */
	public function saveInfo($user, $data)
	{
		if(empty($user) || empty($data)){
			return false;
		}
		$bool			= true;
		$userInfo		= $this->getInfoById($user);
		if(!empty($userInfo['cfwId'])){//调用超凡网接口，修改用户信息。
			$return = array('code' => 1);
			if( isset($data['mobile']) && !empty($data['mobile']) ){
				$return	= $this->load('passport')->changeMobile($userInfo['cfwId'], $data['mobile']);
			}
			if( isset($data['email']) && !empty($data['email']) ){
				$return	= $this->load('passport')->changeEmail($userInfo['cfwId'], $data['email']);
			}
			if( isset($data['nickname']) ){
				$return	= $this->load('passport')->changeNickname($userInfo['cfwId'], $data['nickname']);
			}

			if(isset($return['code']) && $return['code'] != 1){//修改失败，返回错误信息
				return array('code' => $return['code'],'mess'=> $return['msg']);
			}
		}
		//修改本地信息，记录修改日志
		$r['eq']				= array('id'=>$user);
		$bool					= $this->import("user")->modify($data, $r);
		if($bool){
			$this->load('changelogs')->updateUserInfo($user, $data, $userInfo);
			return array('code' => 1);
		}
		return array('code' => 2,'mess'=> '修改失败');
	}

	/**
	 * 保存用户信息
	 * 超凡网用户调用接口修改超凡网信息
	 * 保存成功记录修改日志
	 * @author	martin
	 * @since	2016/1/20
	 *
	 * @access	public
	 * @param	string	$user	用户ID
	 * @param	string	$data	用户信息
	 * @return	object  返回业务对象
	 */
	public function saveInfoPwd($user, $data)
	{
		if(empty($user) || empty($data)){
			return false;
		}
		$bool			= true;
		$userInfo		= $this->getInfoById($user);
		if(!empty($userInfo['cfwId'])){//调用超凡网接口，修改用户密码。
			$return = array('code' => 1);
			if( isset($data['password']) && !empty($data['password']) ){
				$return	= $this->load('passport')->changePwd($userInfo['cfwId'], $data['oldpassword'], $data['password']);
			}
			if(isset($return['code']) && $return['code'] != 1){//修改失败，返回错误信息
				return array('code' => $return['code'],'mess'=> $return['msg']);
			}
		}else{//调用超凡网接口，修改用户密码
			$pword		= getPasswordMd5($data['oldpassword'],$userInfo['salt']);
			if($userInfo['password'] != $pword){
				return array('code' => 2,'mess'=> '旧密码错误');
			}
		}
		unset($data['oldpassword']);
		$data['password']	= getPasswordMd5($data['password'], $userInfo['salt']);

		$r['eq']				= array('id'=>$user);
		$bool					= $this->import("user")->modify($data, $r);
		if($bool){
			return array('code' => 1);
		}
		return array('code' => 2,'mess'=> '修改失败');
	}
	/**
	 * 检查用户密码是否正确
	 * @author	martin
	 * @since	2016/2/16
	 *
	 * @access	public
	 * @param	string	$user	用户ID
	 * @param	string	$data	用户信息
	 * @return	object  返回业务对象
	 */
	public function checkInfoPwd($user, $data)
	{
		if(empty($user) || empty($data)){
			return false;
		}

		$userInfo		= $this->getInfoById($user);
		$account		= empty($userInfo['cfwId']) ? $userInfo['id'] : $userInfo['cfwId'];
		$code			= $this->load('login')->userPwdVerify($account,$data['password'],4);
		echo $code; exit;
	}
	/**
	 * 用当前登录id获取对应的超凡id
	 * @author	haydn
	 * @since	2016/3/2
	 *
	 * @access	public
	 * @param	int		$id		用户ID
	 * @return	int  	$cfwId	返回超凡id
	 */
	public function getChofanId($id)
	{
		$r['eq'] 	= array('id' => $id);
		$r['col']   = array('`cfwId`');
		$data 		= $this->import("user")->find($r);
		$cfwId		= !empty($data) ? $data['cfwId'] : 0;
		return $cfwId;
	}
	/**
	 * 获取超凡账号
	 * @author	haydn
	 * @since	2016/3/2
	 *
	 * @access	public
	 * @param	string      $account    登录账户
     * @param   int         $cateId     账户标识(1邮件、2手机、3用户名)
	 * @return	array  		$cfwId		返回超凡账号信息
	 */
	public function getChofnAccount($account,$cateId = 2)
	{
		$cfInfo	= $this->importBi('passport')->get($account,$cateId);
		return $cfInfo;
	}
	/**
	 * 用超凡id获取用户中心账号
	 * @author	haydn
	 * @since	2016/3/17
	 *
	 * @access	public
	 * @param	int      	$cfid    	超凡id
	 * @param	string     	$field    	返回字段(类如：id,name)
	 * @return	array  		$cfwId		返回账号信息
	 */
	public function getUcChofnInfo($cfid,$field='*')
    {
    	$fieldArr	= explode(',',$field);
		$r['eq'] 	= array('cfwId' => $cfid);
		$r['col']   = $fieldArr;
		$data 		= $this->import("user")->find($r);
		return $data;
    }
    /**
	 * 重置密码
	 * @author	haydn
	 * @since	2016/3/17
	 *
	 * @access	public
	 * @param	int      	$uid    	账号id
	 * @param	string     	$password   新密码
	 * @return	void
	 */
    public function resetpwd($uid,$password)
    {
    	$usInfo	= $this->import("user")->get($uid);
    	if( !empty($usInfo['cfwId']) ){
			$this->importBi('passport')->resetPwd($usInfo['cfwId'],$password);
    	}
    	$password			= getPasswordMd5($password,$usInfo['salt']);
    	$data['password']	= $password;
    	$r['eq'] 			= array('id'=>$uid);
		$bool 				= $this->import("user")->modify($data, $r);
		return $bool;
    }
    /**
    * 用账号获取信息
    *
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   string     $account		登录账户
    * @param   int        $cateId		账户标识(1邮件、2手机、3用户名)
    * @return  array      $data			数据包
    */
    public function getUserInfo($account,$cateId = 2)
    {
       $data = $this->import('user')->getUserInfo($account,$cateId);
       return $data;
    }
    /**
	 * 重置超凡网密码
	 * @author	haydn
	 * @since	2016/4/19
	 *
	 * @access	public
	 * @param	int      	$cfid    	超凡id
	 * @param	string     	$password   新密码
	 * @return	int         -1账户不存在、0失败或异常、1成功
	 */
    public function resetChofnPwd($cfid,$password)
    {
    	$code	= $this->importBi('passport')->resetPwd($cfid,$password);
    	$code	= $code == 1 ? 1 : 0;
		return $code;
    }
}
?>