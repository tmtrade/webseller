<?
/**
* 用户注册 
* @package	Module
* @author	haydn
* @since	2016-01-18
*/
class RegisterModule extends AppModule
{
    /**
    * 消息模版
    */
    public $models = array(
        'user'      => 'user',
        'verify'    => 'verify',
        'relation'	=> 'relation'
    );
    /**
    * 验证用户是否存在（验证超凡、个人中心）
    * @since    2016-01-20
    * @access   public
    * @param    string      $account    登录账户
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   int         返回$code(0：不存在、1：存在)
    */
    public function exist($account,$cateId = 2)
    {
        $code       = 1;
        $cfexist    = $this->importBi('passport')->exist($account,$cateId);
        if( $cfexist['code'] == 0 ){//不存在
            $code = $this->import('user')->exist($account,$cateId);
        }
        return $code;
    }
    /**
    * 添加用户
    * @since    2016-01-20
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   int         $id         返回注册id
    */
    public function addUser($account,$password,$cateId = 2){
        $id = $this->import('user')->add($account,$password,$cateId);//注册用户
        return $id;
    }    
    /**
    * 用户注册
    * @author   hyand
    * @since    2016-01-20
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @param    string      $smstemp    注册发送短信的模板
    * @return   int         $id
    */
    public function regDeal($account,$password,$cateId = 2,$smstemp = '')
    {
        $id     = 0;
        $id 	= $this->addUser($account,$password,$cateId);
        if( $id > 0 ){
            $issend = $this->sendPassword($account,$password,$cateId,$smstemp);
            $this->addRelation($id,$account,$cateId);
        }
		$this->load('message')->pushMessage(array('tplId' => 1,'message'=>''), $id);
        return $id;
    }
    /**
    * 发送密码
    * @since    2016-01-20
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @param    string      $config     发送短信模板
    * @param    string      $title      发送邮件的标题
    * @return   int         $code       返回code(1：发送成功 0：发送失败)
    */
    public function sendPassword($account,$password,$cateId = 2,$smstemp = '',$title = '')
    { 
		if( $cateId == 1 ){
			$smstemp	= !empty($smstemp) ? $smstemp : 'newpassword';
			$content    = file_get_contents(WebDir.'/App/View/login/'.$smstemp.'.html');
			$content	= str_replace(array('{$email}','{$password}'),array("$account","$password"),$content);
			$title		= !empty($title) ? $title : '您的账号密码';
			$array      = $this->importBi('message')->sendMail($account,$title,$content);
		}elseif( $cateId == 2 ){
			$msgArr     = C('MSG_TEMPLATE');
			$msgIdArr	= C('MSG_TEMPLATEID');
			$smstemp	= !empty($smstemp) ? $smstemp : 'register';
			$smsId		= $msgIdArr[$smstemp];
			$msg		= array_key_exists($smstemp,$msgArr) ? $msgArr[$smstemp] : $config;
			//$content    = sprintf($msg,$password);
			//$array      = $this->importBi('message')->sendMsg($account,$password,$smsId);
			$array      = $this->importBi('crm')->sendSmsMsg($account,$password,$smsId);
		}
		$code = isset($array['code']) && $array['code'] == 1 ? 1 : 0;
		return $code;
    }
    /**
    * 更新账号密码
    * @author  haydn
    * @since   2015-12-16
    * @param   string   $account      登录账户
    * @param   string   $password     登录密码
    * @param   int      $cateId       账户标识(1邮件、2手机、3用户名)
    * @return  int      $code         返回（1：修改成功 0：失败）
    */
    public function updatePassword($account,$password,$cateId = 2)
    {
        $user       = $this->import('user')->getUserInfo($account,$cateId);
        $pword      = getPasswordMd5($password,$user['salt']);
        $issend     = $this->sendPassword($account,$password,$cateId);
        if( $issend == 1 ){
        	$cfArray= $this->importBi('passport')->get($account,$cateId);
    		if( !empty($cfArray) && $cfArray['code'] == 1 ){//修改超凡密码
    			$cfId = !empty($cfArray['data']['id']) ? $cfArray['data']['id'] : 0;
				$this->importBi('passport')->resetPwd($cfId,$password);
    		}
            $code   = $this->import('user')->updatePassword($account,$pword,$cateId);
        }
        $code       = $code == 1 ? 1 : 0;
        return $code;
    }
    /**
    * 接口用户注册
    * @author  haydn
    * @since   2015-12-16
    * @param   string   $account      登录账户
    * @param   string   $password     登录密码
    * @param   int      $cateId       账户标识(1邮件、2手机、3用户名)
    * @return  int      $id           大于0成功，否则失败
    */
    public function remoteUserLogin($account,$password,$cateId = 2)
    {
    	$id		= 0;
		$cfInfo	= $this->importBi('passport')->get($account,$cateId);
		if(!empty($cfInfo['data']['id'])){//超凡存在
        	$isexist    = $this->import('user')->existChofnId($cfInfo['data']['id']);//检查账号是否在库里面存在
	        $isexist == 0 && $id = $this->import('user')->addChofn($cfInfo['data']);//用超凡账号创建账号
		}else{
			$usInfo     = $this->import('user')->getUserInfo($account,$cateId);//检查用户库有无用户
			empty($usInfo['id']) && $id = $this->regDeal($account,$password,$cateId);//注册
		}
		return $id;
    }
    /**
    * 注册数据写入分配系统
    * @author	haydn
    * @since	2016-02-27
    * @param 	string		$account	登录账户
    * @param 	int 		$cateId		账户标识(1邮件、2手机、3用户名)
    * @param 	int 		$type		业务类型（1：国内商标 2：国际商标 3：国内专利 4：国际专利 5：商标转让 6：版权信息 7：专利转让 8：法律信息 9：高新科技）
    * @return	int			$id			分配id
    */
    public function addRegNetwork($account,$cateId = 2,$type = 1)
    {
    	$atype 				= $cateId == 2 ? 'tel' : 'email';
    	$post['type'] 		= 1;//业务类型
    	$post['source'] 	= 0;//来源站点（0：后台查标[.COM] 2：在线咨询[.COM]  4：400电话咨询）
		$post['subject'] 	= '知友验证通过用户';//注册名称
		$post['sid'] 		= !empty($_GET['sid']) ? $_GET['sid'] : '';
		$post['area'] 		= !empty($_GET['area']) ? $_GET['area'] : '';
		$post[$atype] 		= $account;
		$data				= $this->load('network')->networkJoin($post);
		$id					= !empty($data['code']) ? $data['data']['id'] : 0;
		return $id;
    }
    /**
    * 信息写入分配系统
    * @author	haydn
    * @since	2016-02-27
    * @param 	array		$data		数据包
    * @param 	int 		$cateId		账户标识(1邮件、2手机、3用户名)
    * @return	int			$id			分配id
    */
    public function addNetwork($data,$cateId = 2)
    {
    	$post['type']		= !empty($data['type']) ? $data['type'] : 5;//业务类型
    	$post['source'] 	= !empty($data['source']) ? $data['source'] : 0;//来源站点（0：后台查标[.COM] 2：在线咨询[.COM]  4：400电话咨询）
		$post['company'] 	= !empty($data['company']) ? $data['company'] : '';//公司名称
		$post['pttype'] 	= !empty($data['pttype']) ? $data['pttype'] : '求购'; //类型（1：求购 2：出售）
		$post['remarks'] 	= $data['remarks'];//备注
		$post['subject'] 	= $data['subject'];//注册名称
		$post['name'] 		= $data['name'];//联系人
		$post['tel'] 		= $data['tel'];
		$post['ptype'] 		= !empty($data['ptype']) ? $data['ptype'] : '';//专利的类别
		$post['sid'] 		= !empty($_GET['sid']) ? $_GET['sid'] : '';
		$post['area'] 		= !empty($_GET['area']) ? $_GET['area'] : '';
		/*
		$submit				= $this->importBi('crm')->getNewSubmit($data['tel']);//获取相同手机最近提交的数据
		if( $submit['code'] == 1 ){
			$lastTime = $submit['data']['dateline'];
			if( time()-$lastTime <= 10 ){//10秒内禁止重复提交
				return -1;
			}
		}*/
		$data				= $this->load('network')->networkJoin($post);
		$id					= !empty($data['code']) ? $data['data']['id'] : 0;
		return $id;
    }
    /**
    * 用户关联分配id
    * @author	haydn
    * @since	2016-02-27
    * @param 	int 		$uid		用户id
    * @param 	string		$account	登录账户
    * @param 	int 		$cateId		账户标识(1邮件、2手机、3用户名)
    * @return	int			$id			
    */
    public function addRelation($uid,$account,$cateId = 2)
    {
    	$id		= 0;
    	$aid	= $this->addRegNetwork($account,$cateId);
    	if( $aid > 0 ){
			$count = $this->import('relation')->getRelationCount($uid);
			if( $count == 0 ){
				$id	= $this->import('relation')->add($uid,$aid);
			}else{
				$id	= $this->import('relation')->updateRelation($uid,$aid);
			}
    	}
		return $id;
    }
}
?>