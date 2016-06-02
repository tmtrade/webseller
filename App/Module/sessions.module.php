<?
/**
 * 应用业务组件基类
 *
 * 存放业务组件公共方法
 *
 * @package	Model
 * @author	void
 * @since	2015-11-20
 */
class SessionsModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'sessions'		=> 'sessions',
    );

	/**
	 * 通过COOKIES获取userId
	 * @author	martin
	 * @since	2016/2/22
	 *
	 * @access	public
	 * @param	string	$cookie		cookiesID
	 * @return	string  返回业务对象
	 */
	public function getUserIdByCookie( $cookie )
	{
		if( empty($cookie) ) return '';
		$r['eq']	= array( 'cookieId' => $cookie );
		$r['order']	= array( 'id' => 'desc' );
		$r['limit']	= 1;

		$data		= $this->import( 'sessions' )->find( $r );
		return isset( $data['userId'] ) ? $data['userId'] : '';
	}
	/**
	* 获取登录时间
	* @author	haydn
	* @since	2016-04-18
	* @param	int		$userId		用户id
	* @param	int		$thetime	本次登录时间
	* @return	int		$time		时间
	*/
	public function getloginDate($userId,$thetime = 0)
	{
		$r['raw']	= "`loginDate` < {$thetime} and `userId` = {$userId}";
		$r['order']	= array( 'id' => 'desc' );
		$r['limit']	= 1;
		$data		= $this->import( 'sessions' )->find( $r );
		return $data['loginDate'];
	}
}
?>