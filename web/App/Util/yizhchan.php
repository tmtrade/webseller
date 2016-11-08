<?php
error_reporting(E_ALL);
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
/**
 * 定义接口地址
 * 本地测试环境：http://demo.chofn.com:88/
 * 线上生产环境：http://system.chofn.net/
 * @var string
 */
define('CRM_API_HOST', "http://demo.chofn.com:88/");
/**
 * 定义接口用户名
 * @var string
 */
define('CRM_API_USER', 'yizhchan');
/**
 * 定义接口密钥
 * @var string
*/
define('CRM_API_KEY', '216Iv321Sz247FDasasafff');
class yizhchan{
	public static function request($param)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, CRM_API_HOST.'Api/yizhchan.php');
		curl_setopt(
			$ch, CURLOPT_POSTFIELDS,
			array_merge(
				array(
					'api_user' => CRM_API_USER,
					'api_key' => md5(CRM_API_KEY.$param['id'])
				),
				$param
			)
		);
		$result = curl_exec($ch);//print_r($result);
		if($result === false) {
			$result = curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
	/**
     * 获取网络信息
     * @since  	2016-01-11
     * @author 	haydn
	 * @param	int 	$cid	客户id
     * @return 	json 	$data 	数据包
     */
	public static function getNetwork($query)
	{
		$param = array(
				'api_type'   => 'getNetwork',
				'id'         => 100001,
				'map'		 => serialize($query),
		);
		return interfaces::request($param);
	}
	/**
     * 用网络id获取信息
     * @since  	2016-01-11
     * @author 	haydn
	 * @param	int 	$aid	网络信息id
     * @return 	json 	$data 	数据包
	 */
	public static function getBrandInfo($aid)
	{
		$param = array(
				'api_type'   => 'getBrandInfo',
				'id'         => 100001,
				'aid'		 => $aid,
		);
		return interfaces::request($param);
	}	
}
/*
//===========================实例1：获取网络信息====================
$query = array(
			'tel'		 => 13594688538,//电话
			'cid'		 => 1382219,    //客户id
		);
$json = yizhchan::getNetwork($query);




//============================实例2：用网络id获取信息=========================

$json = yizhchan::getBrandInfo(1880039);


$json = json_decode($json,true);
print_r($json);
*/
?>