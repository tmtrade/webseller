<?
/**
* 项目忘记密码页面
*
*
* @package	Action
* @author	void
* @since	2016-01-20
*/
class LostPasswordAction extends AppAction
{

	/**
	* 控制器默认方法
	* @author	void
	* @since	2016-03-24
	*
	* @access	public
	* @return	void
	*/
	public function index()
	{
		$this->display();
	}
	/**
	* 验证图片验证码和用户
	* @since	2016-04-05
	* @author	haydn
	* @return 	void
	*/
	public function verifyImgCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$account 	= $this->input('account', 'string', '');
		$password 	= $this->input('password', 'string', '');
		$cateId  	= isCheck($account);
		if( !in_array($cateId,array(1,2)) ){
			$code	= -1;
			$msg	= '账号格式错误';
		}else{
			session_start();
			session_destroy();
			if( $password == $_SESSION["authnum_session"] ){
				$code	= 1;
				$msg	= '验证成功';
				LoginAuth::set(array('uc_secret' => $account,'uc_lostcode' => $password),6000);
			}else{
				$code	= 3;
				$msg	= '验证码输入错误';
			}
		}
		$array	= array('code' => $code,'msg' => $msg);
		$this->returnAjax($array);
	}
	/**
	* 发送重置密码验证码
	* @since	2016-03-17
	* @author	haydn
	* @return 	void
	*/
	public function sendResetCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$account = $this->input('account', 'string', '');
		$cateId  = isCheck($account);
		if( !in_array($cateId,array(1,2)) ){
			$code	= -1;
			$msg	= '账号格式错误';
		}else{
			$string = $cateId == 1 ? 'email.template' : 'valid';
			$id		= $this->load('verify')->sendCode($account,$cateId,$string);
			$code	= $id > 0 ? 1 : 0;
			$msg	= $id > 0 ? '验证码发送成功' : '验证码发送失败';
		}
		$array	= array('code' => $code,'msg' => $msg);
		$this->returnAjax($array);
	}
	/**
	* 忘记密码修改第二步
	* @author	haydn
	* @since	2016-03-25
	* @return	void
	*/
	public function steps2()
	{
		$array = $this->verifySteps();
		$cateId  	= isCheck($array['account']);
		$vid		= $this->load('verify')->getCodeId( $array['account'], $array['verify'], $cateId);
		$this->load('verify')->setCodeUse($vid);
		$enmobile	= encrypt($array['account']);//加密手机
		$this->set('enmobile', $enmobile);
		$this->display();
	}
	/**
	* 忘记密码修改第三步(修改密码)
	* @author	haydn
	* @since	2016-03-25
	* @return	void
	*/
	public function steps3()
	{
		$array = $this->verifySteps();
		$cateId  	= isCheck($array['account']);
		$vid		= $this->load('verify')->getCodeId( $array['account'], $array['verify'], $cateId);
		$this->load('verify')->setCodeUse($vid);
		$this->display();
	}
	/**
	* 忘记密码修改第4步
	* @author	haydn
	* @since	2016-03-25
	* @return	void
	*/
	public function steps4()
	{
		$array 		= $this->verifySteps();
		$url 		= 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].'/';
		$success 	= $this->input('success','string');
		if( $success == 1 ){
			LoginAuth::set(array('uc_secret' => '','uc_lostcode' => ''),0);
			header("Location: ".$url);
		}
		$this->set('mobile', $array['account']);
		$this->display();
	}
	/**
	* 验证通过修改密码
	* @since	2016-03-25
	* @author	haydn
	* @return 	void
	*/
	public function upResetpwd()
	{
		$array	= $this->verifySteps();
		$account= $array['account'];
		$cateId = isCheck($account);
		$om		= $this->input('m1', 'string');//密码1
		$nm		= $this->input('m2', 'string');//密码2
		$isexist= $this->load('register')->exist($account,$cateId);
		$msgArr = array('账号不存在修改失败','修改成功','两次密码不一样','其他错误');
		if( $isexist == 0 ){
			$code 	= 0;
		}elseif( $om != $nm ){
			$code	= 2;
		}else{
			$code 	= 1;
			$usInfo = $this->load('user')->getUserInfo($account,$cateId);
			if( !empty($usInfo['id']) ){
				$this->load('user')->resetpwd($usInfo['id'],$nm);
			}
			if( empty($usInfo['cfwId']) ){
				try{
					$cfInfo		= $this->load('user')->getChofnAccount($account,$cateId);
					if( !empty($cfInfo['data']['id'])){
						$this->load('user')->resetChofnPwd($cfInfo['data']['id'],$nm);
					}
				}catch(Exception $e){
	                $code 	= 3;
	            }
			}
		}
		$msg	= $msgArr[$code];
		$flag 	= array('code' => $code,'msg' => $msg);
		$this->returnAjax($flag);
	}
	/**
	* 忘记密码验证
	* @author	haydn
	* @since	2016-03-25
	* @return	void
	*/
	private function verifySteps()
	{
		$mobile 		= $this->input('mobile','string');
		$account 		= LoginAuth::get('uc_secret');
		$uc_lostcode 	= LoginAuth::get('uc_lostcode');
		$array			= $account == $mobile ? array('account' => $account,'verify' => $uc_lostcode) : array();//数据验证
		if( count($array) == 0 ){
			$this->redirect('', '/user/main/');
		}
		return $array;
	}
}
?>