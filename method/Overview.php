<?php
final class Friendlist_Overview extends GWF_Method
{
	private $ipp = 20;
	private $nItems = 0;
	private $nPages = 1;
	private $page = 1;
	private $orderby = 'friend.user_name ASC';
	private $online = false;
	private $friends = null;
	
	public function isLoginRequired()
	{
		return !$this->module->cfgGuestFriendships();
	}
	
	public function getHTAccess()
	{
		return 'RewriteRule ^friends$ index.php?mo=Friendlist&me=Overview [QSA]'.PHP_EOL;
	}

	public function execute()
	{
		if (false !== ($error = $this->sanitize()))
		{
			return $error;
		}
		return $this->templateOverview();
	}
	
	private function sanitize()
	{
		$user = GWF_User::getStaticOrGuest();
		$uid = $user->getID();
		
		$this->online = Common::getRequestString('online', '0') === '1';

		$conditions = "fr_user_id=$uid";
		if ($this->online)
		{
			$cut = time() - GWF_ONLINE_TIMEOUT;
			$conditions .= " AND friend.user_lastactivity > $cut";
		}
		
		$table = GDO::table('GWF_Friendship');
		$this->orderby = $table->getMultiOrderby(Common::getGet('by'), Common::getGet('dir'));
		$this->nItems = $table->countRows($conditions);
		$this->nPages = GWF_PageMenu::getPagecount($this->ipp, $this->nItems);
		$this->page = Common::clamp(intval(Common::getGet('page')), 1, $this->nPages);
		$from = GWF_PageMenu::getFrom($this->page, $this->ipp);
		$this->friends = $table->select('*, friend.user_name, friend.user_guest_name', $conditions, $this->orderby, array('friend'), $this->ipp, $from);
		return false;
	}
	
	
	public function templateOverview()
	{
		$hrefPage = GWF_WEB_ROOT.sprintf('friends?by=%s&dir=%s&page=%%PAGE%%', urlencode(Common::getGet('by')), urlencode(Common::getGet('dir')));
		$hrefSort = GWF_WEB_ROOT.'friends?by=%BY%&dir=%DIR%&page=1';
		$tVars = array(
			'table' => GDO::table('GWF_Friendship'),
			'friends' => $this->friends,
			'sort_url' => $hrefSort,
			'page_menu' => GWF_PageMenu::display($this->page, $this->nPages, $hrefPage),
			'href_request' => GWF_WEB_ROOT.'friendship_request',
			'href_open_requests' => GWF_WEB_ROOT.'friend_requests',
		);
		return $this->module->template('overview.php', $tVars);
	}

}
