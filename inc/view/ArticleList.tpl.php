<div class="wrap">
	<h2><?php echo WP_EA?></h2>
	<p>This page is displaying most recent blog posts you have submitted to EzineArticles.com.</p>
	<table class="widefat post fixed" cellpadding="0">
	<thead>
		<tr>
			<th>Post</th>
			<th>Category</th>
			<th>Status</th>
			<th>Submitted</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach($ea_articlelist as $post_id => $ea_article_result)
	{

		$ea_article = (object)$ea_article_result['article'];

		$edit_link = "<a class='row-title' href='post.php?action=edit&amp&post={$post_id}'>{$ea_article->title}</a>";

		$ea_category = preg_replace('/[^a-zA-Z]/',' ', $ea_article->category);

		if($ea_article->subcategory)
			$ea_category.= '&rsaquo;' . preg_replace('/[^a-zA-Z]/',' ', $ea_article->subcategory);

		?>
		<tr>
			<td class="post-title"><strong><?php echo $edit_link?></strong></td>
			<td><?php echo $ea_category?></td>
			<td><?php echo ucwords($ea_article->status)?></td>
			<td><?php echo date('F j, Y', strtotime($ea_article->date_submitted))?></td>
		</tr>
	<?php
	}
	?>
	</tbody>
	</table>
</div>