<?php

include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_most_visited_forums($module_rows);
$smarty->assign('modForumsMostVisitedForums', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>