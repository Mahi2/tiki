<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_include_info()
{
	return [
		'name' => tra('Include'),
		'documentation' => 'PluginInclude',
		'description' => tra('Include a portion of another wiki page'),
		'prefs' => ['wikiplugin_include'],
		'iconname' => 'copy',
		'introduced' => 1,
		'tags' => [ 'basic' ],
		'format' => 'html',
		'params' => [
			'page' => [
				'required' => true,
				'name' => tra('Page Name'),
				'description' => tra('Wiki page name to include.'),
				'since' => '1',
				'filter' => 'pagename',
				'default' => '',
				'profile_reference' => 'wiki_page',
			],
			'start' => [
				'required' => false,
				'name' => tra('Start'),
				'description' => tra('When only a portion of the page should be included, specify the marker from which
					inclusion should start.'),
				'since' => '1',
				'default' => '',
			],
			'stop' => [
				'required' => false,
				'name' => tra('Stop'),
				'description' => tra('When only a portion of the page should be included, specify the marker at which
					inclusion should end.'),
				'since' => '1',
				'default' => '',
			],
			'nopage_text' => [
				'required' => false,
				'name' => tra('Nopage Text'),
				'description' => tra('Text to show when no page is found.'),
				'since' => '6.0',
				'filter' => 'text',
				'default' => '',
			],
			'pagedenied_text' => [
				'required' => false,
				'name' => tra('Page Denied Text'),
				'description' => tra('Text to show when the page exists but is denied to the user.'),
				'since' => '6.0',
				'filter' => 'text',
				'default' => '',
			],
            'pagenotapproved_text' => [
				'required' => false,
				'name' => tra('Page Denied Text'),
				'description' => tra('Text to show when the page exists but no version is approved.'),
				'since' => '18.0',
				'filter' => 'text',
				'default' => '',
            ],
			'page_edit_icon' => [
				'required' => false,
				'name' => tra('Edit Icon'),
				'description' => tra('Option to show the edit icon for the included page (shown by default).'),
				'since' => '12.1',
				'default' => 'y',
				'filter' => 'alpha',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
		],
	];
}

function wikiplugin_include($dataIn, $params)
{
	global $user, $killtoc, $prefs;
	static $included_pages, $data;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');

	$killtoc = true;
	$max_times = 5;
	$params = array_merge([
		'nopage_text' => '',
		'pagedenied_text' => '',
		'page_edit_icon' => 'y',
		'pagenotapproved_text' => tra('There are no approved versions of this page.'), 
	], $params);
	extract($params, EXTR_SKIP);
	if (! isset($page)) {
		return ("<b>missing page for plugin INCLUDE</b><br />");
	}

	// This variable is for accessing included page name within plugins in that page
	global $wikiplugin_included_page;
	$wikiplugin_included_page = $page;

	$memo = $page;
	if (isset($start)) {
		$memo .= "/$start";
	}
	if (isset($end)) {
		$memo .= "/$end";
	}
	if (isset($included_pages[$memo])) {
		if ($included_pages[$memo] >= $max_times) {
			return '';
		}
		$included_pages[$memo]++;
	} else {
		$included_pages[$memo] = 1;
		// only evaluate permission the first time round
		// evaluate if object or system permissions enables user to see the included page
		if ($prefs['flaggedrev_approval'] != 'y') {
			$data[$memo] = $tikilib->get_page_info($page);
		} else {
			$flaggedrevisionlib = TikiLib::lib('flaggedrevision');
			if ($flaggedrevisionlib->page_requires_approval($page)) {
				if ($version_info = $flaggedrevisionlib->get_version_with($page, 'moderation', 'OK')) {
					$data[$memo] = $version_info;
				} else {
					$included_pages[$memo] = $max_times;
					return($pagenotapproved_text);
				}
			} else {
				$data[$memo] = $tikilib->get_page_info($page);
			}
		}
		if (! $data[$memo]) {
			$text = $nopage_text;
		}
		$perms = $tikilib->get_perm_object($page, 'wiki page', $data[$memo], false);
		if ($perms['tiki_p_view'] != 'y') {
			$included_pages[$memo] = $max_times;
			$text = $pagedenied_text;
			return($text);
		}
	}

	if ($data[$memo]) {
		$text = $data[$memo]['data'];
		if (isset($start) || isset($stop)) {
			$explText = explode("\n", $text);
			if (isset($start) && isset($stop)) {
				$state = 0;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Searching for start marker, dropping lines until found
						unset($explText[$i]);	// Drop the line
						if (0 == strcmp($start, trim($line))) {
							$state = 1;	// Start retaining lines and searching for stop marker
						}
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($stop, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Go back to looking for start marker
						}
					}
				}
			} elseif (isset($start)) {
				// Only start marker is set. Search for it, dropping all lines until
				// it is found.
				foreach ($explText as $i => $line) {
					unset($explText[$i]); // Drop the line
					if (0 == strcmp($start, trim($line))) {
						break;
					}
				}
			} else {
				// Only stop marker is set. Search for it, dropping all lines after
				// it is found.
				$state = 1;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Dropping lines
						unset($explText[$i]);
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($stop, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Start dropping lines
						}
					}
				}
			}
			$text = implode("\n", $explText);
		}
	}

	$parserlib = TikiLib::lib('parser');
	$old_options = $parserlib->option;
	$options = [
		'is_html' => $data[$memo]['is_html'],
		'suppress_icons' => true,
	];
	if (! empty($_REQUEST['page'])) {
		$options['page'] = $_REQUEST['page'];
	}
	$parserlib->setOptions($options);
	$parserlib->parse_wiki_argvariable($text);
	$text = $parserlib->parse_data($text, $options);
	$parserlib->setOptions($old_options);

	// append a "Read more" link at end of text if only a portion of page is being included
	if (isset($start) || isset($stop)) {
	    $text .= '<p><a href="tiki-index.php?page='.urlencode($page).'" class="btn btn-default">'.smarty_function_icon(['name' => 'align-left'], $smarty).' '.tra("Read more...").'</a><p>';
	}

	// append an edit button if page_edit_icon does not equal 'n'
	if ($page_edit_icon != 'n') {
		if (isset($perms) && $perms['tiki_p_edit'] === 'y' && strpos($_SERVER['PHP_SELF'], 'tiki-send_newsletters.php') === false) {
			$smarty = TikiLib::lib('smarty');
			$smarty->loadPlugin('smarty_block_ajax_href');
			$smarty->loadPlugin('smarty_function_icon');
			$tip = tra('Include Plugin') . ' | ' . tra('Edit the included page:') . ' &quot;' . $page . '&quot;';
			$returnto = ! empty($GLOBALS['page']) ? $GLOBALS['page'] : $_SERVER['REQUEST_URI'];
			if (empty($_REQUEST['display']) || $_REQUEST['display'] != 'pdf') {
				$text .= '<a class="editplugin tips" ' . // ironically smarty_block_self_link doesn't work for this! ;)
				smarty_block_ajax_href(['template' => 'tiki-editpage.tpl'], 'tiki-editpage.php?page=' . urlencode($page) . '&returnto=' . urlencode($returnto), $smarty, $tmp = false) . '>' .
				smarty_function_icon([ '_id' => 'page_edit', 'title' => $tip, 'class' => 'icon tips'], $smarty) . '</a>';
			}
		}
	}
	return $text;
}
