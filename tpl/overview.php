<?php
$user = GWF_Session::getUser();

$headers = array(
		array($lang->lang('th_user_name'), 'friend.user_name'),
		array($lang->lang('th_relation'), 'fr_relation'),
		array($lang->lang('th_since'), 'fr_since'),
		array(), # actions
);

echo $page_menu;

echo GWF_Table::start();
echo GWF_Table::displayHeaders1($headers, $tVars['sort_url']);

while ($friendship = $table->fetch($friends, GDO::ARRAY_O))
{
	$friendship instanceof GWF_Friendship;
	
	$buttons = '';
	$buttons .= GWF_Button::generic($lang->lang('btn_quit'), $friendship->hrefCancel());

	echo GWF_Table::rowStart();
	echo GWF_Table::column($friendship->displayFriendProfileLink(), 'gwf-user-name');
	echo GWF_Table::column($friendship->displayRelation(), 'gwf-label');
	echo GWF_Table::column($friendship->displayDate(), 'gwf-date');
	echo GWF_Table::column($buttons, 'gwf-buttons');
	echo GWF_Table::rowEnd();
}
echo GWF_Table::end();

echo GWF_Button::add($lang->lang('btn_request_general'), $href_request);
echo GWF_Button::generic($lang->lang('btn_open_requests'), $href_open_requests);
