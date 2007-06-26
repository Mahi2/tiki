<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}




    $pref_toggles_Colors = array('ProgressBarPlay','ProgressBarLoad','ProgressBarButton','ProgressBar','VolumeOn','VolumeOff','VolumeButton',
	'Button','ButtonPressed','ButtonOver','ButtonInfo','ButtonInfoPressed','ButtonInfoOver','ButtonInfoText','ID3','PlayTime',
	'TotalTime','PanelDisplay','AlertMesg');
    $pref_toggles_Values = array('PreloadDelay','VideoHeight','VideoLength','URLAppend','LimitedMsg',
	'MaxPlay');


   
   $xmlmm=fopen('multiplayer.xml','w');
   fwrite ( $xmlmm,'<interface name="default">'."\n");
   fwrite ( $xmlmm,'	<Color>'."\n");

   foreach ($pref_toggles_Colors as $toggle) {
	$value=${$toggle};
	$hexavalue=str_replace("#","0x",$value);
	if (!isset($_REQUEST[$toggle])) $_REQUEST[$toggle] = ${$toggle};
      $tikilib->set_preference($toggle,  $_REQUEST[$toggle]);
      $smarty->assign($toggle, $_REQUEST[$toggle]);
     fwrite ( $xmlmm,"		<$toggle>$hexavalue</$toggle>"."\n");
}
    fwrite ( $xmlmm,'	</Color>'."\n");
    fwrite ( $xmlmm,'	<Value>'."\n");

   foreach ($pref_toggles_Values as $toggle) {
	if (!isset($_REQUEST[$toggle])) $_REQUEST[$toggle] = ${$toggle};
      $tikilib->set_preference($toggle,  $_REQUEST[$toggle]);
      $smarty->assign($toggle, $_REQUEST[$toggle]);
      if ( ${$toggle} ) fwrite ( $xmlmm,"		<$toggle>${$toggle}</$toggle>"."\n");
}
    fwrite ( $xmlmm,'	</Value>'."\n");
    fwrite ( $xmlmm,'</interface>'."\n");

    fclose ($xmlmm);

?>
