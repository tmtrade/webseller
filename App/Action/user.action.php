<?
/**
* 项目首页
*
* 首页内容展示
*
* @package	Action
* @author	void
* @since	2015-11-20
*/
class UserAction extends AppAction
{
	/**
	* 引用业务模型
	*/
	public $models = array(
		'verify'	=> 'verify',
	);

	/**
	* 控制器默认方法
	* @author	martin
	* @since	2016/1/18
	*
	* @access	public
	* @return	void
	*/
	public function index()
	{
		$userId					= $this->userInfo['id'];
		$message				= $this->load('message')->getPageList($userId,1,5);//我的消息
		$want					= $this->load('buyer')->getWantTm($userId);//求购数量

		/****************我的商标****************/
		$mytrade['proposer']	= $this->load('userproposer')->getProposerCount($userId);//我的申请人
		$mytrade['total']		= $this->load('mytrade')->getMytradeCount( array('userId'=>$userId) );//我的商标
		if($mytrade['total'] != 0){
			$mytrade['status1']	= $this->load('mytrade')->getMytradeCount( array('userId'=>$userId,'status1'=>1) );//申请中
			$mytrade['status2']	= $this->load('mytrade')->getMytradeCount( array('userId'=>$userId,'status2'=>1) );//已注册
			$mytrade['status3']	= $this->load('mytrade')->getMytradeCount( array('userId'=>$userId,'status3'=>1) );//已无效
			$mytrade['new']		= $this->load('newtrade')->infoTotal($userId);
		}

		/****************竞手商标****************/
		$collect['total']		= $this->load('collect')->getTradeCount(array('userId'=>$userId,'source'=>2));//竞手商标
		if($collect['total'] != 0){
			$collect['status1']	= $this->load('collect')->getTradeCount(array('userId'=>$userId,'source'=>2,'status1'=>1));//申请中
			$collect['status2']	= $this->load('collect')->getTradeCount(array('userId'=>$userId,'source'=>2,'status2'=>1));//已注册
			$collect['status3']	= $this->load('collect')->getTradeCount(array('userId'=>$userId,'source'=>2,'status3'=>1));//已无效
		}
		/****************交易收藏****************/
		$sale['total']			= $this->load('collect')->getTradeCountagain($userId);
		if($sale['total'] != 0){
			$saleList			= $this->load('collect')->getTradeSale($userId);//我收藏的出售商标
			if(count($saleList) > 0){
				$sale['online']		= $this->load('sale')->getSaleTotal($saleList);
				$sale['other']		= $sale['total'] - $sale['online'];
				$sale['price1']		= $this->load('sale')->getSaleTotal($saleList,"%s=0");
				$sale['price2']		= $this->load('sale')->getSaleTotal($saleList,"%s >0 and %s < 50000");
				$sale['price3']		= $this->load('sale')->getSaleTotal($saleList,"%s >=50000 and %s < 100000");
				$sale['price4']		= $this->load('sale')->getSaleTotal($saleList,"%s > 100000");
			}
		}

		/****************出售商标****************/
		$contact['total']		= $this->load('salecontact')->getSaleContact($userId);
		$contact['history']		= $this->load('salehistory')->getHistoryCount($userId);
		

		/*商品续展*/
		//$raw					= 'validEndDate between ' . time() . ' and ' . strtotime('+1 year');
		$raw					= 'status10=1';
		$deal['status10']		= $this->load('mytrade')->getMytradeCount(array('userId'=>$userId),$raw);
		//$raw					= 'validEndDate between '.strtotime('-6 months').' and '.time();
		$raw					= 'status11=1';
		$deal['status11']		= $this->load('mytrade')->getMytradeCount(array('userId'=>$userId),$raw);

		/****************商品案件****************/
		$raw					= '(status4 = 1 or status8 = 1 or status9 = 1 or status10 = 1 or  status14 = 1 or status28 = 1 or status15 = 1)';
		$deal['total']			= $this->load('mytrade')->getMytradeCount(array('userId'=>$userId),$raw);
		$deal['finish']			= $this->load('mytrade')->getDealCount($userId);
		$deal['deal']			= $deal['total'] - $deal['finish'];

		$this->set('mytrade', $mytrade);
		$this->set('collect', $collect);
		$this->set('sale', $sale);
		$this->set('contact', $contact);
		$this->set('want', $want);
		$this->set('deal', $deal);
		$this->display();
	}

	/**
	* 个人首页
	* @author	martin
	* @since	2016/1/19
	*
	* @access	public
	* @return	void
	*/
	public function main()
	{
		$userInfoId = $this->userInfo['id'];
		if($this->isPost()){
			$param	= $this->getFormData();
			$bool	= $this->load('user')->saveInfo($userInfoId, $param);
			echo $bool['code'] == 1 ? 1 : 0;
			exit;
		}
		$data		= $this->load("user")->getInfoById( $userInfoId );
		$weixin		= $this->load("weixin")->getBinDing($userInfoId);
		$data		= $this->load("user")->getInfoById( $userInfoId );
		$weixin['error'] == 1 && $this->load("weixin")->setUserWxId( $userInfoId, $weixin['info']['id']);//设置微信id
		$this->set('data', $data);
		$this->set('weixin', $weixin);
		$this->set('isbinding', $weixin['error'] == 0 ? 0 : 1);
		$this->set('data', $data);
		$this->display();
	}
	/**
	* 验证是否绑定微信
	* @author	haydn
	* @since	2016/2/25
	*
	* @access	public
	* @return	void
	*/
	public function isBangDing()
	{
		$userInfoId = $this->userInfo['id'];
		$weixin		= $this->load("weixin")->getBinDing($userInfoId);
		$array['wxid'] = $weixin['error'] == 0 ? 0 : 1;
		$this->returnAjax($array);
	}
	/**
	* 解除绑定微信页面
	* @author	haydn
	* @since	2016/2/25
	*
	* @access	public
	* @return	void
	*/
	public function changeWeiXin()
	{
		//$isbin	= $this->verifyIsBinding();
		//$isbin	== false && $this->redirect('未绑定微信', '/user/security/');
		$this->display();
	}
	/**
	* 发送微信验证码
	* @author	haydn
	* @since	2016/2/25
	*
	* @access	public
	* @return	json
	*/
	public function sendWeiXinCode()
	{
		$isbin		= $this->verifyIsBinding();
		if( $isbin == true ){
			$uId 	= $this->userInfo['id'];
			$data	= $this->load("user")->getInfoById( $uId );
			$account= !empty($data['mobile']) ? $data['mobile'] : $data['email'];
			$id		= $this->load("weixin")->sendWeiXinCode($account);//发送验证码
			$code	= !empty($id) ? 1 : 4;
		}else{
			$code	= 2;
		}
		$msg	= $this->load("weixin")->getWXCodeMsg($code);
		$result	= array('code' => $code,'msg' => $msg);
		$this->returnAjax($result);//返回结果
	}
	/**
	* 验证是否绑定微信
	* @author	haydn
	* @since	2016/2/25
	*
	* @access	public
	* @return	bool
	*/
	public function verifyIsBinding()
	{
		$userInfoId = $this->userInfo['id'];
		$data		= $this->load("user")->getInfoById( $userInfoId );
		if( $data['wenxinId'] == 0 ){
			$isbin 	= false;
		}else{
			$isbin	= true;
		}
		return $isbin;
	}
	/**
	* 验证微信验证码
	* @author	haydn
	* @since	2016/2/25
	*
	* @access	public
	* @return	json
	*/
	public function checkWeiXinCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$userInfoId = $this->userInfo['id'];
		$data		= $this->load("user")->getInfoById( $userInfoId );
		$wxcode		= $this->input('wxcode', 'string');
		$account	= !empty($data['mobile']) ? $data['mobile'] : $data['email'];
		$cateId		= isCheck($account);
		$result		= $this->load('verify')->getVerify($account, $wxcode, $cateId);
		$this->returnAjax($result);
	}
	/**
	* 解绑微信
	* @author	haydn
	* @since	2016/2/25
	*
	* @access	public
	* @return	json
	*/
	public function unbundlingWeiXin()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$userInfoId = $this->userInfo['id'];
		$wxcode		= $this->input('wxcode', 'string');
		$codeid		= $this->input('codeid', 'string');//验证码表id
		//$this->load("verify")->setCodeUse($codeid);//验证码使用过1.0.1版本无用
		$code 		= $this->load("weixin")->unBindweixin($userInfoId);
		$code == 1 && $this->load("weixin")->setUserWxId($userInfoId,0);
		$msg		= $code == 1 ? '解绑成功' : '解绑失败';
		$result		= array('code' => $code,'msg' => $msg);
		$this->returnAjax($result);
	}

	/**
	* 修改密码
	* @author	martin
	* @since	2016/1/19
	*
	* @access	public
	* @return	void
	*/
	public function changePwd()
	{ 
		$this->display();
	}

	/**
	* 绑定邮箱
	* @author	martin
	* @since	2016/1/19
	*
	* @access	public
	* @return	void
	*/
	public function changeEmail()
	{
		$emailtext = $this->input('m', 'string');
		if(!empty($emailtext))
		{
			$url		= urlParamDecode($emailtext);
			$backArr	= $this->convertUrlQuery($url);
			$emailtext	= $backArr['m'];
			$this->set('emailtext',$emailtext);
		}
		$this->display();
	}

	/**
	* 分解url
	* @author	martin
	* @since	2016/3/8
	*
	* @access	public
	* @return	void
	*/
	public function convertUrlQuery($query)
	{
		$queryParts = explode('&', $query);
		$params = array();
		foreach ($queryParts as $param)
		{
			$item = explode('=', $param);
			$params[$item[0]] = $item[1];
		}
		return $params;
	}

	/**
	* 绑定手机
	* @author	martin
	* @since	2016/1/19
	*
	* @access	public
	* @return	void
	*/
	public function changePhone()
	{
		$data	= $this->load("user")->getInfoById( $this->userInfo['id'] );
		$this->set('data', $data);
		$this->display();
	}

	/**
	* 发送手机验证码
	* 
	* @author  martin
	* @since   2016/1/20
	*
	* @access  public
	*
	* @return  json
	*/
	public function sendRegCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');

		$mobile = $this->input('m', 'string', '');
		if ( empty($mobile) || isCheck($mobile) != 2 ){
			$this->returnAjax(array('code'=>2));//手机号不正确
		}

		$userinfo = $this->load('register')->exist($mobile);
		if ( $userinfo ) $this->returnAjax(array('code'=>3));//该手机号已存在

		//设置验证码
		$res = $this->load('passport')->RegTempUser($mobile,4);
		if (isset($res['code']) && $res['code'] == 1){
			$this->import('verify')->add($mobile, $res['verify']);
			$flag = array('code'=>1);//正确
		}elseif (isset($res['code']) && $res['code'] == 2) {
			$flag = array('code'=>2);//发送失败
		}else{
			$flag = array('code'=>0);//发送失败
		}
		$this->returnAjax($flag);
	}

	/**
	* 更换手机，判断验证码
	* 
	* @author  martin
	* @since   2016/1/20
	*
	* @access  public
	*
	* @return  json
	*/
	public function checkCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$mobile			= $this->input('m', 'string', '');
		$verify			= $this->input('v', 'string', '');
		$userInfoId		= $this->userInfo['id'];
		$userinfo		= $this->load('user')->getInfoById($userInfoId);
		if($userinfo['mobile'] != $mobile){
			$userinfo	= $this->load('register')->exist($mobile);
			if ( $userinfo ) $this->returnAjax(array('code'=>3,'mess'=>'该号码已经存在，请重新输入'));
		}
		$ver			= $this->load('verify')->getVerify($mobile, $verify, 2);
		if($ver['code'] == 2){
			$this->returnAjax(array('code'=>2, 'mess'=>$ver['mess']));//验证失败
		}else{
			$userinfo	= array('mobile' => $mobile,'isMobile' => 1);
			$isupdate	= $this->load('user')->saveInfo($this->userInfo['id'], $userinfo);
			if($isupdate['code'] == 1){
				$this->import('verify')->updateVerify($ver['id']);
			}
			$this->returnAjax($isupdate);//返回修改结果

		}
	}

	/**
	* 发送邮箱发送验证码
	* 
	* @author  martin
	* @since   2016/1/21
	*
	* @access  public
	*
	* @return  json
	*/
	public function sendEmailCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');

		$email		= $this->input('m', 'string');
		if ( empty($email) || isCheck($email) != 1 ){
			$this->returnAjax(array('code'=>2));//邮箱地址不正确
		}
		$userinfo	= $this->load('register')->exist($email);
		if ( $userinfo ) $this->returnAjax(array('code'=>3));//邮箱地址已存在

		$pass		= getRandChar(4, true);//生成8位随机密码

		$url		= urlParamEncode("m=".$email."&time=".time());
		$url		= USER_CENTRE . "user/changeEmail/?m=".$url;
		$this->set('code' ,$pass);
		$this->set('url' ,$url);
		$content	= $this->fetch('user/email.template.html');
		$res		= $this->load('passport')->sendEmail($email, '更换邮箱验证码', $content,'', '知友');

		//$res		= $this->load('passport')->RegEmailUser($email,4);//设置验证码
		if (isset($res['code']) && $res['code'] == 1){
			$this->import('verify')->add($email, $pass, 1);
			$flag	= array('code'=>1);//正确
		}elseif (isset($res['code']) && $res['code'] == 2) {
			$flag	= array('code'=>2);//发送失败
		}else{
			$flag	= array('code'=>0);//发送失败
		}
		$this->returnAjax($flag);
	}

	/**
	* 更换手机，判断验证码
	* 
	* @author  martin
	* @since   2016/1/20
	*
	* @access  public
	*
	* @return  json
	*/
	public function checkEmailCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$email			= $this->input('m', 'string');
		$verify			= $this->input('v', 'string');
		$userInfoId		= $this->userInfo['id'];
		$userinfo		= $this->load('user')->getInfoById($userInfoId);

		if($userinfo['email'] != $email){
			$userinfo	= $this->load('register')->exist($email,1);
			if ( $userinfo ) $this->returnAjax(array('code'=>3,'mess'=>'该邮箱已经存在，请重新输入'));
		}
		$ver			= $this->load('verify')->getVerify($email, $verify, 1);
		if($ver['code'] == 2){
			$this->returnAjax(array('code'=>2, 'mess'=>$ver['mess']));//验证失败
		}else{
			$saveinfo	= array('email' => $email,'isEmail' => 1);
			$isupdate	= $this->load('user')->saveInfo($userInfoId, $saveinfo);
			if($isupdate['code'] == 1){
				$this->import('verify')->updateVerify($ver['id']);	
			}
			$this->returnAjax($isupdate);//返回修改结果
		}
	}

	/**
	* 检查密码是否正确
	* 
	* @author  martin
	* @since   2016/2/16
	*
	* @access  public
	*
	* @return  json
	*/
	public function checkPwd()
	{
		//if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$old		= $this->input('old', 'string');
		$userInfoId	= $this->userInfo['id'];
		$saveinfo	= array('password' => $old);
		$isupdate	= $this->load('user')->checkInfoPwd($userInfoId, $saveinfo);
		$this->returnAjax( $isupdate );//返回修改信息
	}

	/**
	* 修改密码
	* 
	* @author  martin
	* @since   2016/1/20
	*
	* @access  public
	*
	* @return  json
	*/
	public function updatePwd()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$old		= $this->input('old', 'string');
		$new		= $this->input('new', 'string');
		$userInfoId	= $this->userInfo['id'];
		$saveinfo	= array('password' => $new, 'oldpassword'=>$old);
		$isupdate	= $this->load('user')->saveInfoPwd($userInfoId, $saveinfo);
		$this->returnAjax( $isupdate );//返回修改信息
	}
	/**
	* 评价顾问
	* @since    2016-03-10
	* @author   haydn
	* @return   void
	*/
	public function myStaffSubmit()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$staff  	= array();
		$userId 	= $this->userInfo['id'];
		$content	= $this->input('content', 'string');
		$istrade	= $this->input('istrade', 'int');
		$sid		= $this->input('sid', 'int');
		$account	= $this->userInfo['specname'];
		$id			= $this->load('staffjudge')->judgeAdd($userId,$content,$istrade);
		if( $id > 0 ){
			$this->load('staffjudge')->sendCrmMail($sid,$account,$content,$istrade);
		}
		$msg		= $id > 0 ? '评价成功' : '评价失败';
		$result 	= array('code' => $id,'msg' => $msg);
		$this->returnAjax($result);
	}
	/**
	* 重置密码页面
	* @since	2016-03-17
	* @author	haydn
	* @return 	void 
	*/
	public function resetpwd()
	{
		$this->display();
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
		$m	= $this->input('m', 'string');
		if( !in_array($m,array(1,2)) ){
			$code 		= -1;
		}else{
			$code		= 0;//发送失败
			$account	= $m == 1 ? $this->userInfo['mobile'] : $this->userInfo['email'];
			if( $m == 1 ){
				$res		= $this->load('passport')->RegTempUser($account,4);
				$pass		= $res['verify'];
			}else{
				$pass		= getRandChar(4, true);//生成8位随机密码
				$url		= urlParamEncode("m=".$account."&time=".time());
				$url		= USER_CENTRE . "user/changeEmail/?m=".$url;
				$this->set('code' ,$pass);
				$this->set('url' ,$url);
				$content	= $this->fetch('user/email.template.html');
				$res		= $this->load('passport')->sendEmail($account, '邮箱验证码', $content,'', '知友');
			}
			if (isset($res['code']) && $res['code'] == 1){
				$this->import('verify')->add($account, $pass, $m);
				$code		= 1;
			}
		}
		$flag = array('code' => $code);
		$this->returnAjax($flag);
		
	}
	/**
	* 验证重置密码的验证码
	* @since	2016-03-17
	* @author	haydn
	* @return 	void 
	*/
	public function checkResetCode()
	{
		if ( !$this->isAjax() ) $this->redirect('', '/user/main/');
		$m	= $this->input('m', 'string');
		$v	= $this->input('v', 'string');
		if( !in_array($m,array(1,2)) ){
			$code	= -1;
			$msg	= '账号错误';
		}else{
			$account= $m == 1 ? $this->userInfo['mobile'] : $this->userInfo['email'];
			$ver	= $this->load('verify')->getVerify($account, $v, $m);
			$code	= $ver['code'];
			$msg	= $ver['mess'];
		}
		$flag = array('code' => $code,'msg' => $msg);
		$this->returnAjax($flag);
	}
	/**
	* 验证通过修改密码
	* @since	2016-03-17
	* @author	haydn
	* @return 	void 
	*/
	public function upResetpwd()
	{
		$om		= $this->input('om', 'string');//密码1
		$nm		= $this->input('nm', 'string');//密码2
		$code 	= 0;
		$msg 	= '两次密码不一样';
		if( $om == $nm ){
			$code	= $this->load('user')->resetpwd($this->userInfo['id'],$nm);
			$msg	= '修改成功';
		}
		$flag = array('code' => $code,'msg' => $msg);
		$this->returnAjax($flag);
	}
	/**
	* 我的顾问
	* @since    2016-01-21
	* @author   haydn
	* @return   void
	*/
	public function myStaffMore()
	{
		$this->myStaff();
		$this->display();
	}
}

?>