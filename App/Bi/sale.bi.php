<?
/**
 * FAQ wm.chaofan.wang
 * 
 * @access	public
 * @package bi
 * @author	Xuni
 * @since	2016-03-17
 */
class SaleBi extends Bi
{
    /**
     * 对外接口域名编号
     */
    public $apiId = 9;

    //添加联系人
    public function addSale($data)
    {
	    $actionName = 'systemapi';
	    $funName     = 'addSale';
//	    $param = array(
//		'uid'           => '58001',//用户中心ID
//		'number'        => '100',//商标号
//		'phone'         => '13551111112',//联系人电话
//		'contact'       => '标大帅',//联系人名称
//		'price'         => 44445,//底价
//		'type'          => 1,//1，出售，2，许可，3，出售+许可
//		'source'        => 4,//1，用户中心。
//	    );
	    $params = array(
		    'user' => 'api1010',
		    'sign' => $this->sign($data),
		    'data' => $data,
		    );
	    return $this->request("systemapi/addSale/", $params);
    }
    
    //修改联系人价格
    public function updateContactPrice($data)
    {
	    $actionName = 'systemapi';
	    $funName     = 'updateContactPrice';
	    // $param = array(
	    //     'cid'           => '170151',//联系人信息ID
	    //     'price'         => 12345,//底价
	    // );
	    $params = array(
		    'user' => 'api1010',
		    'sign' => $this->sign($data),
		    'data' => $data,
		    );
	    return $this->request("systemapi/updateContactPrice/", $params);
    }
    
    //取消联系人价格
    public function cancelContact($data)
    {
	    $actionName = 'systemapi';
	    $funName     = 'cancelContact';
	    // $param = array(
	    //     'uid'           => '5800',
	    //     'number'         => '100',
	    // );
	    $params = array(
		    'user' => 'api1010',
		    'sign' => $this->sign($data),
		    'data' => $data,
		    );
	    return $this->request("systemapi/cancelContact/", $params);
    }
	
    function sign($data)
    {
        ksort($data, SORT_STRING);
        $apiKey = 'JyZyZcXmChOfN2016ZxWlQkFkEyYhZx';
        $sign   = md5( md5(serialize($data)).$apiKey );
        return $sign;
    }


}
?>