<?php
final class Friendlist_Cancel extends GWF_Method
{
	private $user;
	private $friend;
	private $friendship;
	
	public function isLoginRequired()
	{
		return !$this->module->cfgGuestFriendships();
	}
	
	public function getHTAccess()
	{
		return 'RewriteRule ^cancel_friendship/([^/]+)/?$ index.php?mo=Friendlist&me=Cance&username=$1 [QSA]'.PHP_EOL;
	}
	
	public function execute()
	{
		$this->user = GWF_User::getStaticOrGuest();
		
		if ($this->user->isUest())
		
		if (false === ($this->friend = GWF_User::getByName(Common::getRequestString('usename'))))
		{
			return GWF_HTML::err('ERR_UNKNOWN_USER');
		}
		
		if (isset($_POST['cancel']))
		{
			return $this->onCancel();
		}
		
		return $this->templateCancel();
	}
	
	############
	### Form ###
	############
	public function form()
	{
		$data = array();
		$data['username'] = array(GWF_Form::SSTRING, $this->friend->displayName(), $this->module->lang('th_user_name'));
		$date['are_friends'] = array(GWF_Form::VALIDATOR);
		$data['cancel'] = array(GWF_Form::SUBMIT, $this->module->lang('btn_cancel'));
		return new GWF_Form($this, $data);
	}
	
	public function validate_are_friends(Module_Friendlist $m)
	{
		if (!($this->friendship = GWF_Friendship::getFriendshipFor($this->user, $this->friend)))
		{
			return $m->lang('err_not_friends', array($this->friend->displayName()));
		}
		return false;
	}
	
	################
	### Template ###
	################
	public function templateCancel()
	{
		$form = $this->form();
		$tVars = array(
			'form' => $form->templateY($this->module->lang('ft_cancel')),	
		);
		return $this->module->template('cancel.php', $tVars);
	}
	
	##############
	### Action ###
	##############
	public function onCancel()
	{
		$form = $this->form();
		if (false !== ($error = $form->validate($this->module)))
		{
			return $error . $this->templateCancel();
		}
		
		if (!$this->cancelFriendship($this->user, $this->friend, $this->friendship))
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		$withEmail = $this->module->cfgABC();
		if ($withEmail)
		{
			$this->sendMail($this->user, $this->friend, $this->friendship);
		}
		
		return $this->module->message('msg_canceled', array($this->friend->displayName()));
	}
	
	private function cancelFriendship(GWF_User $user, GWF_User $friend, GWF_Friendship $friendship)
	{
		return $friendship->delete();
	}
	
	############
	### Mail ###
	############
	private function sendMail(GWF_FriendRequest $request)
	{
		if ($email = ($this->to->getValidMail()))
		{
			$this->sendMailB($request, $email);
		}
	}
	
	private function sendMailB(GWF_FriendRequest $request, $email)
	{
		$mail = new GWF_Mail();
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setSubject($this->module->lang('request_mail_subject'));
		$linkProfile = Common::getAbsoluteURL('profile/'.$this->user->getName());
		$linkProfile = GWF_HTML::anchor($linkProfile, $linkProfile);
		$linkAccept = Common::getAbsoluteURL('frindrequest_accept/'.$request->getToken());
		$linkAccept = GWF_HTML::anchor($linkAccept, $linkAccept);
		$linkAbuse = sprintf('mailto:%s', GWF_SUPPORT_EMAIL);
		$linkAbuse = GWF_HTML::anchor($linkAbuse, $linkAbuse);
		$args = array(
				GWF_SITENAME,
				$this->user->displayName(),
				$this->to->displayName(),
				$linkProfile,
				$linkAccept,
				$linkAbuse,
		);
		$body  = $this->module->lang('request_mail_body', $args);
		$mail->setBody($body);
		$mail->sendToUser($this->to);
	}
	
	
}