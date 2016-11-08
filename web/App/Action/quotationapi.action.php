<?
/**
 *
 * 报价单接口(对内)
 *
 * @package     Action
 * @author      dower
 * @since       2016-03-01
 */
class QuotationApiAction extends RpcServer
{
	protected $users;//接口用户
    protected $keys;//接口密钥

    /**
     * 删除报价单
     * @param $params
     * @return array|bool
     */
    public function removeQuotation($params)
    {
        $check  = $this->checkout($params);
        if ( $check !== true ) return $check;
        //得到参数
        $data = $this->formattData($params['data']);

        //报价单id
        if ( empty($data['id']) ){
            return array('code'=>400,'msg'=>'参数错误');
        }
        //用户id
        if ( empty($data['uid']) ){
            return array('code'=>400,'msg'=>'参数错误');
        }
        $flag = $this->load('quotation')-> delete($data['id'],$data['uid']);
        if($flag){
            return array('code'=>200,'msg'=>'操作成功');
        }else{
            return array('code'=>417,'msg'=>'操作失败');
        }
    }

    /**
     * 得到参数
     * @param $data
     * @return array
     */
    protected function formattData($data){
        $this->input    = $data;
        $id = $this->getParam('id', 'int');
        $uid = $this->getParam('uid', 'int');
        return array(
            'id'=>$id,
            'uid'=>$uid,
        );
    }

    /**
     * 验证数据
     * @param $params
     * @return array|bool
     */
    protected function checkout($params)
    {
        //得到定义的用户
        $this->users    = C('API_QUOTATION_USERS');
        $this->keys     = C('API_QUOTATION_KEYS');
        //得到参数
    	$user = empty($params['user']) ? '' : $params['user'];
    	$sign = empty($params['sign']) ? '' : $params['sign'];
    	$data = empty($params['data']) ? array() : $params['data'];
        //判断用户是否正确
    	if ( empty($user) || !in_array($user, $this->users) ) return array('code'=>401,'msg'=>'未授权');
        //判断签名是否正确
        if ( empty($sign) || $sign != $this->sign($data, $user) ) return array('code'=>403,'msg'=>'签名错误');
        //判断数据是否正确
        if ( empty($data) ) return array('code'=>400,'msg'=>'参数不完整');
        return true;
    }

    /**
     * 数据签名
     * @param $data
     * @param $user
     * @return string
     */
    protected function sign($data, $user)
    {
        ksort($data, SORT_STRING);
        $apiKey = $this->keys[$user] ? $this->keys[$user] : 'nobody';
        $sign   = md5( md5(serialize($data)).$apiKey );
        return $sign;
    }

}
?>