<?php
final class Friendlist_Request extends GWF_Method
{
	private $user;
	private $to;
	
	public function isLoginRequired()
	{
		return !$this->module->cfgGuestFriendships();
	}

	public function getHTAccess()
	{
		return 'RewriteRule ^friendship_request$ index.php?mo=Friendlist&me=Request [QSA]'.PHP_EOL;
	}

	public function execute()
	{
		$this->user = GWF_User::getStaticOrGuest();
		
		if (isset($_POST['request']))
		{
			return $this->onRequest();
		}
		
		return $this->templateRequest();
	}

	public function form()
	{
		$data = array(
			'to' => array(GWF_Form::STRING, Common::getGetString('to'), $this->module->lang('th_buddy')),
			'quota' => array(GWF_Form::VALIDATOR),
			'friend' => array(GWF_Form::VALIDATOR),
			'not_friend_yet' => array(GWF_Form::VALIDATOR),
			'request' => array(GWF_Form::SUBMIT, $this->module->lang('btn_request')),
		);
		return new GWF_Form($this, $data);
	}
	
	public function templateRequest()
	{
		$form = $this->form();
		$tVars = array(
			'form' => $form->templateY($this->module->lang('ft_request')),
			'href_overview' => GWF_WEB_ROOT.'friends',
		);
		return $this->module->template('request.php', $tVars);
	}

	public function validate_to(Module_Friendlist $m, $arg)
	{
		if (false === ($this->to = GWF_User::getByName($arg)))
		{
			return GWF_HTML::lang('ERR_UNKNOWN_USER');
		}
		return false;
	}
	
	public function validate_quota(Module_Friendlist $m, $arg)
	{
		$quotaTime = $m->cfgRequestQuotaTime();
		$quotaCount = $m->cfgRequestQuotaCount();
		if ($quotaCount > 0)
		{
			$requests = GWF_FriendRequest::countRequestsFrom($this->user, $quotaCount);
			if ($requests >= $quotaCount)
			{
				return $m->lang('err_request_quota_exceeded', array($quotaCount, GWF_Time::humanDuration($quotaTime)));
			}
		}
		
		$quotaCount = $m->cfgFriendsQuotaCount();
		if ($quotaCount > 0)
		{
			$friends = GWF_Friendship::countFriends($this->user);
			if ($friends >= $quotaCount)
			{
				return $m->lang('err_friends_quota_exceeded', array($quotaCount));
			}
		}
		
		return false;
	}
	
	public function validate_friend(Module_Friendlist $m, $arg)
	{
		if ($this->to)
		{
			if ($this->to->getID() == $this->user->getID())
			{
				return $m->lang('err_self_friendship');
			}
			if (!$this->to->isUser())
			{
				return $m->lang('err_only_real_users');
			}
		}
		return false;
	}
	
	public function validate_not_friend_yet(Module_Friendlist $m, $arg)
	{
		if ($this->to)
		{
			if (GWF_Friendship::areFriends($this->user, $this->to))
			{
				return $this->module->lang('err_already_friends', array($this->to->displayName()));
			}
			if (GWF_FriendRequest::hasPendingRequest($this->user, $this->to))
			{
				return $this->module->lang('err_already_requesting', array($this->to->displayName()));
			}
			if (GWF_FriendRequest::hasPendingRequest($this->to, $this->user))
			{
				return $this->module->lang('err_already_requested', array($this->to->displayName()));
			}
		}
		return false;
	}
	
	public function onRequest()
	{
		$withMail = $this->module->cfgAcceptByMail();
		$form = $this->form();
		if (false !== ($error = $form->validate($this->module)))
		{
			return $error.$this->templateRequest();
		}
		
		$request = new GWF_FriendRequest(array(
			'frq_id' => '0',
			'frq_user_id' => $this->user->getID(),
			'frq_relation' => 'friend',
			'frq_friend_id' => $this->to->getID(),
			'frq_token' => null,
			'frq_opened_at' => GWF_Time::getDate(),
			'frq_state' => 'open',
			'frq_closed_at' => null,
		));
		
		if (!$request->insert())
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		if (!$request->generateToken())
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		if ($withMail)
		{
			$this->sendMail($request);
		}
		
		return $this->module->message('msg_requested');
	}
	
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
		$linkAccept = Common::getAbsoluteURL('accept_friendship/'.$request->getToken());
		$linkAccept = GWF_HTML::anchor($linkAccept, $linkAccept);
		$linkAbuse = sprintf('mailto:%s', GWF_SUPPORT_EMAIL);
		$linkAbuse = GWF_HTML::anchor($linkAbuse, $linkAbuse);
		$linkSite = Common::getAbsoluteURL('');
		$linkSite = GWF_HTML::anchor($linkSite, $linkSite);
		$args = array(
			GWF_SITENAME,
			$this->user->displayName(),
			$this->to->displayName(),
			$linkProfile,
			$linkAccept,
			$linkAbuse,
			$linkSite,
		);
		$body  = $this->module->lang('request_mail_body', $args);
		$mail->setBody($body);
		$mail->sendToUser($this->to);
	}
	
}
