<?php
/**
 * 发送验证邮件，验证短信工具类
 *
 * @author    Top
 * @since     2013年11月11日17:23:24
 * @copyright CHOFN
 */
class Verify {
	
	 /**
     * 发送验证短信
     *
     * @return null
     *
     * @author Top
     * @since 2013年11月5日10:23:24
     * @copyright CHOFN
     */
	public static function SMS($target,$data){
	    $resultList =self::http_request($target,'POST',$data);
	    return $resultList[0];
	}
	
	 /**
     * 发送验证邮件
     *
     * @return null
     *
     * @author Top
     * @since 2013年11月5日10:23:24
     * @copyright CHOFN
     */
	public static function sendEmail($to, $subject, $html , $from = "info@send.chofn.com", $fromname = '超凡-商标管家'){
		if(empty($to) || empty($subject) || empty($html) ){
			return false;
		}
		if( ! $from){
			$from = "guanjia@send.chofn.com";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send.json');
	
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			array(
				'api_user' => 'guanjia',//API账户
				'api_key' => 'KWmCCxkLqL8M',//API密码
				'label' => '486',//API-内部标签
				//以上三项勿动
				
				'from' => $from,//发送者邮箱地址
				'fromname' => '超凡-商标管家',//发送者名称
				'to' => $to,//接收者邮箱地址
				'subject' => $subject,//邮件标题
				'html' => $html,//内容
				//'file1' => '@/path/to/附件.png;filename=附件.png',//附件1
				//'file2' => '@/path/to/附件2.txt;filename=附件2.txt'//附件2
			)
		);
	
		$result = curl_exec($ch);
		if($result === false){ //请求失败
			//echo 'last error : ' . curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
	

	 /**
     * 发起http请求
     *
     * @return null
     *
     * @author Top
     * @since 2013年11月5日10:23:24
     * @copyright CHOFN
     */
	public static function  http_request($url,$method='GET',$data='',$cookie='',$refer=''){
		$header='';
		$body='';
		$newcookie='';
		if (preg_match('/^http:\/\/(.*?)(\/.*)$/',$url,$reg)){$host=$reg[1]; $path=$reg[2];}
		else {outs(1,"URL($url)格式非法!"); return;}
		$http_host=$host;
		if (preg_match('/^(.*):(\d+)$/', $host, $reg)) {$host=$reg[1]; $port=$reg[2];}
		else $port=80;
		$fp = fsockopen($host, $port, $errno, $errstr, 30);
		if (!$fp) {
			outs(1,"$errstr ($errno)\n");
		} else {
			fputs($fp, "$method $path HTTP/1.1\r\n");
			fputs($fp, "Host: $http_host\r\n");
			if ($refer!='') fputs($fp, "Referer: $refer\r\n");
			if ($cookie!='') fputs($fp, "Cookie: $cookie\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($data)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data . "\r\n\r\n");
			$header_body=0;
			$chunked_format=0;
			$chunked_len=0;
			while (!feof($fp)) {
				$str=fgets($fp);
				//$len=hexdec($str);        if ($header_body==1) {echo ">>$str\t$len\n";        $str=fread($fp,$len);echo $str;}
				if ($header_body==1){
					if ($chunked_format){
						if ($chunked_len<=0){
							$chunked_len=hexdec($str);
							if ($chunked_len==0) break;
							else continue;
						} else {
							$chunked_len-=strlen($str);
							if ($chunked_len<=0) $str=trim($str);
							//elseif ($chunked_len==0) fgets($fp);
						}
					}
					$body.=$str;
				}
				else if ($str=="\r\n") $header_body=1;
				else {
					$header.=$str;
					if ($str=="Transfer-Encoding: chunked\r\n") $chunked_format=1;
					if (preg_match('|Set-Cookie: (\S+)=(\S+);|',$str,$reg)) $newcookie.=($newcookie==''?'':'; ').$reg[1].'='.$reg[2];
				}
			}
			fclose($fp);
		}
		$GLOBALS['TRAFFIC']+=414+strlen($url)+strlen($data)+strlen($header)+strlen($body);
		if (preg_match('/^Location: (\S+)\r\n/m',$header,$reg)) {
			if (substr($reg[1],0,1)!='/'){
				$path=substr($path,0,strrpos($path,'/')+1);
				$path.=$reg[1];
			} else $path=$reg[1];
			if ($newcookie) $cookie=$newcookie;
			return http_request('http://'.$http_host.$path,'GET','',$cookie,$url);
		}
		return array($body, $header, $newcookie);
	}
}