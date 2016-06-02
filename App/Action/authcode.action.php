<?
/**
* 项目首页
*
* 首页内容展示
*
* @package	Action
* @author	void
* @since	2015-11-20
*/
class AuthcodeAction extends AppAction
{
	

	/**
	* 控制器默认方法
	* @author	martin
	* @since	2016/1/18
	*
	* @access	public
	* @return	void
	*/
	public function index()
	{
		session_start();
		require WebDir.'/App/Util/ValidateCode.class.php';  //先把类包含进来，实际路径根据实际情况进行修改。
		$_vc = new ValidateCode();  //实例化一个对象
		$_vc->doimg();  
		$_SESSION['authnum_session'] = $_vc->getCode();//验证码保存到SESSION中
	}
}

?>