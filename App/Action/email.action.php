<?php

class EmailAction extends AppAction
{
	public function __construct()
	{
		$title = "商标类别注释 - 知友";
		$this->set("title", $title);
	}
	/**
	 * 验证EMAIL格式是否正确
	 *
	 * @param string $email
	 *        	要验证的EMAIL
	 * @return number boolean
	 * @author Top
	 * @since 2013-11-1 下午3:24:12
	 * @copyright CHOFN
	 */
	function validate_email($email) {
		if (empty ( $email )) {
			return 0;
		}
		$email = trim ( $email );
		return strlen ( $email ) > 5 && preg_match ( "/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $email );
	}

	/**
	 * ajax提交用户反馈
	 *
	 * @return null
	 *
	 * @author    alexey
	 * @since     2015年12月25日 09:34:07
	 * @copyright CHOFN
	 */
	public function feedback()
	{
		$email    = $this->input("email", "string");
		$content  = $this->input("feedback", "string");
		$sendArra = array("services@chofn.com");
		//$sendArra = array("283675222@qq.com");
		include(FILEDIR . "App/Util/Verify.php");
		if ($content != "" && self::validate_email($email)) {

			//import("@.Extend.Tool.Verify");
			$subject  = "知友 - 用户意见反馈";
			$from     = $email;
			$html     = "<div>" . $content . "</div>";
			$fromname = "游客";
			if ($_SESSION['cid'] != 1) {
				$fromname = "游客";
			} else {
				$fromname = $_SESSION['member']['name'];
			}
			foreach ($sendArra as $v) {
				$to = $v;
				Verify::sendEmail($to, $subject, $html, $from, $fromname);
			}
			echo 1;
		} elseif (!self::validate_email($email)) {
			echo 0;
		} elseif ($content == "") {
			echo 2;
		}

	}
}

?>