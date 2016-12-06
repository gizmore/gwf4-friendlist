<?php
$lang = array(
'module_name' => 'Friendlist',	

##############
### Config ###
##############
'cfg_guest_friendships' => 'Allow guest friendships?',
'cfg_friendship_friends_quota_count' => 'Max number of friends',
'cfg_friendship_request_quota_count' => 'Max number of pending requests',
'cfg_friendship_cleanup_timeout' => 'Max cleanup age',
'cfg_friendship_request_quota_time' => 'Max request age',
		
###############
### Sidebar ###
###############
'btn_more' => 'more…',
'btn_num_open_requests' => '%d Friendship request(s)',
		
################
### Overview ###
################
'th_user_name' => 'User',
'th_relation' => 'Relation',
'th_opened_at' => 'Date',
'th_since' => 'Since',
'btn_request_general' => 'Add a friend',
'btn_open_requests' => 'Friendship requests',
'btn_accept' => 'Accept',
'btn_deny' => 'Deny',
'btn_quit' => 'Quit Friendship',

###############
### Request ###
###############
'btn_request' => 'Become Friends',
'ft_request' => 'Request friendship',
'th_buddy' => 'Username',
'err_request_quota_exceeded' => 'You have more than %s pending friendship requests within the last %s.',
'err_friends_quota_exceeded' => 'You have reached the limit of %s friends.',
'err_already_friends' => 'You and %s are already friends.',
'err_self_friendship' => 'You cannot be friend with yourself here.',
'err_only_real_users' => 'You can only be friend with human users.',
'err_already_requesting' => 'You already have a pending request to %s.',
'err_already_requested' => 'There is already a pending request from %s.',
'msg_requested' => 'Your friendship has been requested.',
'request_mail_subject' => GWF_SITENAME.': Friendship Request',
'request_mail_body' =>
	'Dear %3$s'.PHP_EOL.
	''.PHP_EOL.
	'%2$s requested to become friends with you on %7$s'.PHP_EOL.
	''.PHP_EOL.
	'To view their profile, visit %4$s'.PHP_EOL.
	''.PHP_EOL.
	'To accept the request instantly, visit %5$s.'.PHP_EOL.
	''.PHP_EOL.
	''.PHP_EOL.
	''.PHP_EOL.
	'Please report abuse by answering to this mail or writing to %6$s.'.PHP_EOL.
	''.PHP_EOL.
	''.PHP_EOL.
	'Sincerly,'.PHP_EOL.
	'The %1$s Team'.PHP_EOL,

##############
### Accept ###
##############
'msg_accepted' => 'You and %s are now friends.',
'accept_mail_subject' => GWF_SITENAME.': Friendship accepted',
'accept_mail_body' =>
'Dear %2$s'.PHP_EOL.
''.PHP_EOL.
'You and %3$s are now friends on %4$s.'.PHP_EOL.
''.PHP_EOL.
'To view their profile, visit %5$s'.PHP_EOL.
''.PHP_EOL.
''.PHP_EOL.
'Sincerly,'.PHP_EOL.
'The %1$s Team'.PHP_EOL,

##############
### Accept ###
##############
'msg_denied' => 'You have denied the friendship request from %s.',
'deny_mail_subject' => GWF_SITENAME.': Friendship request denied',
'deny_mail_body' =>
'Dear %2$s'.PHP_EOL.
''.PHP_EOL.
'%3$s has denied your friendship request on %4$s.'.PHP_EOL.
''.PHP_EOL.
'To view their profile, visit %5$s'.PHP_EOL.
''.PHP_EOL.
''.PHP_EOL.
'Sincerly,'.PHP_EOL.
'The %1$s Team'.PHP_EOL,
		
##############
### Cancel ###
##############
'ft_cancel' => 'Quit friendship with %s',
'err_not_friends' => 'You and % are not friends on %s.',
'msg_friendship_canceled' => 'You and % are not friends on %s anymore.',
'cancel_mail_subject' => GWF_SITENAME.': Friendship cancelled',
'cancel_mail_body' =>
'Dear %2$s'.PHP_EOL.
''.PHP_EOL.
'%3$s has cancelled the friendship on %4$s with you.'.PHP_EOL.
''.PHP_EOL.
'To view their profile, visit %5$s'.PHP_EOL.
''.PHP_EOL.
''.PHP_EOL.
'Sincerly,'.PHP_EOL.
'The %1$s Team'.PHP_EOL,
);
