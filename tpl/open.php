<?php
$user = GWF_Session::getUser();

$headers = array(
	array($lang->lang('th_user_name'), 'user.user_name'),
	array($lang->lang('th_relation'), 'frq_relation'),
	array($lang->lang('th_opened_at'), 'frq_opened_at'),
	array(), # actions
);

echo $page_menu;

echo GWF_Table::start();
echo GWF_Table::displayHeaders1($headers, $tVars['sort_url']);

while ($request = $table->fetch($requests, GDO::ARRAY_O))
{
	$request instanceof GWF_FriendRequest;
	
	$buttons = '';
	$buttons .= GWF_Button::generic($lang->lang('btn_deny'), $request->hrefDeny());
	$buttons .= GWF_Button::generic($lang->lang('btn_accept'), $request->hrefAccept());
	
	echo GWF_Table::rowStart();
	echo GWF_Table::column($request->displayUserName(), 'gwf-user-name');
	echo GWF_Table::column($request->displayRelation(), 'gwf-label');
	echo GWF_Table::column($request->displayDate(), 'gwf-date');
	echo GWF_Table::column($buttons, 'gwf-buttons');
	echo GWF_Table::rowEnd();
}
echo GWF_Table::end();
