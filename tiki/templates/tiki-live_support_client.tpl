<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>Live support:User window</title>
    {literal}
	<script type="text/javascript" src="lib/live_support/live-support.js">
	</script>
	{/literal}
	{$trl}
  </head>
  <body onUnload="client_close();">
  	<div id='request_chat' align="center">
  		<input type="hidden" id="reqId" />
		<input type="hidden" id="user" value="{$user}" />
		<input type="hidden" id="email" value="{$user_email}" />
		<input type="hidden" id="tiki_user" value="{$user}" />
		
		<h2>{tr}Request live support{/tr}</h2>
		<table>
			{if $user}
				<tr>
					<td>{tr}User{/tr}</td>
					<td>
						{$user}				
					</td>
				</tr>
				<tr>
					<td>{tr}Email{/tr}</td>
					<td>
						{$user_email}
					</td>
				</tr>
			{else}
				<tr>
					<td>{tr}User{/tr}</td>
					<td>
						<input type="text" id="user" />			
					</td>
				</tr>
				<tr>
					<td>{tr}Email{/tr}</td>
					<td>
						<input type="text" id="email" />
					</td>
				</tr>
			{/if}
			<tr>
				<td>{tr}Reason{/tr}</td>
				<td>
					<!--input id='reason' type="text" />-->
					<textarea id='reason' cols='20' rows='3'></textarea>		
				</td>
			</tr>	
		</table>
		
		<br/><br/>				
		<input onClick="request_chat(document.getElementById('user').value,document.getElementById('tiki_user').value,document.getElementById('email').value,document.getElementById('reason').value);" type="button" value="{tr}Request support{/tr}" />
	</div>
	
	<div id='requesting_chat' style='display:none;'>
		<b>{tr}Your request is being processed{/tr}....</b>
		<br/><br/>
		<a onClick="javascript:client_close();window.close();" class="link">{tr}cancel request and exit{/tr}</a><br/>
		<a href="tiki-live_support_message.php" class="link">{tr}cancel request and leave a message{/tr}</a></br>
	</div>
	
  </body>
</html>  