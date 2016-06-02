<?
/**
* 
* 接口日志
* 
* @package	haydn
* @author	haydn
* @since	2016-03-09
*/
class InterfaceModel extends AppModel
{
    /**
    * 写入日志
    * 
    * @access  public
    * @author  haydn
    * @since   2016-03-09
    * @param   string||array    $string  	请求信息
    * @param   string     		$url 		来源
    * @param   string    		$apiKey  	方法名称
    * @return  int        		$id
    */
    public function addLog($string,$url = '',$apiKey = '')
    {
    	$string				= is_array($string) ? serialize($string) : $string;
    	$data['apiKey']		= $apiKey;
        $data['request']    = $string;//请求
        $data['response']   = '';
        $data['url']     	= $url;
        $data['recordip']   = getClientIp();
        $data['recorddate'] = TIME;
        $id                 = $this->create($data);
        return $id;
    }

    /**
     * 修改日志
     * @author  martin
     * @since   2016-01-18
     * @param   int    			 $id  		日志id
     * @param   string||array    $string  	请求信息
     * @return  void
     */
    public function updateLog($id,$string)
    {
    	$string		= is_array($string) ? serialize($string) : $string;
		$r['eq']	= array('id' => $id);
		$data		= array('response' => $string, 'modified'=> TIME);
		return $this->modify($data, $r);
    }
}
?>