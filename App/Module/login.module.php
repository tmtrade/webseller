<?
/**
* 用户登录
* 
* @package	Module
* @author	haydn
* @since	2016-01-18
*/
class LoginModule extends AppModule
{
    /**
    * 消息模版
    */
    public $models = array(
        'user'      => 'user',
        'sessions'  => 'sessions',
        'loginlogs' => 'loginlogs',
        'sessions'  => 'sessions',
        'verify'    => 'verify'
    );
    /**
    * 用户登陆
    * 
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   int         $code       返回code(1:成功，2:账号不存在，3：密码错误)
    */
    function login($account,$password,$cateId = 2,$uTime = '')
    {
        $vinfo  = $this->userLogVerify($account,$password,$cateId);
        if( $vinfo['code'] == 1 ){//验证成功
            $this->setLogin($vinfo,$cateId,$uTime);//设置登录信息
        }
        $pword	= $vinfo['code'] == 1 ? '' : $password;//验证不失败密码保存
        $this->import('loginlogs')->addlog($account,$pword,$vinfo['userId'],$vinfo['code']);
        return $vinfo['code'];
    }
    /**
    * 登录验证用户
    * (如果是超凡账号就是写入到用户库)
    * (是超凡账号登录优先验证超凡密码和账号)
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   array       $array
    */
    public function userLogVerify($account,$password,$cateId = 2)
    {
        $code       = 2;//账号不存在
        $uid        = 0;
        $array      = array();
        $cfInfo     = $this->importBi('passport')->get($account,$cateId);
        if( !empty($cfInfo['data']['id']) ){//登录账号在超凡存在
        	//验证密码和账号是否相同
        	$code	= $this->verifyChofn($cfInfo,$account,$password,$cateId);
        	if( $code == 1 ){
        		$uid	= $this->useChofnGetUcId($cfInfo);
        		if( !empty($uid) ){
        			$this->import('user')->updateChofn($cfInfo['data'],$uid);//修改用户中心里面的超凡账号
        		}else{
					$uid= $this->import('user')->addChofn($cfInfo['data']);//用超凡账号创建账号
					$this->load('message')->pushMessage(array('tplId' => 1,'message'=>''), $uid);
        		}
        	}
        }else{
        	$usInfo		= $this->import('user')->getUserInfo($account,$cateId);
        	if( !empty($usInfo['cfwId']) ){//超凡账号在用户中心注册过(超凡账号和用户中心账号不一样)
				$cfInfo	= $this->importBi('passport')->get($usInfo['cfwId'],4);
				$code	= $this->verifyChofn($cfInfo,$account,$password,$cateId);
        	}elseif( !empty($usInfo['id']) ){
                $uid    = $usInfo['id'];
                $pword  = getPasswordMd5($password,$usInfo['salt']);
                $code   = $usInfo['password'] != $pword ? 3 : 1;
            }
        }
        $array  = $this->returnUser($account,$password,$uid,$code);
        return $array;
    }
    /**
    * 返回验证用户
    * 
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $userId     id
    * @param    int         $code       code标示(1:成功，2:账号不存在，3：密码错误)
    * @return   int         $code       返回注册id
    */
    public function returnUser($account,$password,$userId,$code){
        $array = array(
            'account'   => $account,
            'password'  => $password,
            'userId'    => $userId,
            'code'      => $code,
        );
        return $array;
    }
    /**
    * 验证码登录
    * @since	2016-01-26
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $yzm   		验证码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   int	       	$code		返回code(1:成功)
    */
    public function userCodeLog($account,$yzm,$cateId = 2,$uTime = '')
    {
    	$logincode	= 0;
		$vinfo		= $this->load('verify')->getVerify($account,$yzm,$cateId);
		$password 	= 'uc'.getRandChar(5,true);
		$this->load('register')->remoteUserLogin($account,$password,$cateId);//账号注册（存在不处理）
		$usInfo		= $this->import('user')->getUserInfo($account,$cateId);
		if( $usInfo['id'] > 0 ){//验证成功
			$this->setLogin($usInfo,$cateId,$uTime);//设置登录信息
            $this->load('verify')->setCodeUse($vinfo['id']);//验证码设置成使用
            $logincode = 1;
        }
        $pword = $vinfo['code'] == 1 ? '' : $yzm;//验证失败密码保存
        $this->import('loginlogs')->addlog($account,$yzm,$usInfo['id'],$logincode);
        return $vinfo['code'];
    }
	
    /**
    * 验证用户密码(只验证，不做其他操作)
    * 
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   array       $array
    */
    public function userPwdVerify($account,$password,$cateId = 2)
    {
        $code       = 2;//账号不存在
        $uid        = 0;
        $array      = array();
        $cfInfo     = $this->importBi('passport')->get($account,$cateId);
        $usInfo     = $this->import('user')->getUserInfo($account,$cateId);
        if(!empty($cfInfo['data']['id'])){//超凡存在
            $pword          = getPasswordMd5($password,$cfInfo['data']['salt']);
            $code			= $cfInfo['data']['password'] != $pword ? 3 : 1;//验证密码
        }else{
            if( !empty($usInfo['id']) ){
                $uid    = $usInfo['id'];
                $pword  = getPasswordMd5($password,$usInfo['salt']);
                $code   = $usInfo['password'] != $pword ? 3 : 1;
            }
        }
        return $code;
    }
    /**
    * 设置登录信息
    * @since	2016-03-15
    * @author	haydn
    * @param 	array	 $usInfo	用户信息
    * @param	int      $cateId    账户标识(1邮件、2手机、3用户名)
    * @param	int      $uTime     cookie过期时间
    * @return 	void
    */
    public function setLogin($usInfo,$cateId,$uTime = '')
    {
    	$usInfo['id'] 	= isset($usInfo['userId']) ? $usInfo['userId'] : $usInfo['id'];
		$cookid 		= getRandChar();
		$uKey			= C('PUBLIC_USER');
		$uTime			= $uTime >=0 ? $uTime : C('PUBLIC_TIME');
		$this->import('user')->updateLogin($usInfo['id']);//修改登录次数
		$this->import('sessions')->addSessions($usInfo['id'],$cookid,$cateId);//写入登录日志
		LoginAuth::set(array($uKey => $cookid),$uTime);
    }
    /**
    * 用登录token获取用户信息
    * @since	2016-03-16
    * @author	haydn
    * @return	array	$mbinfo		用户信息 
    */
    public function getTokenUser()
    {
		$uKey 	= C('PUBLIC_USER');
        $token 	= LoginAuth::get($uKey);
        $mbinfo	= array();
        if(!empty($token)) {
            $session = $this->import('sessions')->getByToken($token);
            if( isset($session['userId']) && $session['cookieId'] == $token ) {
                $userInfo   = $this->import('user')->get($session['userId']);
                $userinfo	= $this->load('user')->getInfoById( $session['userId'] );
                $mbinfo     = array(
                    'id'        => $session['userId'],
                    'mobile'    => $userInfo['mobile'],
                    'email'    	=> $userInfo['email'],
                    'cfwId'     => $userInfo['cfwId'],
                    'cateId'	=> $session['type'],
                    'specname'	=> $userinfo['specname']
                );
                
            }
        }
		return $mbinfo;
    }
    /**
    * 登录账号和超凡网账号验证
    * @param    array      	$cfInfo    	超凡信息
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return	int			$code		返回（1：正确 2：账号不存在 3：密码错误）
    */
    public function verifyChofn($cfInfo,$account,$password,$cateId)
    {
		$pword	= getPasswordMd5($password,$cfInfo['data']['salt']);		
		$field	= $cateId == 1 ? 'email' : 'mobile';
		$cfUser	= $cfInfo['data'][$field];
		if( $cfUser != $account ){//验证两边账号是否相同
			$code	= 2;
		}elseif( $cfInfo['data']['password'] != $pword ){//验证密码                
        	$code	= 3;
        }else{
			$code	= 1;
        }
        return $code;
    }
    /**
    * 用户超凡网的邮箱或手机在uc里面是否存在
    * @since	2016-03-30
    * @author	haydn
    * @param    array      	$chofnArray	超凡数据包 
    * @return	int			$uid		返回账号id
    */
    public function useChofnGetUcId($chofnArray)
    {
    	$uid 	= 0;
		$mobile = $chofnArray['data']['mobile'];
		$email 	= $chofnArray['data']['email'];
		$arrm	= $this->import('user')->getUserInfo($mobile,2);
		if( !empty($arrm) ){
			$uid = $arrm['id'];
		}else{
			$arre	= $this->import('user')->getUserInfo($email,1);
			$uid	= !empty($arre) ? $arre['id'] : 0;
		}
		return $uid;
    }
    /**
    * code对应值
    * @since	2016-03-23
    * @author	haydn
    * @param    int      	$code 
    * @return	strjing		$msgJs
    */
    public function getError($code){
		switch($code){
			case 0:
		  		$msgJs = '账号格式不正确';
			  break;
			case 1:
		  		$msgJs = '成功';
			  break;  
			case 2:
		  		$msgJs = '账号不存在';
			  break;
			case 3:
		  		$msgJs = '密码错误';
			  break;
			case 4:
		  		$msgJs = '验证码错误';
			  break;  
			default:
				$msgJs = '登录失败，请稍后登录';
		}
		return $msgJs;	
	}
}
?>