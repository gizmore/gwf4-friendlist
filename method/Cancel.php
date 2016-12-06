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
		return 'RewriteRule ^quit_friendship/([^/]+)/?$ index.php?mo=Friendlist&me=Cancel&username=$1 [QSA]'.PHP_EOL;
	}
	
	public function execute()
	{
		$this->user = GWF_User::getStaticOrGuest();
		
		if (!$this->user->isUser())
		{
			return GWF_HTML::err('ERR_NO_PERMISSION');
		}
		
		if (false === ($this->friend = GWF_User::getByName(Common::getRequestString('username'))))
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
		$data['are_friends'] = array(GWF_Form::VALIDATOR);
		$data['cancel'] = array(GWF_Form::SUBMIT, $this->module->lang('btn_quit'));
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
			'form' => $form->templateY($this->module->lang('ft_cancel', array($this->friend->displayName()))),	
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
		
		if ($this->module->cfgAcceptByMail())
		{
			$this->sendMail($this->user, $this->friend, $this->friendship);
		}
		
		return $this->module->message('msg_friendship_canceled', array($this->friend->displayName(), GWF_SITENAME));
	}
	
	private function cancelFriendship(GWF_User $user, GWF_User $friend, GWF_Friendship $friendship)
	{
		return $friendship->delete();
	}
	
	############
	### Mail ###
	############
	private function sendMail(GWF_User $user, GWF_User $friend, GWF_Friendship $friendship)
	{
		if ($email = ($friend->getValidMail()))
		{
			$this->sendMailB($user, $friend, $email, $friendship);
		}
	}
	
	private function sendMailB(GWF_User $user, GWF_User $friend, $email, GWF_Friendship $friendship)
	{
		$linkSite = Common::getAbsoluteURL('');
		$linkSite = GWF_HTML::anchor($linkSite, $linkSite);
		$linkProfile = Common::getAbsoluteURL('profile/'.$this->user->getName());
		$linkProfile = GWF_HTML::anchor($linkProfile, $linkProfile);
		$args = array(
			GWF_SITENAME,
			$friend->displayName(),
			$user->displayName(),
			$linkSite,
			$linkProfile,
		);
		$mail = new GWF_Mail();
		$body  = $this->module->lang('cancel_mail_body', $args);
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setSubject($this->module->lang('cancel_mail_subject'));
		$mail->setBody($body);
		$mail->sendToUser($this->friend);
	}
}
