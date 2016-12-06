<div ng-controller="FriendshipCtrl">

	<h1>Friends</h1>
	
	<?php
	if ($numFriends > 0)
	{
		echo "<md-list>\n";
		while ($friendship = $table->fetch($friends, GDO::ARRAY_O))
		{
			$friendship instanceof GWF_Friendship;
			$profileLink = $friendship->displayFriendProfileLink();
			printf("<md-list-item>%s</md-list-item>\n", $profileLink);
		}
		echo "</md-list>\n";
	}
	?>
	
	<?php if ($numFriends > $maxNumFriends) { ?>
		<md-button href="<?php echo $href_more; ?>"><?php echo $lang->lang('btn_more'); ?></md-button>
	<?php } else { ?>
		<md-button href="<?php echo $href_add; ?>"><?php echo $lang->lang('btn_request_general'); ?></md-button>
	<?php } ?>
	
	<md-button ng-if="<?php echo $numOpen; ?> > 0" href="<?php echo $href_open; ?>"><?php echo $lang->lang('btn_num_open_requests', array($numOpen)); ?></md-button>

</div>
