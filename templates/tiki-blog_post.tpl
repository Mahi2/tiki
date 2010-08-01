{popup_init src="lib/overlib.js"}

{title url="tiki-blog_post.php?blogId=$blogId&amp;postId=$postId"}{if $postId gt 0}{tr}Edit Post{/tr}{else}{tr}Post{/tr}{/if} - {$blog_data.title|escape}{/title}

<div class="navbar">
	{if $blogId gt 0 }
		{assign var=thisblog value=$blogId|sefurl:blog}
		{button href=$thisblog _text="{tr}View Blog{/tr}"}
	{/if}

	{if $blogs|@count gt 1 }
		{* No need for users to go to log list if they are already looking at the only blog *}
		{button href="tiki-list_blogs.php" _text="{tr}List Blogs{/tr}"}
	{/if}
</div>

{if $contribution_needed eq 'y'}
	{remarksbox type='Warning' title='{tr}Warning{/tr}'}
		<div class="highlight"><em class='mandatory_note'>{tr}A contribution is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}

{if $preview eq 'y'}
	<div align="center" class="attention" style="font-weight:bold">{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}</div>
	{include file='tiki-view_blog_post_content.tpl'}
{/if}

<form enctype="multipart/form-data" name='blogpost' method="post" action="tiki-blog_post.php" id ='editpageform'>
	<input type="hidden" name="wysiwyg" value="{$wysiwyg|escape}" />
	<input type="hidden" name="postId" value="{$postId|escape}" />

	<table class="normal">
		{if $blogs|@count gt 1 and ( !isset($blogId) or $blogId eq 0 )}
			<tr>
				<td class="editblogform">{tr}Blog{/tr}</td>
				<td class="editblogform">
					<select name="blogId">
						{section name=ix loop=$blogs}
							<option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title|escape}</option>
						{/section}
					</select>
				</td>
			</tr>
		{else}
			<input type="hidden" name="blogId" value="{$blogId|escape}" />
		{/if}

		<tr>
			<td class="editblogform">{tr}Title{/tr}</td><td class="editblogform">
				<input type="text" size="80" name="title" value="{$title|escape}" />
			</td>
		</tr>

		<tr>
			<td class="editblogform">
				{tr}Body{/tr}
			</td>
			<td class="editblogform">
				{textarea id='blogedit' class="wikiedit" name="data"}{$data}{/textarea}
			</td>
		</tr>

		{if $postId > 0 && $wysiwyg ne 'y'}
			{if count($post_images) > 0}
				<tr>
					<td class="editblogform">{tr}Images{/tr}</td>
					<td class="editblogform">
						<table>
							{section name=ix loop=$post_images}
								<tr>
									<td>
										<a class="link" href="tiki-view_blog_post_image.php?imgId={$post_images[ix].imgId}">{$post_images[ix].filename}</a> 
									</td>
									<td>
										<textarea rows="2" cols="40">{$post_images[ix].link|escape}</textarea><br />
										<textarea rows="1" cols="40">{$post_images[ix].absolute|escape}</textarea>
									</td>
									<td>
										<a href="tiki-blog_post.php?postId={$postId}&amp;remove_image={$post_images[ix].imgId}"><img src='img/icons/trash.gif' alt='{tr}Trash{/tr}'/></a>
									</td>
								</tr>
							{/section}
						</table>
					</td>
				</tr>
			{/if}
		{/if}

		<tr>
			<td class="editblogform">{tr}Mark entry as private:{/tr}</td>
			<td class="editblogform"><input type="checkbox" name="blogpriv" {if $blogpriv eq 'y'}checked="checked"{/if} /></td>
		</tr>
		<tr id='show_pubdate' class="formcolor">
			<td>{tr}Publish Date{/tr}</td>
			<td>
				{html_select_date prefix="publish_" time=$created start_year="-5" end_year="+10" field_order=$prefs.display_field_order} {tr}at{/tr} 
				{html_select_time prefix="publish_" time=$created display_seconds=false}
			</td>
		</tr>
		{if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
			{include file='freetag.tpl'}
		{/if}
		{if $prefs.feature_contribution eq 'y'}
			{include file='contribution.tpl'}
		{/if}
		<tr>
			<td class="editblogform">&nbsp;</td>
			<td class="editblogform">
				<input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false" />
				<input type="submit" class="wikiaction" name="save_exit" value="{tr}Save{/tr}" onclick="needToConfirm=false" />
				<input type="hidden" name="referer" value="{$referer|escape}" />
				&nbsp;&nbsp;&nbsp;<input type="submit" name="cancel" onclick='document.location="{$referer|escape:'html'}";needToConfirm=false;return false;' value="{tr}Cancel{/tr}"/>
			</td>
		</tr>
	</table>
</form>
<br />
