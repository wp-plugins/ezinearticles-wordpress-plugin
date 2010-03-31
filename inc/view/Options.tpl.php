<div class="wrap">
	<h2><?php echo WP_EA?> - Options</h2>

	<form method="post">

		<h3><?php _e('General settings', 'ea') ?></h3>
		<table width="100%" cellpadding="2" cellspacing="5" class="editform">
			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles API Key:', 'ea')?></td>
				<td><input type="text" name="ea_api_key" id="ea_api_key" size="55" value="<?php echo $ea_api_key?>" tabindex="1">
				<small>
				<br /> In order to use Publish on EzineArticles plugin you must have
				<br />your unique EzineArticles API Key. <a target="_blank" href="http://api.ezinearticles.com/?get_api_key">Click Here</a> if you do not have one yet.
				<br />If you have forgotten your key. you can retreive it from the members area.
				<br /><a target="_blank" href="http://members.ezinearticles.com/tools/wordpress">Click Here</a> to login. Click 'Author Tools' and 'WordPress Plugin'.
				</small>
				</td>
			</tr>

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles Username:', 'ea')?></td>
				<td><input type="text" name="ea_email" id="ea_email" size="30" value="<?php echo $ea_email?>" tabindex="2">
				<small>
				<br />Enter your EzineArticles Member's Username(Email). If you are not yet <br />a member of EzineArticles,
				<a target="_blank" href="http://ezinearticles.com/submit/">Click Here</a> for your FREE  Basic Membership Account.
				</small>
				</td>
			</tr>

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles Password:', 'ea')?></td>
				<td><input type="password" name="ea_password" id="ea_password" size="30" value="<?php echo $ea_password?>" tabindex="3">
				<small>
				<br />Enter your EzineArticles Member's Password associated with your username.
				</small>
				</td>
			</tr>
		</table>
		
		<div class="submit">
			<input type="submit" name="save_settings" value="<?php _e('Save Settings', 'ea')?>">
			<input type="submit" name="reset_settings" value="<?php _e('Reset Settings', 'ea')?>">
		</div>
	</form>
</div>