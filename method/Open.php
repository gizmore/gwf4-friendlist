<?php
final class Friendlist_Open extends GWF_Method
{
	private $ipp = 20;
	private $nItems = 0;
	private $nPages = 1;
	private $page = 1;
	private $orderby = 'friend.user_name ASC';
	private $online = false;
	private $requests = null;

	public function isLoginRequired()
	{
		return !$this->module->cfgGuestFriendships();
	}

	public function getHTAccess()
	{
		return 'RewriteRule ^friendrequests$ index.php?mo=Friendlist&me=Open [QSA]'.PHP_EOL;
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
		$conditions = "frq_friend_id=$uid AND frq_state='open'";
		$table = GDO::table('GWF_FriendRequest');
		$this->orderby = $table->getMultiOrderby(Common::getGet('by'), Common::getGet('dir'));
		$this->nItems = $table->countRows($conditions);
		$this->nPages = GWF_PageMenu::getPagecount($this->ipp, $this->nItems);
		$this->page = Common::clamp(intval(Common::getGet('page')), 1, $this->nPages);
		$this->requests = $table->select('*, user.user_name, user.user_guest_name', $conditions, $this->orderby, array('user'), $this->ipp, GWF_PageMenu::getFrom($this->page, $this->ipp));
		return false;
	}


	public function templateOverview()
	{
		$hrefPage = GWF_WEB_ROOT.sprintf('friendrequests?by=%s&dir=%s&page=%%PAGE%%', urlencode(Common::getGet('by')), urlencode(Common::getGet('dir')));
		$hrefSort = GWF_WEB_ROOT.'friendrequests?by=%BY%&dir=%DIR%&page=1';
		$tVars = array(
			'table' => GDO::table('GWF_FriendRequest'),
			'requests' => $this->requests,
			'sort_url' => $hrefSort,
			'page_menu' => GWF_PageMenu::display($this->page, $this->nPages, $hrefPage),
			'href_request' => GWF_WEB_ROOT.'friendship_request',
		);
		return $this->module->template('open.php', $tVars);
	}

}
