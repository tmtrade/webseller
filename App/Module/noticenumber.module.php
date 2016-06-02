<?
/**
 * 应用业务组件基类
 *
 * 微信处理
 * 
 * @package	Model
 * @author	void
 * @since	2015-11-20
 */
class NoticeNumberModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'notice'		=> 'notice',
        'noticenumber'	=> 'noticenumber'
    );
	 
	/**
     * 获取语言
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$code	code编码
     * @return  string		$msg	对应话术
     */
    public function getNewDate()
	{
		$r['limit']	= 1;
		$r['order']	= array('number'=>'desc');
		$data		= $this->import('noticenumber')->find($r);
		return $data['objEndDate'];
	}
	
	/**
     * 获取语言
     * @author  haydn
     * @since   2016-02-25
     * @param   int			$code	code编码
     * @return  string		$msg	对应话术
     */
    public function getNoticeType($req, $raw = '')
	{
		$r['limit']		= 1;
		$r['eq']		= $req;
		if($raw !=''){
			
			$r['raw']	= $raw;
		}
		$r['eq']		= $req;
		$r['order']		= array('id'=>'desc');
		$data			= $this->import('notice')->find($r);
		if($data){
			$notice		= $this->import('noticenumber')->get($data['number']);
			return $notice['trialDate'];
		}
		return '';
	}
    
}
?>