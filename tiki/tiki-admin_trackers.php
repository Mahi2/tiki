<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_trackers.php,v 1.8 2003-12-10 03:51:45 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');

if ($feature_trackers != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_trackers != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["trackerId"], 'tracker')) {
	$smarty->assign('individual', 'y');
}

if ($_REQUEST["trackerId"]) {
	$info = $trklib->get_tracker($_REQUEST["trackerId"]);
} else {
	$info = array();
	$info["name"] = '';
	$info["description"] = '';
	$info["showCreated"] = '';
	$info["showStatus"] = '';
	$info["showLastModif"] = '';
	$info["useComments"] = '';
	$info["useAttachments"] = '';
	$info["showComments"] = '';
	$info["showAttachments"] = '';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('showCreated', $info["showCreated"]);
$smarty->assign('showStatus', $info["showStatus"]);
$smarty->assign('showLastModif', $info["showLastModif"]);
$smarty->assign('useComments', $info["useComments"]);
$smarty->assign('useAttachments', $info["useAttachments"]);
$smarty->assign('showComments', $info["showComments"]);
$smarty->assign('showAttachments', $info["showAttachments"]);

if (isset($_REQUEST["remove"])) {
	$trklib->remove_tracker($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	if (isset($_REQUEST["showCreated"]) && $_REQUEST["showCreated"] == 'on') {
		$showCreated = 'y';
	} else {
		$showCreated = 'n';
	}

	if (isset($_REQUEST["showStatus"]) && $_REQUEST["showStatus"] == 'on') {
		$showStatus = 'y';
	} else {
		$showStatus = 'n';
	}

	if (isset($_REQUEST["useComments"]) && $_REQUEST["useComments"] == 'on') {
		$useComments = 'y';
	} else {
		$useComments = 'n';
	}

	if (isset($_REQUEST["useAttachments"]) && $_REQUEST["useAttachments"] == 'on') {
		$useAttachments = 'y';
	} else {
		$useAttachments = 'n';
	}

	if (isset($_REQUEST["showComments"]) && $_REQUEST["showComments"] == 'on') {
		$showComments = 'y';
	} else {
		$showComments = 'n';
	}

	if (isset($_REQUEST["showAttachments"]) && $_REQUEST["showAttachments"] == 'on') {
		$showAttachments = 'y';
	} else {
		$showAttachments = 'n';
	}

	if (isset($_REQUEST["showLastModif"]) && $_REQUEST["showLastModif"] == 'on') {
		$showLastModif = 'y';
	} else {
		$showLastModif = 'n';
	}

	$trklib->replace_tracker($_REQUEST["trackerId"], $_REQUEST["name"], $_REQUEST["description"], $showCreated, $showLastModif,
		$useComments, $useAttachments, $showStatus, $showComments, $showAttachments);
	$smarty->assign('trackerId', 0);
	$smarty->assign('name', '');
	$smarty->assign('description', '');
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $trklib->list_trackers($offset, $maxRecords, $sort_mode, $find);

for ($i = 0; $i < count($channels["data"]); $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["trackerId"], 'tracker')) {
		$channels["data"][$i]["individual"] = 'y';
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

// block for categorization
$cat_type = 'tracker';
$cat_objid = $_REQUEST["trackerId"];
include_once ("categorize_list.php");

// Display the template
$smarty->assign('mid', 'tiki-admin_trackers.tpl');
$smarty->display("tiki.tpl");

?>
