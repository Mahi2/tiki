<a class="pagetitle" href="tiki-edit_banner.php">Edit or create banners</a><br/><br/>
[<a class="link" href="tiki-list_banners.php">List banners</a>]<br/><br/>
<form action="tiki-edit_banner.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="bannerId" value="{$bannerId}" />
<div class="simplebox">
  <table width="100%"><tr><td>
  <table width="100%">
  <tr>
     <td class="form">{tr}URL to link the banner{/tr}</td><td><input type="text" name="url" value="{$url}" /></td>
   </tr>
  <tr><td class="form">{tr}Client{/tr}:</td>
      <td>
        <select name="client">
        {section name=ix loop=$clients}
        <option value="{$clients[ix].user}">{$clients[ix].user}</option>
        {/section}
        </select>
      </td>
   </tr>
   <tr><td class="form">{tr}Max impressions{/tr}:</td>
       <td><input type="text" name="maxImpressions" value="{$maxImpressions}" size="7" /></td>
   </tr>
   <tr><td  class="form" valign="top">{tr}Zone{/tr}:</td>
       <td><select name="zone">
           {section name=ix loop=$zones}
           <option value="{$zones[ix].zone}" {if $zone eq $zones[ix].zone}selected="selected"{/if}>{$zones[ix].zone}</option>
           {/section}
           </select>
        </td>
   </tr>
   </table>
   </td>
   <td align="right"  class="form">
   {tr}create zone{/tr}:
   <input type="text" name="zoneName" size="10" /><br/>
   <input type="submit" name="create_zone" value="{tr}create zone{/tr}" />
   </td>
   </tr>
   
   </table>
</div>



<div class="simplebox">
<table width="100%">
<tr><td colspan="2" class="form">{tr}Show the banner only between these dates{/tr}:</tr></tr>
<tr><td class="form">{tr}From date{/tr}:</td><td>{html_select_date time=$fromDate prefix="fromDate_" end_year="+2"}</td></tr>
<tr><td class="form">{tr}To date{/tr}:</td><td>{html_select_date time=$toDate prefix="toDate_" end_year="+2"}</td></tr>
<tr><td class="form">{tr}Use dates{/tr}</td><td><input type="checkbox" name="useDates" {if $useDates eq 'y'}checked='checked'{/if}/></td></tr>
</table>
</div>


<div class="simplebox">
<table with="100%">
<tr><td colspan="2" class="form">{tr}Show the banner only in this hours{/tr}:</td></tr>
<tr><td class="form">{tr}from{/tr}:</td><td>{html_select_time time=$fromTime display_seconds=false prefix=fromTime}</td></tr>
<tr><td class="form">{tr}to{/tr}:</td><td>{html_select_time time=$toTime display_seconds=false prefix=toTime}</td></tr>
</table>
</div>

<div class="simplebox">
<table with="100%">
<tr><td colspan="7" class="form">{/tr}Show the banner only on{/tr}:</td></tr>
<tr>
<td class="form">{tr}Mon{/tr}:<input type="checkbox" name="Dmon" {if $Dmon eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Tue{/tr}:<input type="checkbox" name="Dtue" {if $Dtue eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Wed{/tr}:<input type="checkbox" name="Dwed" {if $Dwed eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Thu{/tr}:<input type="checkbox" name="Dthu" {if $Dthu eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Fri{/tr}:<input type="checkbox" name="Dfri" {if $Dfri eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Sat{/tr}:<input type="checkbox" name="Dsat" {if $Dsat eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Sun{/tr}:<input type="checkbox" name="Dsun" {if $Dsun eq 'y'}checked="checked"{/if} /></td>
</tr>
</table>
</div>

<div class="simplebox">
{tr}Select ONE method for the banner{/tr}
<table border="1" width="100%">
  <tr>
  <td><input type="radio" name="use" value="useHTML" {if $use eq 'useHTML'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use HTML{/tr}
  <table>
  <tr>
  <td>{tr}HTML code{/tr}:</td>
  <td><textarea rows="5" cols="50" name="HTMLData">{$HTMLData}</textarea></td>
  </tr>
  </table>
  </td>
  </tr>
  <tr>
  <td><input type="radio" name="use" value="useImage" {if $use eq 'useImage'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use image{/tr}
  <table>
  <tr><td class="form">{tr}Image:{/tr}</td>
     <td><input type="hidden" name="imageData" value="{$imageData}" />
         <input type="hidden" name="imageName" value="{$imageName}" />
         <input type="hidden" name="imageType" value="{$imageType}" />
         <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
         <input name="userfile1" type="file" /></td>
  </tr>
  {if $hasImage eq 'y'}
  <tr><td class="form">{tr}Current Image{/tr}</td><td>
  {$imageName}: <img border="0" src="{$tempimg}" />
  </td></tr>   
  {/if}
  </table>
  </td>
  <tr>
  <tr>
  <td><input type="radio" name="use" value="useFixedURL" {if $use eq 'useFiexedURL'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use image generated by URL (the image will be requested at the URL for each impression){/tr}
  <table>
  <tr><td class="form">{tr}URL{/tr}:</td>
      <td><input type="text" name="fixedURLData" value="{$fixedURLData}" /></td>
  </tr>
  </table>
  </td>
  </tr>
  <tr>
  <td><input type="radio" name="use" value="useText" {if $use eq 'useText'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use text{/tr}
  <table>
  <tr>
  <td class="form">{tr}Text{/tr}:</td>
  <td><textarea rows="8" cols="20" name="textData">{$textData}</textarea></td>
  </tr>
  </table>
  </td>
  </tr>
</table>
</div>

<div align="center" class="simplebox">
<input type="submit" name="save" value="{tr}save the banner{/tr}" />
</div>
</form>
<br/><br/><br/><br/><br/><br/>
<div align="left" class="simplebox">
<h2{tr}>Remove Zones (you lose entered info for the banner){/tr}</h2>
{section name=ix loop=$zones}
{$zones[ix].zone}   <a class="link" href="tiki-edit_banner.php?removeZone={$zones[ix].zone}">remove</a><br/>
{/section}
</div>