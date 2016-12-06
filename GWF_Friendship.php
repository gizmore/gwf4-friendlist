<?php
final class GWF_Friendship extends GDO
{
	public static $RELATIONS = array(
		'seen', 'friend', 'coworker', 'boss',
		'wife', 'husband',
		'mother', 'father', 'brother', 'sister', 'grandma', 'grandpa', 'son', 'daughter',
		'aunt', 'uncle',
	);
	
	###########
	### GDO ###
	###########
	public function getTableName() { return GWF_TABLE_PREFIX.'friendship'; }
	public function getClassName() { return __CLASS__; }
	public function getColumnDefines()
	{
		return array(
			'fr_id' => array(GDO::AUTO_INCREMENT),
			'fr_user_id' => array(GDO::UINT|GDO::INDEX, GDO::NOT_NULL),
			'fr_friend_id' => array(GDO::UINT, GDO::NOT_NULL),
			'fr_relation' => array(GDO::ENUM, 'seen', self::$RELATIONS),
			'fr_since' => array(GDO::DATE, GDO::NOT_NULL, GWF_Date::LEN_SECOND),
			'fr_saved_at' => array(GDO::DATE, GDO::NOT_NULL, GWF_Date::LEN_SECOND),
				
			'user' => array(GDO::JOIN, GDO::NULL, array('GWF_User', 'user_id', 'fr_user_id')),
			'friend' => array(GDO::JOIN, GDO::NULL, array('GWF_User', 'user_id', 'fr_friend_id')),
		);
	}
	
	##############
	### Static ###
	##############
	public static function countFriends(GWF_User $user)
	{
		$where = sprintf("fr_user_id=%s AND fr_relation!='seen'", $user->getID());
		return self::table(__CLASS__)->countRows($where);
	}
	
	public static function areFriends(GWF_User $user, GWF_User $friend)
	{
		$relation = self::getRelationBetween($user, $friend);
		return ($relation !== false) && ($relation !== 'seen');
	}
	
	public static function areBuddies(GWF_User $user, GWF_User $friend)
	{
		return self::getRelationBetween($user, $friend) !== false;
	}
	
	public static function getRelationBetween(GWF_User $user, GWF_User $friend)
	{
		$where = sprintf("fr_user_id=%s AND fr_friend_id=%s", $user->getID(), $friend->getID());
		return self::table(__CLASS__)->selectVar('fr_relation', $where);
	}
	
	public static function getFriendshipFor(GWF_User $user, GWF_User $friend)
	{
		$where = sprintf("fr_user_id=%s AND fr_friend_id=%s", $user->getID(), $friend->getID());
		return self::table(__CLASS__)->selectFirstObject('*', $where);
	}
	
	public static function getFriendsFor(GWF_User $user, $limit=-1, $from=-1)
	{
		$fields = '*, friend.*';
		$joins = array('friend');
		$where = sprintf("fr_user_id=%s", $user->getID());
		$orderby = 'fr_since ASC';
		return self::table(__CLASS__)->select($fields, $where, $orderby, $joins, $limit, $from);
	}
	
	##############
	### Getter ###
	##############
	public function getID() { return $this->getVar('fr_id'); }
	public function getUserID() { return $this->getVar('fr_user_id'); }
	public function getFriendID() { return $this->getVar('fr_friend_id'); }
	public function getRelation() { return $this->getVar('fr_relation'); }
	public function getSince() { return $this->getVar('fr_since'); }
	public function getSavedAt() { return $this->getVar('fr_saved_at'); }
	
	############
	### HREF ###
	############
	public function hrefProfile() { return GWF_WEB_ROOT.'profile/'.$this->getVar('user_name'); }
	
	###############
	### Display ###
	###############
	public function displayFriendName()
	{
		$guestName = $this->getVar('user_guest_name');
		return $guestName ? $guestName : $this->getVar('user_name');
	}
	
	public function displayFriendProfileLink()
	{
		return GWF_HTML::anchor($this->hrefProfile(), $this->displayFriendName());
	}
	
	public function displayRelation()
	{
		return $this->getRelation();
	}
	
	public function displayDate()
	{
		return GWF_Time::displayDate($this->getSince());
	}
	
}
