<?php
final class Module_Friendlist extends GWF_Module
{
	##############
	### Module ###
	##############
	public function getVersion() { return 4.01; }
	public function getDefaultPriority() { return 20; }
	public function getDefaultAutoLoad() { return true; }
	public function getClasses() { return array('GWF_FriendRequest'); }
	public function onLoadLanguage() { return $this->loadLanguage('lang/friendlist'); }
	public function onInstall($dropTable) { require_once 'GWF_InstallFriendlist.php'; return GWF_InstallFriendlist::onInstall($this, $dropTable); }
	public function onCronjob() { require_once 'GWF_FriendlistCronjob.php'; GWF_FriendlistCronjob::onCronjob($this); }
	
	##############
	### Config ###
	##############
	public function cfgAcceptByMail() { return $this->getModuleVarBool('friendship_by_mail', '1'); }
	public function cfgNumInSidebar() { return $this->getModuleVarInt('friends_in_sidebar', '5'); }
	public function cfgGuestFriendships() { return $this->getModuleVarBool('guest_friendships', '0'); }
	public function cfgFriendsQuotaCount() { return $this->getModuleVarInt('friendship_friends_quota_count', '50'); }
	public function cfgRequestQuotaCount() { return $this->getModuleVarInt('friendship_request_quota_count', '3'); }
	public function cfgRequestQuotaTime() { return $this->getModuleVarDuration('friendship_request_quota_time', '86400'); }
	public function cfgCleanupTimeout() { return $this->getModuleVarDuration('friendship_cleanup_timeout', '259200'); }

	###############
	### Startup ###
	###############
	public function onStartup()
	{
		if ( (!Common::isCLI()) && (GWF_Session::hasSession()) )
		{
			$this->addJavascript('gwf-friendship.js');
		}
	}

	###############
	### Sidebar ###
	###############
	public function sidebarContent($bar)
	{
		if ($bar === 'right')
		{
			$this->onInclude();
			$this->onLoadLanguage();
			$user = GWF_User::getStaticOrGuest();
			$maxNumFriends = $this->cfgNumInSidebar();
			$tVars = array(
				'table' => GDO::table('GWF_Friendship'),
				'friends' => GWF_Friendship::getFriendsfor($user, $maxNumFriends, 0),
				'numOpen' => GWF_FriendRequest::countRequestsFor($user),
				'numFriends' => GWF_Friendship::countFriends($user),
				'maxNumFriends' => $maxNumFriends,
				'href_add' => GWF_WEB_ROOT.'friendship_request',
				'href_open' => GWF_WEB_ROOT.'friend_requests',
				'href_more' => GWF_WEB_ROOT.'friends',
			);
			return $this->template('friendlist-bar.php', $tVars);
		}
	}

}