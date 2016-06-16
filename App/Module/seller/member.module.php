<?php
/**
 * 出售者会员处理基础模型类
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/12 0012
 * Time: 上午 11:54
 */
class MemberModule extends AppModule{

    private $session_time = 36000;//登录状态保存时间--10小时

    //引用业务模型
    public $models = array(
        'user' => 'User',
    );

    /**
     * 发送短信验证码
     * @param $phone
     * @param $type string 短信的模板类型
     * @return int 0成功,1发送间隔小于一分钟,2发送失败,3手机号码为空
     */
    public function sendMsg($phone,$type='newValid'){
        if($phone){
            //检测发送间隔
            $key = $this->com('redisHtml')->get($phone.'code');
            if($key && time()-$key['time']<60){
                return 1;//发送间隔小于1分钟
            }
            //发送短信,并将验证码保存到redis中
            $code = mt_rand(1000,9999);
            $template = C('MSG_TEMPLATE');
            $content	= sprintf($template[$type], $code);
            $rst = $this->importBi('message')->sendMsg($phone, $content, 0);
            if(isset($rst['code']) && $rst['code']==1){
                $this->com('redisHtml')->set($phone.'code', array('code'=>$code,'time'=>time()), 600);//缓存短信验证码
                return 0;
            }
            return 2;
        }
        return 3;
    }

    /**
     * 验证用户的验证码
     * @param $phone
     * @param $code
     * @return bool
     */
    public function checkMsg($phone,$code){
        if($phone){
            //从redis中获得验证码
            $key = $this->com('redisHtml')->get($phone.'code');
            if($key && $key['code']==$code){ //验证结果
                $this->com('redisHtml')->remove($phone.'code');//删除redis中的数据
                return true;
            }
        }
        return false;
    }

    /**
     * 验证码登录
     * @param $phone
     * @param $code
     * @return int 0成功,1创建用户表失败,2创建超凡网账户失败,3验证失败
     */
    public function codeLogin($phone,$code){
        $rst = $this->checkMsg($phone,$code);
        if(!$rst){
            return 3;//验证失败
        }
        $cfInfo = $this->importBi('passport')->get($phone, 2);
        if( !empty($cfInfo['data']['id']) ){ //登录账户存在
            $userinfo = $this->getUserByUid($cfInfo['data']['id']);//查询对应用户表的信息
            //保存用户登录信息
            $data = array();
            $data['nickname'] = $cfInfo['data']['nickname'];
            $data['mobile'] = $cfInfo['data']['mobile'];
            $data['email'] = $cfInfo['data']['email'];
            $data['id'] = $userinfo['id'];
            $data['userId'] = $cfInfo['data']['id'];
            $this->setLogin($data);
            return 0;
        }else{//创建新用户
            $rst = $this->createCf($phone);
            if($rst!=1 && $rst!=2){
                $rst = explode('-',$rst);
                //保存用户登录信息
                $data = array();
                $data['nickname'] = '';
                $data['mobile'] = $phone;
                $data['email'] = '';
                $data['id'] = $rst[1];
                $data['userId'] = $rst[0];
                $this->setLogin($data);
                return 0;
            }
            return $rst;
        }
    }

    /**
     * 绑定手机号码
     * @param $phone
     * @param $code
     * @return int 0成功,1验证失败,2超凡网修改失败
     */
    public function bindMobile($phone,$code){
        $rst = $this->checkMsg($phone,$code);
        if(!$rst){
            return 1;//验证失败
        }
        $rst = $this->importBi('passport')->changeMobile(USERID, $phone);
        if(isset($rst['code']) && $rst['code']==1){
            return 0;
        }
        return 2;
    }

    /**
     * 账户登录
     * @param $account
     * @param $code
     * @return bool
     */
    public function passwordLogin($account,$code){
        //设置账户类型
        if(strpos($account,'@')===false){
            $type = 2;
        }else{
            $type = 1;
        }
        //调用接口登录
        $rst = $this->importBi('passport')->login($account, $type, $code, getClientIp());
        if(isset($rst['data']['id'])){
            //设置登录信息
            if(!$rst['data']['id']){
                return false;
            }
            $userinfo = $this->getUserByUid($rst['data']['id']);//查询对应用户表的信息
            //保存用户登录信息
            $data = array();
            $data['nickname'] = $rst['data']['nickname'];
            $data['mobile'] = $rst['data']['mobile'];
            $data['email'] = $rst['data']['email'];
            $data['id'] = $userinfo['id'];
            $data['userId'] = $rst['data']['id'];
            $this->setLogin($data);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 创建本站账户
     * @param $params
     * @return int
     */
    public function createUser($params){
        $params['regDate'] = time();
        return $this->import('user')->create($params);
    }

    /**
     * 创建超凡网账户
     * @param $phone
     * @return int|string string(-)成功,1创建用户表失败,2创建超凡网账户失败
     */
    public function createCf($phone){
        //准备数据
        $password = $this->randString();
        $uid = $this->importBi('passport')->register($phone, $password, 2, getClientIp());//创建超凡账户
        if($uid>0){
            //发送密码短信
            $template = C('MSG_TEMPLATE');
            $content	= sprintf($template['register'], $password);
            $this->importBi('message')->sendMsg($phone, $content, 0);
            //创建站内用户表
            $data = array();
            $data['userId'] = $uid;
            $rst = $this->createUser($data);
            if($rst){
                return $uid.'-'.$rst;
            }
            return 1;
        }
        return 2;
    }

    /**
     * 修改本站账户
     * @param $condition string 条件(一维数组)
     * @param $params
     * @return bool
     */
    public function editUser($condition,$params){
        $r['eq'] = $condition;
        $params['updated'] = time();
        return $this->import('user')->modify($params,$r);
    }

    /**
     * 根据超凡网id得到本站对应信息(无则创建)
     * @param $uid
     * @return array
     */
    public function getUserByUid($uid){
        $r['eq']['userId'] = $uid;
        $rst = $this->import('user')->find($r);
        if($rst){ //有结果返回信息
            return $rst;
        }
        //无用户表信息,添加信息
        $data = array();
        $data['userId'] = $uid;
        $rst = $this->createUser($data);
        return array('id'=>$rst,'userId'=>$uid);
    }

    /**
     * 发送邮箱验证邮件
     * @param $email
     * @return int 0发送成功,1发送失败,2手机号码为空
     */
    public function sendEmail($email){
        if($email){
            //构建激活地址
            $key = md5($email.UID.'yzc');
            $encode_email = base64_encode($email);
            $url = SITE_URL."member/checkEmail/?id=".UID."&key={$key}&t={$encode_email}";//激活的地址
            //邮件内容-----------------------待确定-------------------------------------
            $content = '';
            $title = '一只蝉出售者平台';
            $rst = $this->importBi('message')->sendMail($email, $title, $content, '', $from='一只蝉');
            if(isset($rst['code']) && $rst['code']==1){
                return 0;
            }
            return 1;
        }
        return 2;
    }

    /**
     * 验证邮箱并更改账户绑定邮箱
     * @param $id
     * @param $key
     * @param $t
     * @return int 0成功,1参数错误,2验证失败
     */
    public function checkEmail($id,$key,$t){
        $email = base64_decode($t);
        if($key==md5($email.$id.'yzc')){ //验证通过
            //得到当前id对应的userId信息
            $userId = $this->import('user')->get($id,'userId');
            if($userId){
                //调用超凡网接口更改信息
                $this->importBi('passport')->changeEmail($userId, $email);
                return 0;
            }else{
                return 1;
            }
        }
        return 2;
    }

    /**
     * 生成指定长度的随机字符串
     * @param int $len
     * @return string
     */
    public function randString($len=6){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $chars = str_shuffle($chars);
        if($len>10){
            $len = 6;
        }
        return substr($chars,0,$len);
    }

    /**
     * 保存用户登录信息
     * @param $data
     */
    private function setLogin($data){
        //修改用户登录信息
        $r['eq']['id'] = $data['id'];
        $record = array(
            'loginNum'=>array('loginNum',1),
            'lastDate'=>time(),
        );
        $this->import('user')->modify($record,$r);
        //取回公共发信的站内信
        $this->load('messege')->createSelfMsg($data['id']);
        //保存用户信息
        Session::set('userinfo',$data,$this->session_time);
    }

    /**
     * 删除用户登录信息
     */
    public function loginOut(){
        Session::remove('userinfo');
    }
}