<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//
// $Id$


/**
 * Tree Table Smarty func - smarty_function_treetable()
 * Renders a tree table (for use with http://plugins.jquery.com/project/treeTable)
 * 
 * Params
 * 
 * _data	:	array of data rows	 - 	e.g . with perms for now
 * 
 * 	array(
 * 		array('permName'=>'tiki_p_admin_newsletters', 'permDesc' => 'Can admin newsletters','level' => 'admin', 'type' => 'newsletters' etc...),
 * 		array('permName'=>'tiki_p_blahblah', etc...),
 * 	...)
 * 
 * _columns	: array of columns and headers array('permName' => tra('Permission Name'), 'permDesc' => tra('Permission Description'), etc
 * 				or a string like: '"permName"="Permission Name", "permDesc"="Description", etc'
 * 				if undefined it tries to guess (?)
 * 
 * _valueColumnIndex = 0	:	index of the col in the _data array above to use as the unique index
 * 
 * _sortColumn = ''			:	column to organise tree by (actually row key = e.g. 'type')
 * 
 * _checkbox = ''			: 	name of checkbox (auto-incrementing) - no checkboxes if not set
 * 								if comma delimited list (or array) then makes multiple checkboxes
 * 
 * _checkboxColumnIndex = 0	:	index of the col in the _data array above to use as the checkbox value
 * 								comma delimeted list (or array - of ints) for multiple checkboxes as set above
 * 								if set needs to match number of checkboxes defines in _checkbox (or if not set uses 0,1,2 etc)
 * 
 * _checkboxTitles = ''		:	Comma delimited list (or array) of header titles for checkboxes (optional, but needs to match number of checkboxes above)
 * 
 * _listFilter = 'y'		:	include dynamic text filter
 * 
 * _filterMinRows = 12		:	don't show filter box if less than this number of rows
 * 
 * class = 'treeTable'		:	class of the table - will add 'sortable' if feature_jquery_sortable = y
 * id = 'treetable1'		:	id of the table (auto-incrementing)
 * 
 * _rowClasses = array('odd','even')	:	classes to cycle through for rows (tr's and td's)
 * 											can be a string for same class on each row
 * 											or empty string for not
 * 
 * _emptyDataMessage = {treetable}: '.tra('No rows found')	: message if there are no rows
 * 
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_treetable($params, &$smarty) {
	global $headerlib, $tree_table_id, $prefs;
	
	extract($params);
	
	$_emptyDataMessage = empty($_emptyDataMessage) ? '{treetable}: ' . tra('No rows found') : $_emptyDataMessage;
	if (empty($_data)) {
		return $_emptyDataMessage;
	}
	
	$_checkbox = empty($_checkbox) ? '' : $_checkbox;
	$_checkboxTitles = empty($_checkboxTitles) ? '' : $_checkboxTitles;
	
	if (is_string($_checkbox) && strpos($_checkbox, ',') !== false) {
		$_checkbox = split(',', trim($_checkbox));
	}
	if (!empty($_checkboxColumnIndex)) {
		if (is_string($_checkboxColumnIndex) && strpos($_checkboxColumnIndex, ',') !== false) {
			$_checkboxColumnIndex = split(',', trim($_checkboxColumnIndex));
		}
		if (count($_checkbox) != count($_checkboxColumnIndex)) {
			return tra('{treetable}: Number of items in _checkboxColumnIndex doesn not match items in _checkbox');
		}
	}
	if (!empty($_checkboxTitles)) {
		if (is_string($_checkboxTitles) && strpos($_checkboxTitles, ',') !== false) {
			$_checkboxTitles = split(',', trim($_checkboxTitles));
		}
		if (count($_checkbox) != count($_checkboxTitles)) {
			return tra('{treetable}: Number of items in _checkboxTitles doesn not match items in _checkbox');
		}
	}
	$_checkboxColumnIndex = empty($_checkboxColumnIndex) ? 0 : $_checkboxColumnIndex;
	$_valueColumnIndex = empty($_valueColumnIndex) ? 0 : $_valueColumnIndex;
	
	if (!empty($_checkbox) && !is_array($_checkbox)) {
			$_checkbox = array($_checkbox);
			$_checkboxColumnIndex = array($_checkboxColumnIndex);
	}
	
	$html = '';
	$nl = "\n";
	
	// some defaults
	$_listFilter = empty($_listFilter) ? 'y' : $_listFilter;
	$_filterMinRows = empty($_filterMinRows) ? 12 : $_filterMinRows;
	$_rowClasses = !isset($_rowClasses) ? array('odd','even') : 
		(is_array($_rowClasses) ? $_rowClasses : array($_rowClasses));
	
	if (!empty($_rowClasses)) {
		$rowCounter = 0;
	} else {
		$rowCounter = -1;
	}
	
	// auto-increment val for unique id's etc
	if (empty($id)) {
		if (!isset($tree_table_id)) {
			$tree_table_id = 1;
		} else {
			$tree_table_id++;
		}
		$id = 'treetable_'. $tree_table_id;
	} else {
		$id;
	}
	// TODO - check this? add key/val pairs?
	if (empty($_columns)) {
		$keys =  array_keys($_data[0]);
		$_columns = array();
		foreach($keys as $key) {
			if (!is_numeric($key)) {
				$_columns[$key] = htmlentities($key);
			}
		}
	} else if (is_string($_columns)) {
		$ar = split(',', $_columns);
		$_columns = array();
		foreach ($ar as $str) {
			$ar2 = split('=', trim($str));
			$_columns[trim($ar2[0],' "')] = trim($ar2[1],' "');
		}
		unset($ar, $ar2);
	}
	
	$_sortColumn = empty($_sortColumn) ? '' : $_sortColumn;
	
	if ($_sortColumn) {
		sort2d($_data, $_sortColumn);
		$headerlib->add_jq_onready('$jq("#'.$id.'").treeTable({clickableNodeNames:true});');
		// TODO refilter when .parent is opened - seems to prevent the click propagating
//		$headerlib->add_jq_onready('$jq("tr.parent").click(function(event) {
//if ($jq("#'.$id.'_filter").val()) {
//	$jq("#'.$id.'_filter").trigger("keyup");
//	if (event.isPropagationStopped() || event.isImmediatePropagationStopped()) {
//		$jq(this).trigger("click");
//	}
//}
//		});');
	}
	
	$class = empty($class) ? 'treeTable' : $class;	// treetable
	
	if ($prefs['feature_jquery_tablesorter'] == 'y' && strpos($class, 'sortable') === false) {
		 //$class .= ' sortable';
	}
	
	if ($_listFilter == 'y' && count($_data) > $_filterMinRows) {
		include_once('lib/smarty_tiki/function.listfilter.php');
		$html .= smarty_function_listfilter(
			array('id' => $id.'_filter',
				  'selectors' => "#$id tbody tr:not(.parent)",
				  'parentSelector' => "#$id tbody .parent",
				  'exclude' => ".subHeader"),  $smarty);
	}
	
	// start writing the table
	$html .= $nl.'<table id="'.$id.'" class="'.$class.'">'.$nl;
	
	// write the table header
	$html .= '<thead><tr>';
	if (!empty($_checkbox)) {
		include_once('lib/smarty_tiki/function.select_all.php');
		for ($i = 0; $i < count($_checkbox); $i++) {
			$html .= '<th class="checkBoxHeader">';
			$html .= smarty_function_select_all(
						array('checkbox_names'=>$_checkbox[$i].'[]',
							  'label' => empty($_checkboxTitles) ? '' : $_checkboxTitles[$i]), $smarty);
			$html .= '</th>';
		}
	}
	
	foreach ($_columns as $column => $columnName) {
		$html .= '<th>';
		$html .= htmlentities($columnName);
		$html .= '</th>';
	}
	$html .= '</tr></thead>'.$nl;
	$html .= '<tbody>'.$nl;
	
	$treeColumnsAdded = array();
	
		// for each row
	foreach ($_data as &$row) {
		// set up tree hierarchy
		if ($_sortColumn) {
			$treeType = htmlentities($row[$_sortColumn]);
			$treeTypeId = preg_replace('/\s+/', '_', $treeType);
			$childRowClass = ' child-of-'.$id.'_'.$treeTypeId;
			
			if (!in_array($treeTypeId, $treeColumnsAdded)) {
				$html .= '<tr id="'.$id.'_'.$treeTypeId.'"><td colspan="'.(count($_columns) + count($_checkbox)).'">';
				$html .= $treeType.'</td></tr>'.$nl;
				$treeColumnsAdded[] = $treeTypeId;
				
				// write a sub-header
				$html .= '<tr class="subHeader'.$childRowClass.'">';
				if (!empty($_checkbox)) {
					for ($i = 0; $i < count($_checkbox); $i++) {
						$html .= '<td class="checkBoxHeader">';
						$html .= empty($_checkboxTitles) ? '' : $_checkboxTitles[$i];
						$html .= '</td>';
					}
				}
				foreach ($_columns as $column => $columnName) {
					$html .= '<td>';
					$html .= htmlentities($columnName);
					$html .= '</td>';
				}
				$html .= '</tr>'.$nl;
			}
		} else {
			$rowId = '';
			$childRowClass = '';
		}
		
		// work out row class (odd/even etc)
		if ($rowCounter > -1) {
			$rowClass = $_rowClasses[$rowCounter].$childRowClass;
			$rowCounter++;
			if ($rowCounter >= count($_rowClasses)) { $rowCounter = 0; }
		} else {
			$rowClass = $childRowClass;
		}
		
		$html .= '<tr class="'.$rowClass.'">';
		// add the checkbox
		if (!empty($_checkbox)) {
			for ($i = 0; $i < count($_checkbox); $i++) {
				// get checkbox's "value"
				$cbxVal = htmlentities($row[$_checkboxColumnIndex[$i]]);
				$rowVal = htmlentities($row[$_valueColumnIndex]);
				$cbxTit = empty($_checkboxTitles) ? $cbxVal : $_checkboxTitles[$i];
				$html .= '<td class="checkBoxCell">';
				$html .= '<input type="checkbox" name="'.$_checkbox[$i].'[]" value="'.$rowVal.'"'.($cbxVal=='y' ? ' checked=checked' : '').' title="'.$cbxTit.'" />';
				if ($cbxVal == 'y') {
					$html .= '<input type="hidden" name="old_'.$_checkbox[$i].'[]" value="'.$rowVal.'" />';
				}
				$html .= '</td>';
			}
		}
		
		foreach ($_columns as $column => $columnName) {
			$html .= '<td>';
			$html .= htmlentities($row[$column]);
			$html .= '</td>'.$nl;
		}
		$html .= '</tr>'.$nl;					
	}
	$html .= '</tbody></table>'.$nl;
		
	return $html;

}


// TODO - move this somewhere sensible
// from http://uk.php.net/sort?

// $sort used as variable function--can be natcasesort, for example
// WARNING: $sort must be associative
function sort2d( &$arrIn, $index = null, $sort = 'asort') {
	// pseudo-secure--never allow user input into $sort
	if (strpos($sort, 'sort') === false) {$sort = 'asort';}
	$arrTemp = Array();
	$arrOut = Array();
	foreach ( $arrIn as $key=>$value ) {
		$arrTemp[$key] = is_null($index) ? reset($value) : $value[$index];
	}
	$sort($arrTemp);
	foreach ( $arrTemp as $key=>$value ) {
		$arrOut[$key] = $arrIn[$key];
	}
	$arrIn = $arrOut;
}
