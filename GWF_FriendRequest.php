<?php
final class GWF_FriendRequest extends GDO
{
	public static $STATES = array('open', 'reverted', 'accepted', 'denied');
	
	###########
	### GDO ###
	###########
	public function getTableName() { return GWF_TABLE_PREFIX.'friend_request'; }
	public function getClassName() { return __CLASS__; }
	public function getColumnDefines()
	{
		return array(
			'frq_id' => array(GDO::AUTO_INCREMENT),
			'frq_user_id' => array(GDO::UINT|GDO::INDEX, GDO::NOT_NULL),
			'frq_relation' => array(GDO::ENUM, 'seen', GWF_Friendship::$RELATIONS),
			'frq_friend_id' => array(GDO::UINT|GDO::INDEX, GDO::NOT_NULL),
			'frq_token' => array(GDO::VARCHAR|GDO::ASCII|GDO::CASE_S, GDO::NULL, 16),
			'frq_opened_at' => array(GDO::DATE, GDO::NOT_NULL, GWF_Date::LEN_SECOND),
			'frq_state' => array(GDO::ENUM, 'open', self::$STATES),
			'frq_closed_at' => array(GDO::DATE, GDO::NULL, GWF_Date::LEN_SECOND),

			'user' => array(GDO::JOIN, GDO::NULL, array('GWF_User', 'user_id', 'frq_user_id')),
			'friend' => array(GDO::JOIN, GDO::NULL, array('GWF_User', 'user_id', 'frq_friend_id')),
		);
	}
	
	###############
	### Getters ###
	###############
	public function getToken() { return $this->getVar('frq_token'); }
	
	#############
	### Token ###
	#############
	public function generateToken()
	{
		$newToken = $this->getID().'-'.GWF_Random::randomKey(8);
		return $this->saveVar('frq_token', $newToken);
	}
	
	public function checkToken($token)
	{
		return $this->getToken() === $token;
	}
	
	##############
	### Static ###
	##############
	public static function getByID($id)
	{
		return self::table(__CLASS__)->selectFirstObject('*', 'frq_id='.intval($id));
	}
	
	public static function getByToken($token)
	{
		return self::getByID(self::getIDFromToken($token));
	}
	
	public static function getIDFromToken($token)
	{
		return Common::substrUntil($token, '-');
	}
	
	public static function countRequestsFrom(GWF_User $user, $quotaTime=0)
	{
		$uid = $user->getID();
		$where = "frq_user_id=$uid AND frq_state='open'";
		if ($quotaTime > 0)
		{
			$cut = GWF_Time::getDate(GWF_Date::LEN_SECOND, time()-$quotaTime);
			$where .= " AND frq_opened_at>='$cut'";
		}
		return self::table(__CLASS__)->countRows($where);
	}
	
}
