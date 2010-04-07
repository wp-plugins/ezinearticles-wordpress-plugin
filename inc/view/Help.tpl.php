<div class="wrap">
	<h2><?php echo WP_EA?> - Help</h2>
	This describes the basic steps for publishing to EzineArticles through this WordPress Plugin.

	<h3><?php _e('1) Write an Article', 'ea') ?></h3>
	<p>
		The first step in submitting a high quality article is writing one. Expand 'Posts' and either add a new post or edit one you already have.<br>
		Once your post is to your liking, you can fill in the information that EzineArticles will need to publish it as an articles.
	</p>
	<hr></hr>
	<h3><?php _e('2) Fill EzineArticles related fields.', 'ea') ?></h3>
	<p>
		There are several fields that must be filled to allow you to post to EzineArticles.com. You should have a window labeled 'WP EzineArticles' which has a checkbox.  Checking it will slide down a menu that allows you to see these fields.
	</p>

	<ul>
		<li><b>Title</b> - You must choose a unique title.  Validation will tell you if it has been used before.</li>
		<li><b>Body</b> - Your body will be taken from the body of your post.  You will most likely need to alter this to pass validation.</li>
		<li><b>Category</b> - You must select a category for the article from the drop down list.</li>
		<li><b>Authors</b> - The author drop down should have a list of authors under your EzineArticles account.  Select the one you want to use.</li>
		<li><b>Resource Box</b> - The resource box drop down will contain a list of resource boxes you have saved in your EzineArticles account.
			Selecting one and pushing edit will allow you to edit it for the next submit.
			This box is meant for tweaking for a specific article and will not replace the one stored in your EzineArticles account.
			There is currently no way to permanently overwrite your resource box from WordPress, you will need to log in to your account at http://Members.EzineArticles.com/ to do that.</li>
		<li><b>Summary</b> - Allows you to choose whether the Excerpt or the first two sentences of the body is used for the summary.</li>
		<li><b>Keywords</b> - Post Tags are converted into keywords for EzineArticles.  By clicking add, your cursor will be moved to post tags.</li>
	</ul>

	<hr></hr>
	<h3><?php _e('3) Validate your Article', 'ea') ?></h3>
	<p>
		<b>Validate</b> - Validates your article against the EzineArticles Editorial Guidelines. It will tell you if there are any parts of your article that would cause it to be automatically rejected. You will see a yellow box appear above your article with the details of any problems that were encountered.  If you still want these in your WordPress version, you should publish for WordPress, edit it, and then submit it to EzineArticles.
	</p>
	<hr></hr>
	<h3><?php _e('4) Publish', 'ea') ?></h3>
	<p>
		You are now able to publish to EzineArticles and WordPress separately.  This allows you to tweak your article for EzineArticles while having the freedom to publish as you wish on your WordPress blog.
	</p>
	<ul>
		<li><b>Submit</b>  - This submits your post to EzineArticles for review.  If there are any automatically detected issues with submission it will report them to you in the same way as validate. </li>
		<li><b>Publish</b> - This publishes the post to your WordPress blog. </li>
	</ul>

</div>