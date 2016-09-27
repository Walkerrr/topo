<?php

namespace utils;

class MailTool
{

    private $smtpserver;
    private $smtpserverport;
    private $smtpusermail;
    private $smtpuser;
	private $smtppass;

    public function __construct()
    {
		$this->smtpserver = "abc.xyz.com";        //SMTP服务器 
		$this->smtpserverport =25;                 //SMTP服务器端口 
		$this->smtpusermail = "user@xyz.com";  //SMTP服务器的用户邮箱 
		$this->smtpuser = "user@xyz.com";      //SMTP服务器的用户帐号 
		$this->smtppass = "userpasswd";         //SMTP服务器的用户密码 
	}

	/**
	 * 通过邮件发送临时密码
	 */
	function sendAuthkey($mailto,$authKey)
	{
		$smtpemailto = $mailto;              //发送给谁 
		$mailsubject = "本地网传输及宽带规划支撑平台用户临时令牌重置";//邮件主题 
		$mailsubject = "=?utf-8?B?".base64_encode($mailsubject)."?="; //邮件主题编码 
		$mailbody = "<h3>你好:</h3>";//邮件内容 
		$mailbody = $mailbody ."<p>你在《本地网传输及宽带规划支撑平台》申请了口令重置请求或者刚刚成为该平台新用户。</p>";//邮件内容 
		$mailbody = $mailbody ."<p>你的8位临时口令为：".$authKey."，临时密码将在规定时间内失效，请尽快登录并设置自己的密码。</p>";//邮件内容 
		$mailbody = $mailbody ."<p>本地网传输及宽带规划支撑平台的网址为:<a href='http://abcdefg/'>http://abcdefg/</a>,仅限在设计院内网使用</p>";//邮件内容 
		
		$mailbody = $mailbody ."<p>请将本邮件地址加入白名单，以免漏掉系统的其他通知信息。</p>";//邮件内容 
		$mailbody = $mailbody ."<p>本邮件为系统自动生成，请勿回复。如果该申请非你本人要求，请忽略此邮件。</p>";//邮件内容 

		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件 

		$smtp = new \utils\SMTP();
		$smtp->smtp($this->smtpserver,$this->smtpserverport,true,
			    $this->smtpuser,$this->smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证. 
		//$smtp->debug = true;//是否显示发送的调试信息 
		$ret = $smtp->sendmail($smtpemailto, $this->smtpusermail, $mailsubject, $mailbody, $mailtype); 

	}


	function GenNewAuthkey()
	{
		$key='';
		//根据大家的使用反馈,剔出了经常写错的l和1,0和o四个选项
		$pattern = '23456789abcdef#$%&ghijkmnpqrstuvwxyz';    
		for($i=0;$i<8;$i++)
		{
			$key .= $pattern{mt_rand(0,35)};    //生成php随机数
		}
		return $key;
	}


	function ResetAuthKey($userName,$email)
	{
		$newAuthkey = $this->GenNewAuthkey();
		$duser = new \dao\DaoUser();
		$ret = $duser->resetUserPwd($userName,$newAuthkey,$email);
		if($ret)
		{
			$this->sendAuthkey($email,$newAuthkey);
			return "用户(".$userName.")新的临时密码已经发送到用户登记的电子邮箱，请注意查收。";
		}
		else
		{
			return "用户(".$userName.")密码重置操作失败，可能是因为用户名错误或者邮箱地址不匹配或两次重置时间小于12小时。";
		}
	}

	/**
	 * 通过邮件发送带附件的信息
	 */
	function sendMailWithAttachment($mailto,$subject="",$attachfiles=array())
	{
		$smtpemailto = $mailto;              //发送给谁 
		$mailsubject = "本地网传输及宽带规划支撑平台例行数据发送";//邮件主题 
		if(""!=$subject)
		{
			$mailsubject = $subject;
		}
		$mailsubject = "=?utf-8?B?".base64_encode($mailsubject)."?="; //邮件主题编码 
		$mailbody = "<h3>你好:</h3>";//邮件内容 
		$mailbody = $mailbody ."<p>本邮件是根据你在本地网传输规划平台的操作，为你自动发送的邮件。</p>";//邮件内容 
		$mailbody = $mailbody ."<p>请将本邮件地址加入白名单，以免漏掉系统的其他通知信息。</p>";//邮件内容 
		$mailbody = $mailbody ."<p>本邮件为系统自动生成，请勿回复。如果该申请非你本人要求，请忽略此邮件。</p>";//邮件内容 

		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件 

		$smtp = new \utils\SMTP();
		$smtp->smtp($this->smtpserver,$this->smtpserverport,true,
			    $this->smtpuser,$this->smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证. 

        //附件可以是数组形式
        if(is_array($attachfiles))
		{
			foreach($attachfiles as $afile)
			{
				$smtp->addAttachment($afile);
			}
		}
		else
		{
			$smtp->addAttachment($attachfiles);
		}
		
		//$smtp->debug = true;//是否显示发送的调试信息 
		$ret = $smtp->sendmail($smtpemailto, $this->smtpusermail, $mailsubject, $mailbody, $mailtype); 

	}

}
?>