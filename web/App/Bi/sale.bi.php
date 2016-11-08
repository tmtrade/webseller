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
	    $params = array(
		    'user' => 'api1010',
		    'sign' => $this->sign($data),
		    'data' => $data,
		    );
	    return $this->request("systemapi/addSale/", $params, 5);
    }
    
    //修改联系人价格
    public function updateContactPrice($data)
    {
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