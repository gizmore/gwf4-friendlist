<?php
final class GWF_InstallFriendlist
{
	public static function onInstall(Module_Friendlist $module, $dropTables)
	{
		return GWF_ModuleLoader::installVars($module, array(
			'friendship_by_mail' => array('1', 'bool'),
			'friends_in_sidebar' => array('5', 'int', 0, 100),
			'guest_friendships' => array('0', 'bool'),
			'friendship_friends_quota_count' => array('50', 'int', 0, 10000),
			'friendship_request_quota_count' => array('3', 'int', 0, 100),
			'friendship_request_quota_time' => array('86400', 'time', 0, 100000),
			'friendship_cleanup_timeout' => array('86400', 'time', 0, 100000),
		));
	}
}
