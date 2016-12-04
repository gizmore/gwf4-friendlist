<?php
final class GWF_FriendlistCronjob extends GWF_Cronjob
{
	public static function onCronjob(Module_Friendlist $module)
	{
		self::start('Friendlist');
		self::deleteOldRequests($module);
		self::end('Friendlist');
	}

	private static function deleteOldRequests(Module_Friendlist $module)
	{
		$cut = time() - $module->cfgCleanupTimeout();

		self::log('Deleting friendship requests older than '.GWF_Time::displayTimestamp($cut));
		
		$table = GDO::table('GWF_FriendRequest');
		$result = $table->deleteWhere("frq_opened_at<$cut");

		if (0 < ($nDeleted = $table->affectedRows($result)))
		{
			self::log(sprintf('Deleted %d old friendship requests.', $nDeleted));
		}
	}
}
