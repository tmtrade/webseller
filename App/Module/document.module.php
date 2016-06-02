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
class DocumentModule extends AppModule
{
	/**
	* 引用业务模型
	*/
	public $models = array(
		'document'		=> 'document',
	);

	/**
	* 附件入库
	* @author  haydn
	* @since   2016-02-26
	* @param   int			$array  	 当前登录id
	* @return  array		$array
	*/
	public function addFile($array)
	{
		$data['subject'] 	= $array['name'];
		$data['filesize'] 	= $array['size'];
		$data['filetype'] 	= $array['type'];
		$data['filepath'] 	= $array['path'];
		$data['recorddate'] = TIME;
		$data['recordip'] 	= getIp();
		$id     			= $this->import('document')->create($data);
		return $id;
	}
	/**
	* 上传附件
	* @author  haydn
	* @since   2016-02-26
	* @param   string			$filepath  	 	保存路径
	* @param   string||array	$suffixArray	允许上传的后缀(array('jpg','pdf')||'jpg','pdf)
	* @param   string			$filetag  	 	文件域名称
	* @return  array			$array
	*/
	public function uploadFile($filepath='',$suffixArray = '',$filetag = 'files')
    {
    	$array = array();
    	if( !isset($_FILES[$filetag])  && empty($_FILES[$filetag]) ){
			$array['code'] 	= 0;
			$array['msg'] 	= '文件域错误';
			return $array;
    	}
    	$suffixArray 	= is_array($suffixArray) ? $suffixArray : (explode(",", empty($suffixArray) ? 'jpg,gif,png,jpeg' : $suffixArray));
		$fileArr 		= $_FILES[$filetag];
    	$suffix  		= pathinfo($fileArr['name'], PATHINFO_EXTENSION);
    	if( !in_array($suffix,$suffixArray) ){
			$array['code'] 	= 2;
			$array['msg'] 	= '上传文件错误';
			return $array;
    	}  	
		if(!file_exists($filepath)){
			mkdirs($filepath);
		}
		$tmp_name	= $fileArr['tmp_name'];
		$new_name	= rand(1000,9999).time().'.'.$suffix;
		$target		= '.'.$filepath.'/'.$new_name;
		if( copy($tmp_name,$target) ){
			$array['code'] 	= 1;
			$array['msg'] 	= '文件上传成功';
			$array['data']	= array('name' => $fileArr['name'],'newname' => $new_name,'size' => $fileArr['size'],'type' => $suffix,'path' => $target);
		}else{
			$array['code'] 	= 3;
			$array['msg'] 	= '文件上传失败';
		}
		return $array;
    }

}
?>