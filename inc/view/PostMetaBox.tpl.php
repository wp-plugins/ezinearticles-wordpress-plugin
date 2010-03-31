
<div id="ea-publish-switch-wrap">
	<input type="checkbox" name="ea_publish_switch" id="ea-publish-switch" onClick="javascript:switchTrigger()">
		<span id="ea-publish-switch-display" onClick="javascript:switchTrigger()">
			Publish Post on EzineArticles
		</span>
</div>

<div id="ea-publish-wrap" style="display:none">
<?php
//!ea_get_option('ea_account_status')
if(!$ea_account_status)
{
	?>
	<div class="misc-pub-section">
		<p><b>Sorry, there was an error.</b></p>
		<p>Please verify your EzineArticles API Key, Username and Password are correct.</p>
	</div>
	<?php
	return false;
}
?>

<div class="misc-pub-section">
	<span>Category:</span><br />
	<?php getCategorySelect() ?>
</div>

<div class="misc-pub-section">
	<span>Author:</span><br />
	<?php getAuthorSelect() ?>
</div>

<div class="misc-pub-section">
	<span>Signature:</span>
	<?php $hasResourceBoxes = getResourceBoxSelect() ?><a id="ea-resourcebox-options-edit" href="#ea-resourcebox-options">Edit</a>
	<div id="ea-resourcebox-options-wrap" style="display:none">
		<?php if ($hasResourceBoxes || $edited_resource_text):?>
		Signature Body:<br />
		<?php else:?>
		<p class="howto">Enter Your New Signature Here:</p>
		<?php endif;?>
		<textarea name="ea_resourcebox_text" id="ea-resourcebox-text"></textarea><br />
		<a id="ea-resourcebox-options-cancel" href="#">Close</a>
	</div>
</div>

<div class="misc-pub-section">
	<span>Summary:</span> <span id="ea-summary-options-display">First 2 Sentences of Post</span> <a id="ea-summary-options-edit" href="#ea-summary-options">Edit</a>
	<div id="ea-summary-options-wrap" style="display:none">
		<select name="ea_summary" id="ea-summary">
			<option value="use_excerpt">Use Excerpt</option>
			<option value="use_first" selected="1">First 2 Sentences of Post</option>
		</select>
		<a id="ea-summary-options-cancel" href="#">Cancel</a>
	</div>
</div>

<div class="misc-pub-section">
	<span>Keywords:</span> <span id="ea-keywords-options-display">Use Post Tags</span> <a id="ea-keywords-options-add" href="#ea-keywords-options">Add</a>
</div>

<?php  $account_status = ea_get_option('ea_account_status');
       $account_status = $account_status[0];
       if($account_status['membership_status'] == 'Premium') { ?>
                    <div class="misc-pub-section">
                    <input type="checkbox" name="ea_schedule_switch" id="ea-schedule-switch" onClick="switchBold();">
                    <span id="ea-schedule-switch-display" onClick="switchBold();">Schedule Release of this Post</span>
                    <div id="ea-schedule-options-wrap" style="display:none">
                    <input type="text" name="schedule_date" id="schedule-date-picker" size="2" maxlength="2" value="<?php echo date('j'); ?>">
                    of
                    <select name="schedule_month" id="schedule_month">
                    <?php
                       $month = date("n");
                       $year = date("Y");
                       for($i = 0; $i <= 3; $i++) {
                       if($month > 12)
                       {
                            $month = 1;
                            $year = $year + 1;
                       }
                       $value = $month.':'.$year;
                       $currMonth =  mktime(0, 0, 0, $month, 1, $year);
                       $format = date('M', $currMonth)." ".$year ?>
                               <option value="<?php echo $value ?>"><?php echo $format ?></option>
                               <?php $month ++;
                } ?>
                     </select>
                     @
                    <select name="schedule_hour" id="schedule-hour-list">
                               <option value="0">12 AM</option>
                               <option value="1">1 AM</option>
                               <option value="2">2 AM</option>
                               <option value="3">3 AM</option>
                               <option value="4">4 AM</option>
                               <option value="5">5 AM</option>
                               <option value="6">6 AM</option>
                               <option value="7">7 AM</option>
                               <option selected="selected" value="8">8 AM</option>
                               <option value="9">9 AM</option>
                               <option value="10">10 AM</option>
                               <option value="11">11 AM</option>
                               <option value="12">12 PM</option>
                               <option value="13">1 PM</option>
                               <option value="14">2 PM</option>
                               <option value="15">3 PM</option>
                               <option value="16">4 PM</option>
                               <option value="17">5 PM</option>
                               <option value="18">6 PM</option>
                               <option value="19">7 PM</option>
                               <option value="20">8 PM</option>
                               <option value="21">9 PM</option>
                               <option value="22">10 PM</option>
                               <option value="23">11 PM</option>
                     </select>
             </div>
</div>
<?php } else { ?>
<div class="misc-pub-section">
Scheduled Release
	<a href="http://EzineArticles.com/premium/" title="Premium Feature: Schedule release of this post. Click to find out how to become a Premium Member." target="_blank"><img src="<?php echo WP_EA_PLUGIN_PATH?>/img/premium.png" border="0" align="center" alt="Premium Feature: Schedule Release."></a>
<div align="center"><small><a href="http://EzineArticles.com/premium/" title="Click to find out how to become a Premium Member." target="_blank">This feature is available to Premium Members</a></small></div>
</div>
<?php }?>
<div class="misc-pub-section misc-pub-section-last">
	<div id="minor-publishing-actions">
		<input id="ea-validate-post" class="button button-highlighted" type="button" value="Validate" name="ea_do_validate" title="Checks the article for rejectable content">
		<input id="ea-publish-post" class="button-primary" type="button" value="Submit" name="ea_do_publish" title="Submits to EzineArticles.com, does not publish to WordPress">
	</div>
</div>
<div class="misc-pub-section clear center">
	Please save as a draft before validating.
</div>
</div>
