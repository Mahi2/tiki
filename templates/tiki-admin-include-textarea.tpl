{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Text area (that apply throughout many features){/tr}
{/remarksbox}

<form action="tiki-admin.php?page=textarea" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div align="center" style="padding:1em"><input type="submit" name="textareasetup" value="{tr}Change Preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Plugins{/tr}|
				{tr}Plugin Aliases{/tr}
			{/strip}{/tabs}
{/if}

      {cycle name=content values="1,2,3" print=false advance=false reset=true}

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}General Settings{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Text+Area"}{/if}</legend>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_antibot" name="feature_antibot" {if $prefs.feature_antibot eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_antibot">{tr}Anonymous editors must enter anti-bot code (CAPTCHA){/tr}. </label>{if $prefs.feature_help eq 'y'} {help url="Spam+Protection"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_protect_email" name="feature_wiki_protect_email" {if $prefs.feature_wiki_protect_email eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_protect_email">{tr}Protect email against spam{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_smileys" name="feature_smileys" {if $prefs.feature_smileys eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_smileys">{tr}Smileys{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Smiley"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_ext_icon" name="feature_wiki_ext_icon" {if $prefs.feature_wiki_ext_icon eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_ext_icon">{tr}External link icon{/tr}</label>
		<br /><em>{tr}External links will be identified with{/tr}: </em><img border="0" class="externallink" src="img/icons/external_link.gif" alt=" (external link)" />.
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="popupLinks" id="general-ext_links" {if $prefs.popupLinks eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-ext_links">{tr}Open external links in new window{/tr}.</label>
	<br /><em>{tr}External links will be identified with{/tr}: </em><img border="0" class="externallink" src="img/icons/external_link.gif" alt=" (external link)" />.
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_filegals_manager" id="feature_filegals_manager" {if $prefs.feature_filegals_manager eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_filegals_manager">{tr}Use File Galleries to store pictures {/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_dynamic_content" name="feature_dynamic_content" id="feature_dynamic_content" {if $prefs.feature_dynamic_content eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_dynamic_content">{tr}Dynamic Content System{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Dynamic+Content"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_comments_post_as_anonymous" id="feature_comments_post_as_anonymous"{if $prefs.feature_comments_post_as_anonymous eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_comments_post_as_anonymous">{tr}Allow to post comments as Anonymous{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Post+Comments+as+Anonymous"}{/if}</div>
</div>
</fieldset>


<fieldset><legend>{tr}Wiki syntax{/tr} {if $prefs.feature_help eq 'y'} {help url="Wiki+Syntax"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id='feature_autolinks' name="feature_autolinks" {if $prefs.feature_autolinks eq 'y'}checked="checked"{/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_autolinks">{tr}AutoLinks{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="AutoLinks"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="quicktags_over_textarea" id="quicktags_over_textarea" {if $prefs.quicktags_over_textarea eq 'y'}checked="checked"{/if}/> </div>
	<div class="adminoptionlabel"><label for="quicktags_over_textarea">{tr}Show quicktags above textareas{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="Quicktags"}{/if}
	<br /><em>{tr}If disabled, quicktags will be shown to the left of textareas{/tr}.</em>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input onclick="flip('hotwords_nw');" type="checkbox" name="feature_hotwords" id="feature_hotwords" {if $prefs.feature_hotwords eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_hotwords">{tr}Hotwords{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Hotwords"}{/if}</div>
	
	<div class="adminoptionboxchild" id="hotwords_nw" style="display:{if $prefs.feature_hotwords eq 'y'}block{else}none{/if};">
		<div class="adminoption"><input type="checkbox" name="feature_hotwords_nw" id="feature_hotwords_nw" {if $prefs.feature_hotwords_nw eq 'y'}checked="checked"{/if}/> </div>
		<div class="adminoptionlabel"><label for="feature_hotwords_nw">{tr}Open Hotwords in new window{/tr}.</label></div>	
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_use_quoteplugin" id="feature_use_quoteplugin"{if $prefs.feature_use_quoteplugin eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_use_quoteplugin"> {tr}Use Quote plugin rather than &ldquo;&gt;&rdquo; for quoting{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="PluginQuote"}{/if}
{if $prefs.wikiplugin_quote ne 'y'}<br />{icon _id=information} {tr}Plugin disabled{/tr}. {/if}
	</div>
</div>
</fieldset>

<fieldset><legend>{tr}Default size{/tr}</legend>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_wiki">{tr}Wiki{/tr}:</label> <input type="text" name="default_rows_textarea_wiki" id="default_rows_textarea_wiki" value="{$prefs.default_rows_textarea_wiki}" size="4" /> {tr}rows{/tr}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_comment">{tr}Comments {/tr}:</label><input type="text" name="default_rows_textarea_comment" id="default_rows_textarea_comment" value="{$prefs.default_rows_textarea_comment}" size="4" />{tr}rows{/tr}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_forum">{tr}Forum{/tr}:</label><input type="text" name="default_rows_textarea_forum" id="default_rows_textarea_forum" value="{$prefs.default_rows_textarea_forum}" size="4" />{tr}rows{/tr}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_forumthread">{tr}Forum reply{/tr}: </label><input type="text" name="default_rows_textarea_forumthread" id="default_rows_textarea_forumthread" value="{$prefs.default_rows_textarea_forumthread}" size="4" />{tr}rows{/tr}</div>
</div>
</fieldset>


      {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

	<!-- *** plugins *** -->
    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
		{if $prefs.feature_tabs neq 'y'}
			<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}">
				<a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
					<span>{tr}Plugins{/tr}</span>
				</a>
		</legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
		{/if}
		
		{remarksbox type="note" title="{tr}About plugins{/tr}"}{tr}Tiki plugins add functionality to wiki pages, artcles and blogs etc. You can enable and disable them here.{/tr}{/remarksbox}

		{foreach from=$plugins key=plugin item=info}
			<div class="adminoptionbox">
				{assign var=pref value=wikiplugin_$plugin}
				{if in_array( $pref, $info.prefs)}
					<div class="adminoption"><input type="checkbox" id="wikiplugin_{$plugin|escape}" name="wikiplugin_{$plugin|escape}" {if $prefs[$pref] eq 'y'}checked="checked" {/if}/>
					</div>
				{/if}
					<div class="adminoptionlabel"><label for="wikiplugin_{$plugin|escape}">{$info.name|escape}</label>
						{if $prefs.feature_help eq 'y'} {help url="Plugin$plugin"}{/if}
						<br /><strong>{$plugin|escape}</strong>: {$info.description|escape}
					</div>
			</div>
		{/foreach}

		{if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

	<!-- *** plugin aliases *** -->
    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent admin2cols" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
		{if $prefs.feature_tabs neq 'y'}
			<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}">
				<a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
					<span>{tr}Plugin Aliases{/tr}</span>
				</a>
			</legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
		{/if}

		{remarksbox type="note" title="{tr}About plugin aliases{/tr}"}{tr}Tiki plugin aliases allow you to define your own custom configurations of existing plugins..{/tr}{/remarksbox}

		{* from tiki-admin-include-plugins.tpl *}

		{if $plugins_alias|@count}
			<fieldset>
				<legend>{tr}Available Alias{/tr}</legend>
				<div class="input_submit_container">
					{foreach from=$plugins_alias item=name}
						{assign var=full value='wikiplugin_'|cat:$name}
						<input type="checkbox" name="enabled[]" value="{$name|escape}" {if $prefs[$full] eq 'y'}checked="checked"{/if}/>
						<a href="tiki-admin.php?page=textarea&amp;plugin_alias={$name|escape}">{$name|escape}</a>
					{/foreach}
					<div>
						<input type="submit" name="enable" value="{tr}Enable Plugins{/tr}"/>
					</div>
				</div>
			</fieldset>
		{/if}

		{if $plugin_admin}
			{button href="tiki-admin.php?page=textarea" _text="{tr}New{/tr}"}
		{/if}

		<fieldset id="pluginalias_general">
			<legend>{tr}General Information{/tr}</legend>
		
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="plugin_alias">{tr}Plugin Name{/tr}:</label>
					{if $plugin_admin}
						<input type="hidden" name="plugin_alias" value="{$plugin_admin.plugin_name|escape}"/>
						{$plugin_admin.plugin_name|escape}
					{else}
						<input type="text" name="plugin_alias"/>
					{/if}
				</div>
			</div>
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="implementation">{tr}Base Plugin{/tr}:</label>
					<select name="implementation">
						{foreach from=$plugins_real item=base}
							<option value="{$base|escape}" {if $plugin_admin.implementation eq $base}selected="selected"{/if}>{$base|escape}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="implementation">{tr}Name{/tr}:</label> <input type="text" name="name" value="{$plugin_admin.description.name|escape}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="description">{tr}Description{/tr}:</label> <input type="text" name="description" value="{$plugin_admin.description.description|escape}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="prefs">{tr}Dependencies{/tr}:</label> <input type="text" name="prefs" value="{','|implode:$plugin_admin.description.prefs}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="">{tr}Filter{/tr}:</label> <input type="text" name="filter" value="{$plugin_admin.description.filter|default:'xss'|escape}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="validate">{tr}Validation{/tr}:</label> 
					<select name="validate">
						{foreach from=','|explode:'none,all,body,arguments' item=val}
							<option value="{$val|escape}" {if $plugin_admin.description.validate eq $val}selected="selected"{/if}>{$val|escape}</option>
						{/foreach}
					</select>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="">{tr}Inline (No Plugin Edit UI){/tr}:</label> <input type="checkbox" name="inline" value="1" {if $plugin_admin.description.inline}checked="checked"{/if}/>
			</div></div>
		</fieldset>
		<fieldset id="pluginalias_doc">
			<legend>{tr}Plugin Parameter Documentation{/tr}</legend>
			
			{foreach from=$plugin_admin.description.params key=token item=detail}
				<div class="adminoptionbox">
					<div class="adminoptionlabel" style="float:left">
						<input type="text" name="input[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}"/>
					</div>
					<div class="adminnestedbox">
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][name]">{tr}Name{/tr}:</label> <input type="text" name="input[{$token|escape}][name]" value="{$detail.name|escape}"/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][description]">{tr}Description{/tr}:</label> <input type="text" name="input[{$token|escape}][description]" value="{$detail.description|escape}"/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][required]">{tr}Required{/tr}:</label> <input type="checkbox" name="input[{$token|escape}][required]" value="y"{if $detail.required} checked="checked"{/if}/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][safe]">{tr}Safe{/tr}:</label> <input type="checkbox" name="input[{$token|escape}][safe]" value="y"{if $detail.safe} checked="checked"{/if}/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][filter]">{tr}Filter{/tr}:</label> <input type="text" name="input[{$token|escape}][filter]" value="{$detail.filter|default:xss|escape}"/>
						</div>
					</div>
				</div>
			{/foreach}
		</fieldset>
		<fieldset id="pluginalias_body">
			<legend>{tr}Plugin Body{/tr}</legend>

			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="ignorebody">{tr}Ignore User Input{/tr}:</label> <input type="checkbox" name="ignorebody" value="y" {if $plugin_admin.body.input eq 'ignore'}checked="checked"{/if}/>
				</div>
			</div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="defaultbody">{tr}Default Content{/tr}:</label>
					<textarea cols="60" rows="12" name="defaultbody">{$plugin_admin.body.default|escape}</textarea>
			</div></div>
			<fieldset style="margin-left: 200px">
				<legend>{tr}Parameters{/tr}</legend>
				<div class="adminoptionbox">
					<div class="adminoptionlabel" style="float:left">
						<input type="text" name="bodyparam[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}"/>
					</div>
					
					<div class="adminnestedbox">
						{foreach from=$plugin_admin.body.params key=token item=detail}
							<div class="adminoptionbox">
								<div class="adminoptionlabel">
									<label for="bodyparam[{$token|escape}][encoding]">{tr}Encoding{/tr}:</label> 
									<select name="bodyparam[{$token|escape}][encoding]">
										{foreach from=','|explode:'none,html,url' item=val}
											<option value="{$val|escape}" {if $detail.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
										{/foreach}
									</select>
								</div>
							</div>
							<div class="adminoptionbox"><div class="adminoptionlabel">
									<label for="bodyparam[{$token|escape}][input]">{tr}Argument Source (if different){/tr}:</label> <input type="text" name="bodyparam[{$token|escape}][input]" value="{$detail.input|escape}"/>
							</div></div>
							<div class="adminoptionbox"><div class="adminoptionlabel">
									<label for="bodyparam[{$token|escape}][default]">{tr}Default Value{/tr}:</label> <input type="text" name="bodyparam[{$token|escape}][default]" value="{$detail.default|escape}"/>
							</div></div>
						{/foreach}
					</div>
				</div>
			</fieldset>
		</fieldset>
		<fieldset id="pluginalias_simple_args">
			<legend>{tr}Simple Plugin Arguments{/tr}</legend>
			
			{foreach from=$plugin_admin.params key=token item=value}
				{if ! $value|is_array}
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label for="sparams[{$token|escape}][token]">{tr}Argument{/tr}:</label> <input type="text" name="sparams[{$token|escape}][token]" value="{$token|escape}"/>
							<label for="sparams[{$token|escape}][default]" style="float:none;display:inline">{tr}Default{/tr}:</label> <input type="text" name="sparams[{$token|escape}][default]" value="{$value|escape}"/>
						</div>
					</div>
				{/if}
			{/foreach}
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="sparams[__NEW__][token]">{tr}New Argument{/tr}:</label> <input type="text" name="sparams[__NEW__][token]" value=""/>
					<label for="sparams[__NEW__][default]" style="float:none;display:inline">{tr}Default{/tr}:</label> <input type="text" name="sparams[__NEW__][default]" value=""/>
				</div>
			</div>
		</fieldset>
		<fieldset id="pluginalias_composed_args">
			<legend>{tr}Composed Plugin Arguments{/tr}</legend>

			{foreach from=$plugin_admin.params key=token item=detail}
				{if $detail|is_array}
					<div class="adminoptionbox">
						<div class="adminoptionlabel" style="float:left">
							<input type="text" name="cparams[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}"/>
						</div>
						<div class="adminoptionlabel" style="float:left">
							<label for="cparams[{$token|escape}][pattern]" style="width: auto; margin: 0 2em">{tr}Pattern{/tr}:</label> <input type="text" name="cparams[{$token|escape}][pattern]" value="{$detail.pattern|escape}"/>
						</div>
					</div>
					<fieldset style="margin-left: 200px; clear: both">
						<legend>{tr}Parameters{/tr}</legend>
						{foreach from=$detail.params key=t item=d}
							<div class="adminoptionlabel">
								<input type="text" name="cparams[{$token|escape}][params][{$t|escape}][token]" value="{if $t neq '__NEW__'}{$t|escape}{/if}"/>
							</div>
							<div class="adminoptionlabel">
								<label for="cparams[{$token|escape}][pattern]">{tr}Encoding{/tr}:</label>
								<select name="cparams[{$token|escape}][params][{$t|escape}][encoding]">
									{foreach from=','|explode:'none,html,url' item=val}
										<option value="{$val|escape}" {if $d.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
									{/foreach}
								</select>
							</div>
							<div class="adminoptionlabel">
								<label for="cparams[{$token|escape}][params][{$t|escape}][input]">{tr}Argument Source (if different){/tr}:</label> <input type="text" name="cparams[{$token|escape}][params][{$t|escape}][input]" value="{$d.input|escape}"/>
							</div>
							<div class="adminoptionlabel">
								<label for="cparams[{$token|escape}][params][{$t|escape}][input]">{tr}Default Value{/tr}:</label> <input type="text" name="cparams[{$token|escape}][params][{$t|escape}][default]" value="{$d.default|escape}"/>
							</div>
						{/foreach}
					</fieldset>
				{/if}
			{/foreach}

		</fieldset>

		<input type="submit" name="save" value="{tr}Save{/tr}"/>

		{if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>


<div align="center" style="padding:1em"><input type="submit" name="textareasetup" value="{tr}Change Preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>


