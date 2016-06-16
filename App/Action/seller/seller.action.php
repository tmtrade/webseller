<?
/**
 * 出售者平台应用公用控制器
 *
 * 存放应用的公共方法
 *
 * @package    Action
 * @author    void
 * @since    2015-01-28
 */
abstract class SellerAction extends Action
{
    public $username = '';
    public $userId = '';
    public $id = '';//本站的用户id
    public $isLogin = false;
    public $ignore = array( //不验证登录的地址
        'member/index',
        'member/login',
        'member/loginOut',
        'member/sendCode',
    );

    /**
     * 前置操作(框架自动调用)
     * @author    void
     * @since    2015-01-28
     *
     * @access    public
     * @return    void
     */
    public function before()
    {
        //验证登录,并设置相关信息
        //$this->checkLogin();
        //得到站内信的数量
        $msg_num = $this->load('messege')->getMsgNum();
        $this->set('msg_num',$msg_num);
        //静态文件版本号>>控制js,css缓存
        $this->set('static_version', 9980);
    }

    /**
     * 后置操作(框架自动调用)
     * @author    void
     * @since    2015-01-28
     *
     * @access    public
     * @return    void
     */
    public function after()
    {

    }

    /**
     * 验证登录,并设置相关信息
     * @return bool
     */
    protected function checkLogin(){
        //得到当前的地址
        $url = $this->mod.'/'.$this->action;
        if(in_array($url,$this->ignore)){ //忽略验证的url
            return;
        }else{//验证是否登录
            $userinfo = Session::get('userinfo');
            if($userinfo){
                //设置用户信息
                $username = '未知';//用户名---优先级-用户名>手机>邮箱
                if($userinfo['username']){
                    $username = $userinfo['nickname'];
                }else if($userinfo['mobile']){
                    $username = $userinfo['mobile'];
                }else if($userinfo['email']){
                    $username = $userinfo['email'];
                }
                //设置其他信息
                $this->userId = $userinfo['userId'];
                $this->id = $userinfo['id'];
                $this->isLogin = true;
                $this->username = $username;
                if(!defined('USERID')) define('USERID',$userinfo['userId']);//定义userId常量USERID
                if(!defined('UID')) define('UID',$userinfo['id']);//定义id常量UID
            }else{//未登录,跳转到登录页面
                $this->redirect('','/member/index');
            }
        }
    }

    /**
     * 输出json数据
     *
     * @author    dower
     * @since    2016-6-12
     *
     * @access    public
     * @return    void
     */
    protected function returnAjax($data=array())
    {
        $jsonStr = json_encode($data);
        exit($jsonStr);
    }

    /**
     * 获取输入参数
     *
     * @access    protected
     * @param    string    $name    参数名
     * @param    string    $type    参数类型
     * @param    int        $length    参数长度(0不切取)
     * @return    mixed(int|float|string|array)
     */
    protected function getParam($name, $type = 'string', $length = 0)
    {
        $types = array('int', 'float', 'array', 'string');
        if ( !in_array($type, $types) )
        {
            return '';
        }
        $value = isset($this->input[$name]) ? $this->input[$name] : '';
        if ( empty($value) )
        {
            if ( $type == 'string' )
            {
                return '';
            }
            if ( $type == 'array' )
            {
                return array();
            }
        }
        if ( $type == 'int' )
        {
            return intval($value);
        }
        if ( $type == 'float' )
        {
            return $length == 0 
                ? sprintf("%.2f", floatval($value)) 
                : sprintf("%.{$length}f", floatval($value));
        }
        if ( $type == 'array' )
        {
            return $value;
        }
        $length = $length ? intval($length) : 0;
        return $length ? substr($value, 0, $length) : $value;             
    }

    /**
     * 检测当前url地址(操作)是否发送站内信
     * @param $uid int|string 站内信的发送对象(群发以逗号隔开)
     * @param $sendtype int 站内信的发送方式,默认对一,2对多,3全体
     */
    protected function checkMsg($uid = null,$sendtype=1){
        //设置发送的对象
        if(!$uid){
            $uid = $this->userId;
        }
        //设置发送的类型
        if(!in_array($sendtype,array(1,2,3))){
            $sendtype = 1;
        }
        //得到当前url地址
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$this->mod.'/'.$this->action;
        //得到监控触发的信息
        $monitor = $this->load('messege')->getMonitor();
        if($monitor){
            //判断当前url是否发送信息
            foreach($monitor as $item){
                if(strpos($item['url'],$url)!==false){
                    $params = array();
                    $params['title'] = $item['title'];
                    $params['type'] = $item['type'];
                    $params['sendtype'] = $sendtype;
                    $params['content'] = $item['content'];
                    $params['uids'] = $uid;//当前用户
                    $this->load('messege')->createMsg($params);
                    break;
                }
            }
        }
    }
}
?>