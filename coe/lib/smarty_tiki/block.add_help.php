<?php
/*
 * \brief Add help via icon to a page
 * @author: Stéphane Casset
 * @date: 06/11/2008
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 * $Id$
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_add_help($params, $content, &$smarty, &$repeat) {
	global $prefs;
	global $help_sections;

	if (!isset($content)) return ;
	
	if (isset($params['title'])) $section['title'] = $params['title'];
	if (isset($params['id'])) {
		$section['id'] = $params['id'];
	} else {
		$section['id'] = $params['id'] = 'help_section_'.sizeof($help_sections);
	}
	$section['content'] = $content;

	$help_sections[$params['id']] = $section;

	if (!isset($params['show']) or $params['show'] == 'y') {
		global $headerlib;
		$headerlib->include_jquery_ui();
		require_once $smarty->_get_plugin_filepath('block', 'self_link');
		$self_link_params['alt'] = $params['title'];
		$self_link_params['_icon'] = 'help';
		$self_link_params['_ajax'] = 'n';
		
		$headerlib->add_js('
function openEditHelp() {
	var opts, edithelp_pos = getCookie("edithelp_position");
	opts = { width: 460, height: 500, title: "' . $section['title'] . '", autoOpen: false, beforeclose: function(event, ui) {
		var off = $jq(this).offsetParent().offset();
   		setCookie("edithelp_position", parseInt(off.left) + "," + parseInt(off.top) + "," + $jq(this).offsetParent().width() + "," + $jq(this).offsetParent().height());
	}}
	if (edithelp_pos) {edithelp_pos = edithelp_pos.split(",");}
	if (edithelp_pos && edithelp_pos.length) {
		opts["position"] = [parseInt(edithelp_pos[0]), parseInt(edithelp_pos[1])];
		opts["width"] = parseInt(edithelp_pos[2]);
		opts["height"] = parseInt(edithelp_pos[3]);
	}
	$jq("#help_sections").dialog("destroy").dialog(opts).dialog("open");
	
};');
		$self_link_params['_onclick'] = 'openEditHelp();return false;';
 
		return smarty_block_self_link($self_link_params,"",$smarty);
	} else {
		return ;
	}
}