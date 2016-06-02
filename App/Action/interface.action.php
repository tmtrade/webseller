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
class interfaceAction extends RpcServer
{
	public $models = array(
        'user'		=> 'user',
        'interface'	=> 'interface',
    );
	/**
	 * 添加申请人
	 * @author	haydn
	 * @since	2016/03/03
	 *
	 * @access	public
	 * @return	void
	 */
	public function addProposer($param)
	{
		$logId 		= $this->import('interface')->addLog($param,$_SERVER["HTTP_REFERER"],__FUNCTION__);
		$code		= 0;
		$msg		= '';
		//验证相关的值是否存在
		$verifyArr 	= array('account','name','address','type','pid');
		$msg		= verifyArrayKey($verifyArr,$param);
		if( empty($msg) ){
			$account	= $param['account'];
			$cateId		= isCheck($account);
			if( $cateId != 3 ){
				$usInfo		= $this->import('user')->getUserInfo($account,$cateId);
				//用户不存在，获取超凡网账号并创建账号
				if( empty($usInfo) ){
					$cfInfo = $this->load('user')->getChofnAccount($account,$cateId);
					$uid    = $this->import('user')->addChofn($cfInfo['data']);//用超凡账号创建账号
				}else{
					$uid	= $usInfo['id'];
				}
				$propArr	= $this->load('proposer')->getProposerList($param['name'],$param['address'],1,5,false);
				$pid		= $propArr['total'] > 0 ? $propArr['rows'][0]['id'] : 0;
				$array		= array('name' => $param['name'],'address' => $param['address'],'type' => $param['type'],'pid' => $pid,'spid' => $param['pid']);
				$code		= $this->load('userproposer')->addProposer($uid,$array,0);
				$msg		= $code == 1 ? '添加成功' : '无法重复添加申请人';
			}else{
				$msg		= '账号格式错误';
			}
		}
		$data['code'] 	= $code;
		$data['msg']	= $msg;
		$this->import('interface')->updateLog($logId,$data);
		return $data;
	}
	/**
	 * 验证账号是否存在
	 * @author	haydn
	 * @since	2016/03/30
	 *
	 * @access	public
	 * @return	void
	 */
	public function isExist($param)
	{
		$logId 		= $this->import('interface')->addLog($param,$_SERVER["HTTP_REFERER"],__FUNCTION__);
		$code		= 3;
		$msg		= '';
		$verifyArr 	= array('account');
		$msg		= verifyArrayKey($verifyArr,$param);
		if( empty($msg) ){
			$cateId = isCheck($param['account']);
			$ucInfo = $this->import('user')->getUserInfo($param['account'],$cateId);
			$code	= !empty($ucInfo) ? 2 : 1;
			$msg	= !empty($ucInfo) ? '用户存在' : '用户不存在';
		}
		$data['code'] 	= $code;
		$data['msg']	= $msg;
		$this->import('interface')->updateLog($logId,$data);
		return $data;
	}
	/**
	 * 验证账号超凡是否存在
	 * @author	haydn
	 * @since	2016/03/30
	 *
	 * @access	public
	 * @return	void
	 */
	public function isCfExist($param)
	{
		$logId 		= $this->import('interface')->addLog($param,$_SERVER["HTTP_REFERER"],__FUNCTION__);
		$code		= 3;
		$msg		= '';
		$verifyArr 	= array('chofnid');
		$msg		= verifyArrayKey($verifyArr,$param);
		if( empty($msg) ){
			$cfId	= intval($param['chofnid']);
			if( $cfId > 0 ){
				$count  = $this->import('user')->existChofnId($cfId);
				$code	= $count > 0 ? 1 : 2;
				$msg	= $count > 0 ? '超凡用户存在' : '超凡用户不存在';
			}else{
				$code		= 4;
				$msg		= '超凡id错误';
			}
		}
		$data['code'] 	= $code;
		$data['msg']	= $msg;
		$this->import('interface')->updateLog($logId,$data);
		return $data;
	}
	/**
	* 更新密码
	* @author	haydn
	* @since	2016/03/30
	* @access	public
	* @return	void
	*/
	public function resetPwd($param)
	{
		
		$logId 		= $this->import('interface')->addLog($param,$_SERVER["HTTP_REFERER"],__FUNCTION__);
		$code		= 3;
		$msg		= '';
		$verifyArr 	= array('chofnid','password','salt');
		$msg		= verifyArrayKey($verifyArr,$param);
		if( empty($msg) ){
			$cfId	= intval($param['chofnid']);
			$pwd	= $param['password'];
			$salt	= $param['salt'];
	        $is		= $this->import('user')->resetCfPwd($cfId, $pwd,$salt);
	        $code	= !empty($is) ? 1 : 2;
			$msg	= !empty($is) ? '修改成功' : '修改失败';
		}
		$data['code'] 	= $code;
		$data['msg']	= $msg;
		$this->import('interface')->updateLog($logId,$data);
		return $data;
	}
	/**
	* 超凡网用户添加到用户中心用户
	* @author	haydn
	* @since	2016-04-12
	* @param 	array 		$param
	* @return	array
	*/
	public function regChofnForUc($param)
	{
		$logId 		= $this->import('interface')->addLog($param,$_SERVER["HTTP_REFERER"],__FUNCTION__);
		$uid		= 0;
		$code		= 2;
		$msg		= '';
		$arr		= array();
		$verifyArr 	= array('account','cateId');
		$msg		= verifyArrayKey($verifyArr,$param);
		if( empty($msg) ){
			$account 	= $param['account'];
			$cateId 	= $param['cateId'];
			$usInfo		= $this->import('user')->getUserInfo($account,$cateId);
			if( empty($usInfo) ){
				$field	= $cateId == 1 ? 'mobile' : 'email';
				$cfInfo = $this->load('user')->getChofnAccount($account,$cateId);
				if( count($cfInfo['data']) > 0 ){
					$cfInfo['data'][$field] = '';
					$uid	= $this->import('user')->addChofn($cfInfo['data']);
					$code	= 1;
					$msg	= '超凡网用户添加成功';
					$arr	= array('uid' => $uid);
				}else{
					$code	= 4;
					$msg	= '超凡网用户不存在';
				}
			}else{
				$code	= 3;
				$msg	= '用户存在';
			}
		}
		$data['code'] 	= $code;
		$data['msg']	= $msg;
		$data['data']	= $arr;
		$this->import('interface')->updateLog($logId,$data);
		return $data;
	}
	/**
	* 超凡网用户添加到用户中心用户
	* @author	haydn
	* @since	2016-04-12
	* @param 	array 		$param
	* @return	array
	*/
	public function changeMobile($param)
	{
		$logId 		= $this->import('interface')->addLog($param,$_SERVER["HTTP_REFERER"],__FUNCTION__);
		$code		= 2;
		$msg		= '';
		$arr		= array();
		$verifyArr 	= array('userId','mobile');
		$msg		= verifyArrayKey($verifyArr,$param);
		if( empty($msg) ){
			$cateId		= 2;
			$account 	= $param['mobile'];
			$userId 	= $param['userId'];
			$usInfo		= $this->import('user')->getUserInfo($account,$cateId);
			if( empty($usInfo) ){
				$this->import('user')->changeMobile($account,$userId);
				$code	= 1;
				$msg	= '修改成功';
			}else{
				$code	= 3;
				$msg	= '用户存在手机更新失败';
			}
		}
		$data['code'] 	= $code;
		$data['msg']	= $msg;
		$data['data']	= $arr;
		$this->import('interface')->updateLog($logId,$data);
		return $data;
	}
}
?>