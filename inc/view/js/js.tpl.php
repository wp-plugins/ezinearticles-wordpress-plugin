<script type="text/javascript">

var $ = jQuery.noConflict();

$(document).ready(function() {

		var url = '<?php echo EA_AJAX?>';

		$('#ea-resourcebox-text').text($('#ea-resourcebox option:selected').attr('label'));

		$('#ea-summary-options-edit').click(function() {

			if ($('#ea-summary-options-wrap').is(":hidden")) {
				$('#ea-summary-options-wrap').slideDown("normal");
				$(this).hide();
			}
			return false;
		});

		$('#ea-resourcebox-options-edit').click(function(){

			if ($('#ea-resourcebox-options-wrap').is(":hidden")){
				$('#ea-resourcebox-options-wrap').slideDown("normal");
				$(this).hide();
			}
			return false;
		});

		$('#ea-summary').change(function(){

			$('#ea-summary option:selected').val();
			$('#ea-summary-options-display').text($('#ea-summary option:selected').text());
			$('#ea-summary-options-wrap').slideUp("normal");
			$('#ea-summary-options-edit').show();

			return false;

		});

		$('#ea-resourcebox').change(function(){
			$('#ea-resourcebox-text').text($('#ea-resourcebox option:selected').attr('label'));
			return false;
		});

		$('#ea-summary-options-cancel').click(function() {
			$('#ea-summary-options-wrap').slideUp("normal");
			$('#ea-summary-options-edit').show();
			return false;
		});

		$('#ea-resourcebox-options-cancel').click(function(){
			$('#ea-resourcebox-options-wrap').slideUp("normal");
			$('#ea-resourcebox-options-edit').show();
			return false;
		});

		$('#ea-keywords-options-add').click(function() {

            $("#new-tag-post_tag").focus();
			$("#new-tag-post_tag").animate({backgroundColor: '#FFB6C1'},500).animate({backgroundColor: '#ffffff'},500).animate({backgroundColor: '#FFB6C1'},500).animate({backgroundColor: '#ffffff'},500);

		});

		$('#ea-publish-post').click(function() {
			autosave();
			$.ajax({ url: url, data: $('#post').serialize(), success: function(response)
			{
				$(location).attr('href', 'post.php?action=edit&post=' + $('#post_ID').val());
			}
			});
		});

		$('#ea-validate-post').click(function(){
			autosave();
			$.ajax({ url: url, data: $('#post').serialize() + '&ea_do_validate=1', success: function(response)
			{
				$(location).attr('href', 'post.php?action=edit&post=' + $('#post_ID').val());
			}
			});
		});

});

function switchTrigger()
{
	$(document).ready(function() {
		if ($('#ea-publish-wrap').is(":hidden")) 
		{
			$('#ea-publish-wrap').slideDown("normal");
			$('#ea-publish-switch').attr('checked', true);
			$('#ea-publish-switch-display').css('fontWeight', 'bold');
		}
		else
		{
			$('#ea-publish-wrap').slideUp("normal");
			$('#ea-publish-switch').attr('checked', false);
			$('#ea-publish-switch-display').css('fontWeight', 'normal');
		}
		return false;
	});
}

function switchBold()
{
	$(document).ready(function() {
		if ($('#ea-schedule-options-wrap').is(":hidden")) {
			$('#ea-schedule-options-wrap').slideDown("normal");
			$('#ea-schedule-switch').attr('checked', true);
			$('#ea-schedule-switch-display').css('fontWeight', 'bold');

		}
		else
		{
			$('#ea-schedule-options-wrap').slideUp("normal");
			$('#ea-schedule-switch').attr('checked', false);
			$('#ea-schedule-switch-display').css('fontWeight', 'normal');
		}
		return false;
	});
}

</script>
