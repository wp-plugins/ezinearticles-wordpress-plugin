
<div class="wrap">
<h2><?php echo WP_EA?> - Account</h2>

<?php

	if(isset($ea_account_status) && $ea_account_status!=null)
	{

		$ea_account_status_zero = $ea_account_status[0];

		?>
		<form action="" method="post">
		<h3><?php _e('Your EzineArticles Account Status', 'ea') ?></h3>
		<table width="100%" cellpadding="2" cellspacing="5" class="editform">
		<tr>
			<td align="left" valign="top" width="25%" nowrap><?php _e('Author Names:', 'ea')?></td>
			<td><?php getAuthorSelect();?></td>
		</tr>
			<tr>
			<td align="left" valign="top" width="25%" nowrap><?php _e('Membership Level:', 'ea')?></td>
			<td><?php echo $ea_account_status_zero['membership_level']; ?></td>
		</tr>
		<?php if($ea_account_status_zero['membership_status'] == 'Premium') { ?>
		<tr>
			<td align="left" valign="top" widtd="25%" nowrap><?php _e('Membership Status:', 'ea')?></td>
			<td><span class="premium">$ Premium Member</span></td>
		</tr>
		<?php }?>
		<tr>
			<td align="left" valign="top" widtd="25%" nowrap><?php _e('Submissions Left:', 'ea')?></td>
			<td><?php echo $ea_account_status_zero['submissions_left']; ?></td>
		</tr>
		</table>
			<div class="submit">
			<input type="submit" name="refresh_account_status" value="<?php _e('Refresh account status', 'ea')?>">
		</div>

		<?php
	}
	else
	{
		?>
		<p><b>Could not connect to EzineArticles.com</b></p>
		<p>Please verify your EzineArticles API Key, Username and Password are correct.</p>
		<p><b><a href="admin.php?page=wp_ezinearticles_options">Go to Options</a></b></p>
		<?php
	}

?>
</div>