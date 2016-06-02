<?
/**
 * 项目申请人页面
 *
 *
 * @package	Action
 * @author	void
 * @since	2016-02-14
 */
class ProposerAction extends AppAction
{
    public $num = 10;
	/**
	 * 控制器默认方法
	 * @author	void
	 * @since	2015-11-20
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{  
        if( $this->isLogin == true ){            
           $this->redirect('', '/user/index/');
        }
        $this->display();
	}
    /**
    * 我的申请人
    * @author   hyand
    * @since    2016-02-15
    * @return   void
    */
    public function myProposer()
    {
    	$uid 	= $this->userInfo['id'];
    	$num	= $this->getSurplusNum($uid);
    	
    	$data 	= $this->load('userproposer')->getMyProposerList($uid);
    	$this->set('data', $data);
    	$this->set('num', $num);
        $this->display();
    }
    /**
    * 申请人查询列表
    * @author   hyand
    * @since    2016-02-19
    * @return   json    返回检查结果数据包
    */
    public function proposerQueryList()
    {
    	$html		= '';
		$name 		= $this->input('pname','string');
		$address 	= $this->input('paddress','string');
		$page 		= $this->input('page','int');
		$uid 		= $this->userInfo['id'];
		$num		= $this->getSurplusNum($uid);
		$mydata		= $this->load('userproposer')->getMyProposer($uid);		
		$limit     	= 20;
		$data 		= $this->load('proposer')->getProposerList($name,$address,$page,$limit);
		if( $data['total'] == 0 ){
			$this->set('pname', $name);
			$this->set('paddress', $address);
			$html = 'proposer/proposer.proposernoquerylist.html';
		}else{
			$pager   	= $this->pager($data['total'], $limit);
        	$pageBar 	= empty($data['rows']) ? '' : getPageBar($pager);
        	$this->set('contact', $data['rows']);
        	$this->set('pageBar', $pageBar);
        	$this->set('mydata', $mydata);
		}
		$this->set('num', $num);
		$this->display($html);
    }
    /**
    * ajax申请人查询列表添加
    * @author   hyand
    * @since    2016-02-19
    * @return   json    返回检查结果数据包
    */
    public function addHaveProposer()
    {
    	!$this->isAjax() && $this->redirect('', '/');
		$uid 		= $this->userInfo['id'];
		$pid 		= $this->input('pid','string');
		$code		= $this->load('userproposer')->addHaveProposer($uid,$pid);
		$num		= $this->getSurplusNum($uid);
		$msg		= $this->getAddMsg($code);
		$result     = array('code' => $code,'msg' => $msg,'num' => $num);
        $this->returnAjax($result);
    }
    /**
    * ajax申请人自定义添加
    * @author   hyand
    * @since    2016-02-19
    * @return   json    返回检查结果数据包
    */
    public function addProposer()
    {
    	!$this->isAjax() && $this->redirect('', '/');
		$uid 		= $this->userInfo['id'];
		$name 		= $this->input('pname','string');
		$address 	= $this->input('paddress','string');
		$data		= array('name' => $name,'address' => $address);
		$code		= $this->load('userproposer')->addProposer($uid,$data);
		$num		= $this->getSurplusNum($uid);
		$msg		= $this->getAddMsg($code);
		$result     = array('code' => $code,'msg' => $msg,'num' => $num);
        $this->returnAjax($result);
    }
    /**
    * 删除申请人
    * @author   hyand
    * @since    2016-02-19
    * @return   int		$num	剩余数量
    */
    public function deleted()
    {
		!$this->isAjax() && $this->redirect('', '/');
		$uid 		= $this->userInfo['id'];
		$id 		= $this->input('id','string');
		$code		= $this->load('userproposer')->deleted($uid,$id);
		$msg		= $code == 1 ? '删除成功' : '删除失败';
		$num		= $this->getSurplusNum($uid);
		$result     = array('code' => $code,'msg' => $msg,'num' => $num);
        $this->returnAjax($result);
    }
    /**
    * 认证申请人
    * @author   hyand
    * @since    2016-02-19
    * @return   int		$num	剩余数量
    */
    public function admitProposer()
    {
    	$uid	= $this->userInfo['id'];
    	$email	= '';
    	$state	= $this->load('userproposer')->getAuditState($uid);
    	if( $state > 0 ){
			$html	= '/proposer/proposer.admitproposeraudit.html';
			$email	= $this->userInfo['email'];
    	}else{
			$data 	= $this->load('userproposer')->getMyProposerList($uid);
    	}    	
    	$this->set('data', $data);
    	$this->set('state', $state);
    	$this->set('email', $email);
    	$this->display($html);
    }
    /**
    * 认证申请人POST
    * @author   hyand
    * @since    2016-02-19
    * @return   int			$code
    */
    public function admitProposerPost()
    {
    	if( $this->isPost() ){
			$file 	= '/uploadfile/'.date('Y-m-d',time());
			$pid	= $this->input('proposer','int');
			$data 	= $this->load('document')->uploadFile($file);
			if( $data['code'] !=1 ){
				$this->redirect('证件上传失败', '/proposer/admitProposer/');
			}
			$uid	= $this->userInfo['id'];
			$fid	= $this->load('document')->addFile($data['data']);
			$code	= 0;
			if( $fid > 0 ){
				$code = $this->load('userproposer')->updateFid($uid,$pid,$fid);
			}
			$msg	= $code	== 0 ? '证件上传失败' : '证件上传成功';
			$this->redirect($msg, '/proposer/admitProposer/');
		}
		$this->redirect('', '/proposer/admitProposer/');
    }
    /**
    * 获取允许添加的个数
    * @author   hyand
    * @since    2016-02-19
    * @param	int		$uid	用户id
    * @return   int		$num	剩余数量
    */
    private function getSurplusNum($uid)
    {
		$count	= $this->load('userproposer')->getAddNum($uid);
    	$num	= ($this->num - $count);//添加剩余数量
    	return $num;
    }
    /**
    * 申请人添加语言
    * @author   hyand
    * @since    2016-02-19
    * @param	int		$code	返回的code
    * @return   string  $msg
    */
    private function getAddMsg($code)
    {
		$msg = '';
		switch($code){
			case 0;
				$msg = '申请人数据源不存在添加失败';
			break;
			case 1;
				$msg = '添加成功';
			break;
			case 2;
				$msg = '申请人无法重复添加';
			break;
			default;
				$msg = '系统繁忙请稍后再试';
		}
		return $msg;
    }
}
?>