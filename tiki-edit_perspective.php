<?php
// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'offset' => 'int',
		'id' => 'int',
		'name' => 'striptags',
		'create' => 'alpha',
		'action' => 'alpha',
		'criteria' => 'striptags',
	),
	'staticKeyFiltersForArrays' => array(
		'lm_preference' => 'word',
	),
	'catchAllUnset' => null,
) );

$auto_query_args = array( 'offset', 'id', 'cookietab' );
$section='admin';

require_once('tiki-setup.php');
require_once('lib/perspectivelib.php');

$access->check_feature( array('feature_perspective', 'feature_jquery_ui') );

$selectedId = 0;

if( isset( $_REQUEST['id'] ) ) {
	$selectedId = $_REQUEST['id'];
	$objectperms = Perms::get( array( 'type' => 'perspective', 'object' => $_REQUEST['id'] ) );
}

if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'remove' && $selectedId && $objectperms->perspective_admin ) {
	check_ticket( 'remove_perspective' );

	$perspectivelib->remove_perspective( $selectedId );
	$selectedId = 0;
}

// Edit perspective
if( isset( $_REQUEST['name'] ) && $selectedId && $objectperms->perspective_edit ) {
	global $prefslib; require_once 'lib/prefslib.php';
	$perspectivelib->replace_perspective( $selectedId, $_REQUEST['name'] );

	$preferences = $_REQUEST['lm_preference'];
	$input = $prefslib->getInput( $jitRequest, $preferences, 'perspective' );

	$perspectivelib->replace_preferences( $selectedId, $input );
}

// Create perspective
if( isset( $_REQUEST['create'], $_REQUEST['name'] ) && $globalperms->create_perspective ) {
	$name = trim( $_REQUEST['name'] );

	if( ! empty( $name ) ) {
		$selectedId = $perspectivelib->replace_perspective( null, $name );
	}
}

$maxRecords = $prefs['maxRecords'];
$offset = isset( $_REQUEST['offset'] ) ? $_REQUEST['offset'] : 0;
$smarty->assign( 'offset', $offset );
$smarty->assign( 'count', $tikilib->getOne( 'SELECT COUNT(*) FROM tiki_perspectives' ) );

$perspectives = $perspectivelib->list_perspectives( $offset, $maxRecords );

if( $selectedId ) {
	$info = $perspectivelib->get_perspective( $selectedId );

	$smarty->assign( 'perspective_info', $info );

	if( isset( $_REQUEST['criteria'] ) ) {
		global $prefslib; require_once 'lib/prefslib.php';
		require_once 'lib/smarty_tiki/function.preference.php';

		$criteria = $_REQUEST['criteria'];
		$results = $prefslib->getMatchingPreferences( $criteria );
		$results = array_diff( $results, array_keys( $info['preferences'] ) );

		foreach( $results as $name ) {
			echo smarty_function_preference( array(
				'name' => $name,
			), $smarty );
		}

		exit;
	}
}

$smarty->assign( 'perspectives', $perspectives );
$smarty->assign( 'mid', 'tiki-edit_perspective.tpl' );
$smarty->display( 'tiki.tpl' );
