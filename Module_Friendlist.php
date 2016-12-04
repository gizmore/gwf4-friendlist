<?php
final class Module_Friendlist extends GWF_Module
{
	##############
	### Module ###
	##############
	public function getVersion() { return 4.00; }
	public function getDefaultPriority() { return 20; }
	public function getDefaultAutoLoad() { return true; }
	public function getClasses() { return array('GWF_Friendship', 'GWF_FriendRequest'); }
	public function onLoadLanguage() { return $this->loadLanguage('lang/friendlist'); }
	public function onInstall($dropTable) { require_once 'GWF_InstallFriendlist.php'; return GWF_InstallFriendlist::onInstall($this, $dropTable); }
	public function onCronjob() { require_once 'GWF_FriendlistCronjob.php'; GWF_FriendlistCronjob::onCronjob($this); }
	
	##############
	### Config ###
	##############
	public function cfgAcceptByMail() { return $this->getModuleVarBool('friendship_by_mail', '1'); }
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
		$this->addJavascript('gwf-friendship.js');
	}

	###############
	### Sidebar ###
	###############
	public function sidebarContent($bar)
	{
		if ($bar === 'right')
		{
			$this->onLoadLanguage();
			$user = GWF_User::getStaticOrGuest();
			$tVars = array(
				'href_more' => GWF_WEB_ROOT.'friends',
// 				'friends' => GWF_Friends::forUser($user),
			);
			return $this->template('friendlist-bar.php', $tVars);
		}
	}

}