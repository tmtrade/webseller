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
class ChangelogsModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'changelogs'		=> 'changelogs',
    );
	/**
	 * 获取业务对象(系统对接时使用)
	 * @author	void
	 * @since	2015-11-20
	 *
	 * @access	public
	 * @param	int		$userId		用户编号
	 * @param	array	$new		用户新信息
	 * @param	array	$old		用户旧信息
	 * @return	object  返回业务对象
	 */
	public function updateUserInfo($userId, $newInfo ,$oldInfo)
	{
		$user_key	= array_keys($newInfo);
		$findkey	= array('username','mobile','email');
		$intersect	= array_intersect($user_key, $findkey);
		if( empty($intersect) ) return false;

		foreach($newInfo as $key => $item){
			if( in_array( $key, $findkey ) && trim( $oldInfo[ $key ] ) != trim($newInfo[ $key ]) ){
				$type	= 0;
				switch ($key)
				{
					case 'username':
						$type = 3;
					  break;  
					case 'mobile':
						$type = 1;
						break;
					case 'email':
						$type = 2;
						break;
				}
				if($type == 0) { continue; }
				$save	= array(
						'userId'	=> $userId,
						'type'		=> $type,
						'old'		=> $oldInfo[ $key ],
						'new'		=> $newInfo[ $key ],
						'created'	=> time(),
						'ip'		=> getClientIp(),
						);
				$this->import('changelogs')->create($save);
			}
		}
		return true;

	}
	
	
}
?>