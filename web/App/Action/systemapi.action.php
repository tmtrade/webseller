<?
/**
 * 对外系统接口
 *
 * 分配系统回写跟客记录
 *
 * @package     Action
 * @author      Martin
 * @since       2015/11/4
 */
class SystemApiAction extends RpcServer // extends Action//
{
	public function pushPriceTest()	
	{
		$params = array(
			'number'	=> '11644245',
			'oldInfo'	=> array(
							'price'			=> 5000,
							'priceType'		=> 2,
							'isOffprice'	=> 2,
							'salePrice'		=> 0,
							'salePriceDate' => 0,
							'isSale'		=> 1,
							'isLicense'		=> 1,
							),
			'newInfo'	=> array(
							'price'			=> 10000,
							'priceType'		=> 1,
							'isOffprice'	=> 1,
							'salePrice'		=> 4000,
							'salePriceDate' => 1456823512,
							'isSale'		=> 1,
							'isLicense'		=> 1,
							),
			'webname'	=> 'tradmin',//不变
			'key'		=> "trademark1104martinewodd",//不变
		);
		$this->pushTrademarkPrice($params);

	}


	
    /**
     * 交易站推送商标价格变动信息接口
     *
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   
     * @since    	2016/2/29
     * @return    	bool
     */
	public function pushTrademarkPrice($params)
	{
		$keyarray	= array('number', 'oldInfo', 'newInfo', 'webname', 'key');
		$websign	= array("tradmin" => '6b8736d9bcd0d9d8353f6d4ffa8a251a');

		$this->checkParams($params, $keyarray, $websign);
		extract($params);
		$bool		= $this->load('changeprice')->addChange($number,$oldInfo,$newInfo);
		if(!$bool){ return json_encode('0');die; }
		return json_encode('1'); die; 
	}
    /**
     * 测试获取商标的关注
     *
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   
     * @since    	2016/2/29
     * @return    	bool
     */
	public function testcollect()
	{
		$params = array(
			'uc_ukey'	=> '35EN4VG5',
			'trademark'	=> '1966521',
			'source'	=> '1',
			'webname'	=> 'usercenter',//不变
			'key'		=> "trademark1104martinewodd",//不变
		);
		$this->getCollectTrademark($params);

	}

    /**
     * 返回关注商标信息
     *
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   
     * @since    	2016/2/29
     * @return    	json
     */
	public function getCollectTrademark($params)
	{
		$keyarray	= array('uc_ukey', 'trademark', 'source', 'webname', 'key');
		$websign	= array("usercenter" => '6b8736d9bcd0d9d8353f6d4ffa8a251a');
		$this->checkParams($params, $keyarray, $websign);
		extract($params);
		$userId		= $this->load('sessions')->getUserIdByCookie($uc_ukey);
		if ($userId == '') { return json_encode($this -> returnError('user'));die; }

		$bool		= $this->load('collect')->getUserCollect($userId,$trademark, $source);
		return $bool;
	} 
	

	
    /**
     * 通过Cookies获取用户ID
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   
     * @since    	2016/3/21
     * @return    	json
     */
	public function getUserId($params)
	{

		$keyarray	= array('uc_ukey','webname', 'key');
		$websign	= array("usercenter" => '6b8736d9bcd0d9d8353f6d4ffa8a251a');
		$this->checkParams($params, $keyarray, $websign);
		extract($params);
		$userId		= $this->load('sessions')->getUserIdByCookie($uc_ukey);
		return $userId;
	}

	
    /**
     * 检查参数是否正确
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   
     * @since    	2016/2/29
     * @return    	bool
     */
	private function checkParams($params,$keyarray, $websign){
		
		$bool = $this->checkKey($params, $keyarray);
		if ($bool) { return json_encode($this -> returnError('key'));die; }
		$bool = $this->sign($params, $websign);
		if ($bool) { return json_encode($this -> returnError('sign'));die; }
	}

	
    /**
     * 检查键值是否正确
     *
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   传递参数
	 * @param		array   $keyarray 键值   
     * @since    	2016/2/29
     * @return    	bool
     */
	private function checkKey($params, $keyarray){
		$bool = array_diff(array_keys($params), $keyarray);
		if($bool){
			return true;
		}
        return false;
	}

    /**
     * sign签名
     *
     * @author    	martin
     * @copyright 	CHOFN
	 * @param		array   $params   传递参数
	 * @param		array   $websign  签名  
     * @since    	2016/2/29
     * @return    	bool
     */
    protected function sign($params, $websign)
    {
		$sign = md5( md5(serialize( $params['key'] )));
        if( $sign == $websign[ $params['webname'] ]){
			return false;
		}
        return true;
    }

	/**
	 * @错误返回
	 * @since  2015/11/4
	 * @author Fred
	 */
	private function returnError ($value) {
		switch ($value) {
			case 'sign'		: $string   = 'Sign error';
				break;
			case 'params' 	: $string	= 'Params error!';
				break;
			case 'key' 		: $string	= 'Key error , check key and from value!';
				break;
			case 'user' 	: $string	= 'user error!';
				break;
			case 'insert' 	: $string	= '0';
				break;
		}
		return $string;
	}


}
?>