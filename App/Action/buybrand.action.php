<?
/**
* 项目首页
*
* 购买商标
*
* @package	Action
* @author	void
* @since	2015-11-20
*/
class BuyBrandAction extends AppAction
{
	/**
	* 引用业务模型
	*/
	public $models = array(
		'verify'	=> 'verify',
	);

	/**
	* 控制器默认方法
	* @author	haydn
	* @since	2016/3/11
	*
	* @access	public
	* @return	void
	*/
	public function buyBrandAdd()
	{
		$data			= $this->getFormData('buybrandindex');
		$data['userId']	= !empty($this->userInfo['id']) ? $this->userInfo['id'] : 0;
		$data['name']	= !empty($data['name']) ? $data['name'] : '';
		$this->verifyAjax($data['callback']);//跨域使用
		$array			= $this->load('buybrand')->buyAdd($data);
		$this->setJson($array,$data['callback']);
	}
	/**
	* 验证是否购买
	* @author	haydn
	* @since	2016/3/14
	* @access	public
	* @return	void
	*/
	public function buyExist()
	{
		$callback	= $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);
		$trademark	= $this->input('trademark', 'string', '');
		$class		= $this->input('class', 'string', '');
		$tel		= $this->input('tel', 'string', '');
		$userId		= !empty($this->userInfo['id']) ? $this->userInfo['id'] : 0;
		$account	= $userId == 0 ? $tel : $userId;//两种验证（登录或未登录）
		$count		= $this->load('buybrand')->exist($account,$trademark,$class);
		if( $count == 0 ){
			$code 	= 1;
			$msg	= '没有添加';
		}else{
			$code 	= 2;
			$msg	= '已添加过';
		}
		$array		= array('code' => $code,'msg' => $msg);
		$this->setJson($array,$callback);
	}

	/**
	* 验证是否合法
	* @since	2016-02-19
	* @author	haydn
	* @param	string	$callback	跨域编号
	* @return	void
	*/
	private function verifyAjax($callback)
	{
		if( !empty($callback) ){
			$is = $this->checkSource();
			if(!$is){
				$callback	= $this->input('callback', 'string', '');//跨域使用
				$result 	= array('code' => '-1','msg' => 'key验证失败');
				$this->setJson($result,$callback);
			}
		}else{
			!$this->isAjax() && $this->redirect('', '/');
		}
	}
	/**
	* 设置返回的json值
	* @static	2016-01-25
	* @author	haydn
	* @param 	array 		$array		返回数据
	* @param 	string		$callback	token值
	* @return	void
	*/
	private function setJson($array,$callback)
	{
		if(!empty($callback)){			
			exit($callback . "([" . json_encode($array) . "])");
		}else{
			$this->returnAjax($array);
		}
	}
}
?>