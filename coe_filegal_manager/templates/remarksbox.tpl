{* $Id: $ *}
{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
<div class="rbox {$remarksbox_type}">
{if $remarksbox_close eq 'y'}
	{icon _id='close' class='rbox-close' onclick='$jq(this).parent().fadeOut();'}
{/if}
{if $remarksbox_title ne ''}
	<div class="rbox-title">
{if $remarksbox_icon ne 'none'}
		{icon _id=$remarksbox_icon style='vertical-align: middle'}
{/if}
		<span>{$remarksbox_title}</span>
	</div>
{/if}
	<div class="rbox-data{$remarksbox_highlight}">
		{$remarksbox_content}
	</div>
</div>