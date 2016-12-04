<?php
final class Friendlist_Accept extends GWF_Method
{
	private $user;
	private $friend;
	private $request;

	private $allowGuests;
	
	public function getHTAccess()
	{
		return
			'RewriteRule ^friendship_accept/?$ index.php?mo=Friendlist&me=Accept [QSA]'.PHP_EOL.
			'RewriteRule ^friendship_accept/([^/]+)/?$ index.php?mo=Friendlist&me=Accept&token=$1 [QSA]'.PHP_EOL;
	}
	
	public function execute()
	{
		$this->friend = GWF_User::getStaticOrGuest();
		$this->allowGuests = $this->module->cfgGuestFriendships();
		
		if ( (!$this->friend->isUser()) || ((!$this->allowGuests)&&($this->friend->isGuest())) )
		{
			return GWF_HTML::err('ERR_PERMISSION');
		}
		
		# Get request either way
		if ($token = Common::getRequestString('token'))
		{
			$this->request = GWF_FriendRequest::getByToken($token);
		}
		else if ($id = Common::getRequestString('id'))
		{
			$this->request = GWF_FriendRequest::getByID($id);
		}

		# Check ownage of target
		if ( (!$this->request) || ($this->request->getFriendID() !== $this->friend->getID()) )
		{
			return $this->module->error('err_request');
		}
		
		# Check existance of source
		if (!($this->user = GWF_User::getByID($this->request->getUserID())))
		{
			return GWF_HTML::err('ERR_UNKNOWN_USER');
		}
		
		# Insert two friends
		if (!$this->insertFriendships($this->user, $this->friend, $this->request))
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		# Send mails
		if ($this->module->cfgEmail())
		{
			$this->sendMail($this->user, $this->friend, $this->request);
		}
		
		return $this->module->message('msg_accepted', array($this->user->displayName()));
	}
	
	public function insertFriendships(GWF_User $user, GWF_User $friend, GWF_FriendRequest $request)
	{
		$now = GWF_Time::getDate();
		$a = new GWF_Friendship(array(
			'fr_id' => '0',
			'fr_user_id' => $user->getID(),
			'fr_friend_id' => $friend->getID(),
			'fr_relation' => $request->getRelation(),
			'fr_since' => $now,
			'fr_saved_at' => $now,
		));
		$b = new GWF_Friendship(array(
			'fr_id' => '0',
			'fr_user_id' => $friend->getID(),
			'fr_friend_id' => $user->getID(),
			'fr_relation' => 'friend',
			'fr_since' => $now,
			'fr_saved_at' => $now,
		));
		return $a->insert() && $b->insert();
	}
	
	private function sendMail(GWF_User $user, GWF_User $friend, GWF_FriendRequest $request)
	{
		if ($email = ($user->getValidMail()))
		{
			$this->sendMailB($user, $email, $friend, $request);
		}
	}
	
	private function sendMailB(GWF_User $user, $email, GWF_User $friend, GWF_FriendRequest $request)
	{
		$mail = new GWF_Mail();
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setSubject($this->module->lang('accept_mail_subject'));
		$linkSite = Common::getAbsoluteURL('');
		$linkSite = GWF_HTML::anchor($linkSite, $linkSite);
		$linkProfile = Common::getAbsoluteURL('profile/'.$friend->getName());
		$linkProfile = GWF_HTML::anchor($linkProfile, $linkProfile);
		$args = array(
			GWF_SITENAME,
			$user->displayName(),
			$friend->displayName(),
			$linkSite,
			$linkProfile,
		);
		$body  = $this->module->lang('accept_mail_body', $args);
		$mail->setBody($body);
		$mail->sendToUser($user);
	}
	
}
