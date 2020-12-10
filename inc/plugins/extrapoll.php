<?php
/**
 * Extra Poll
 * https://github.com/martec
 *
 * Copyright (C) 2020-2020, Martec
 *
 * Extra Poll is licensed under the GPL Version 3, 29 June 2007 license:
 *	http://www.gnu.org/copyleft/gpl.html
 *
 * @fileoverview Extra Poll
 * @author Martec
 * @requires Mybb
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define('EP_PLUGIN_VER', '0.2.2');
defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT . 'inc/plugins/pluginlibrary.php');
define('EP_PLUGIN_PATH', __DIR__ . '/extra_poll');

function extrapoll_info()
{
	global $db, $lang;

	$lang->load('config_extrapoll');

	$EP_description = <<<EOF
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
{$lang->extrapoll_plug_desc}
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/pt_BR/i/scr/pixel.gif" width="1" height="1">
</form>
EOF;

	return array(
		'name'			=> 'Extra Poll',
		'description'	=> $EP_description,
		'website'		=> 'https://github.com/martec/Extra_Poll',
		'author'		=> 'martec',
		'authorsite'	=> 'http://community.mybb.com/user-49058.html',
		'version'		=> EP_PLUGIN_VER,
		'codename'		=> 'extrapoll',
		'compatibility' => '18*'
	);

}

function extrapoll_install()
{
	global $db, $lang, $mybb;

	if(!$db->field_exists('expoll', 'threads'))
	{
		$db->query("ALTER TABLE ".TABLE_PREFIX."threads ADD `expoll` text NOT NULL");
	}

	if(!$db->table_exists("polls2")) {
		$db->query("CREATE TABLE ".TABLE_PREFIX."polls2 (
					pid int unsigned NOT NULL auto_increment,
					tid int unsigned NOT NULL default '0',
					question varchar(200) NOT NULL default '',
					dateline int unsigned NOT NULL default '0',
					options text NOT NULL,
					votes text NOT NULL,
					numoptions smallint unsigned NOT NULL default '0',
					numvotes int unsigned NOT NULL default '0',
					timeout int unsigned NOT NULL default '0',
					closed tinyint(1) NOT NULL default '0',
					multiple tinyint(1) NOT NULL default '0',
					public tinyint(1) NOT NULL default '0',
					maxoptions smallint unsigned NOT NULL default '0',
					KEY tid (tid),
					PRIMARY KEY (pid)
				) ENGINE=MyISAM
				".$db->build_create_table_collation().";");
	}
	if(!$db->table_exists("pollvotes2")) {
		$db->query("CREATE TABLE ".TABLE_PREFIX."pollvotes2 (
					vid int unsigned NOT NULL auto_increment,
					pid int unsigned NOT NULL default '0',
					uid int unsigned NOT NULL default '0',
					voteoption smallint unsigned NOT NULL default '0',
					dateline int unsigned NOT NULL default '0',
					ipaddress varbinary(16) NOT NULL default '',
					KEY pid (pid, uid),
					PRIMARY KEY (vid)
				) ENGINE=MyISAM
				".$db->build_create_table_collation().";");
	}
}

function extrapoll_is_installed()
{
	global $db;

	return $db->table_exists('polls2');
}

function extrapoll_uninstall()
{
	global $db, $PL;

    if (!file_exists(PLUGINLIBRARY)) {
        flash_message('This plugin requires PluginLibrary, please ensure it is installed correctly.', 'error');
        admin_redirect('index.php?module=config-plugins');
    }

    $PL or require_once PLUGINLIBRARY;

	if($db->field_exists('expoll', 'threads')) {
		$db->query("ALTER TABLE ".TABLE_PREFIX."threads DROP column `expoll`");
	}

	if($db->table_exists("polls2")) {
		$db->drop_table("polls2");
	}
	if($db->table_exists("pollvotes2")) {
		$db->drop_table("pollvotes2");
	}

	$PL->templates_delete('expolls');

}

function extrapoll_activate()
{
    global $PL, $cache, $db;

    if (!file_exists(PLUGINLIBRARY)) {
        flash_message('This plugin requires PluginLibrary, please ensure it is installed correctly.', 'error');
        admin_redirect('index.php?module=config-plugins');
    }

    $PL or require_once PLUGINLIBRARY;

    if ($PL->version < 9) {
        flash_message('This plugin requires PluginLibrary 9 or newer', 'error');
        admin_redirect('index.php?module=config-plugins');
    }

    if (is_dir(EP_PLUGIN_PATH . '/templates')) {
        $dir = new DirectoryIterator(EP_PLUGIN_PATH . '/templates');
        $templates = [];
        foreach ($dir as $file) {
            if (!$file->isDot() && !$file->isDir() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'html') {
                $templates[$file->getBasename('.html')] = file_get_contents($file->getPathName());
            }
        }

        $PL->templates(
            'expolls',
            'Extra Poll',
            $templates
        );
    }

	include_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('{$addpoll}') . '#i',
		'{$addpoll}{$addexpoll}'
	);

}

function extrapoll_deactivate()
{
	global $db;

	include_once MYBB_ROOT."inc/adminfunctions_templates.php";

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('{$addpoll}{$addexpoll}') . '#i',
		'{$addpoll}'
	);

}

$plugins->add_hook('global_start', 'ep_cache');
function ep_cache()
{
	global $templatelist, $mybb, $settings;

	if (!isset($templatelist) || empty($templatelist)) {
		 $templatelist = '';
	}

	$templatelist .= ',';

 	if (THIS_SCRIPT == 'showthread.php') {
		$templatelist .= 'expolls_poll,expolls_poll_editpoll,expolls_poll_option,expolls_poll_option_multiple,expolls_poll_resultbit,expolls_poll_results,expolls_poll_undovote,expolls_add_poll';
	}
}

$plugins->add_hook('showthread_end', 'ep_showthread_poll');
function ep_showthread_poll()
{
	global $thread, $poll, $forumpermissions, $forum, $fid, $mybb, $templates, $lang, $tid, $addexpoll, $cache, $pollbox;

	if (!isset($lang->extrapoll)) {
		$lang->load('extrapoll');
	}

	$addexpoll = '';
	$ex_poll = my_unserialize($thread['expoll']);

	if ($ex_poll) {
		//create extra poll in showthread
		foreach($ex_poll as $ex_poll_pid => $ex_poll_tid) {
			$pollbox .= load_ep($ex_poll_pid, $ex_poll_tid);
		}
	}

	if ($thread['poll']) {
		// Display 'add extra poll' link to thread creator (or mods)
		$ismod = false;
		if (is_moderator($fid)) {
			$ismod = true;
		}

		$time = TIME_NOW;
		if(($thread['uid'] == $mybb->user['uid'] || $ismod == true) && $forumpermissions['canpostpolls'] == 1 && $forum['open'] != 0 && $thread['closed'] != 1 && ($ismod == true || $thread['dateline'] > ($time-($mybb->settings['polltimelimit']*60*60)) || $mybb->settings['polltimelimit'] == 0))
		{
			eval("\$addexpoll = \"".$templates->get("expolls_add_poll")."\";");
		}
	}
}

function load_ep (&$pid, &$tid) {

	global $db, $mybb, $templates, $lang, $parser, $plugins, $theme;

	if ($tid == 0) {
		xmlhttp_error();
	}

	$thread = get_thread((int)$tid);

	$forumpermissions = forum_permissions($thread['fid']);
	$fid = $thread['fid'];
	$mybb->post_code = generate_post_check();

	// Does the thread belong to a valid forum?
	$forum = get_forum($fid);
	if(!$forum || $forum['type'] != "f") {
		xmlhttp_error();
	}

	if (!isset($lang->showthread)) {
		$lang->load("showthread");
	}

	if (!isset($lang->extrapoll)) {
		$lang->load('extrapoll');
	}

	$options = array(
		"limit" => 1
	);
	$query = $db->simple_select("polls2", "*", "pid='".$pid."'", $options);
	$poll = $db->fetch_array($query);
	$poll['timeout'] = $poll['timeout']*60*60*24;
	$expiretime = $poll['dateline'] + $poll['timeout'];
	$now = TIME_NOW;

	// If the poll or the thread is closed or if the poll is expired, show the results.
	if($poll['closed'] == 1 || $thread['closed'] == 1 || ($expiretime < $now && $poll['timeout'] > 0) || $forumpermissions['canvotepolls'] != 1)
	{
		$showresults = 1;
	}

	if($forumpermissions['canvotepolls'] != 1)
	{
		$nopermission = 1;
	}

	// Check if the user has voted before...
	if($mybb->user['uid'])
	{
		$user_check = "uid='{$mybb->user['uid']}'";
	}
	else
	{
		$user_check = "uid='0' AND ipaddress=".$db->escape_binary($session->packedip);
	}

	$query = $db->simple_select("pollvotes2", "*", "{$user_check} AND pid='".$pid."'");
	while($votecheck = $db->fetch_array($query))
	{
		$alreadyvoted = 1;
		$votedfor[$votecheck['voteoption']] = 1;
	}

	$optionsarray = explode("||~|~||", $poll['options']);
	$votesarray = explode("||~|~||", $poll['votes']);
	$poll['question'] = htmlspecialchars_uni($poll['question']);
	$polloptions = '';
	$totalvotes = 0;
	$poll['totvotes'] = 0;

	for($i = 1; $i <= $poll['numoptions']; ++$i)
	{
		$poll['totvotes'] = $poll['totvotes'] + $votesarray[$i-1];
	}

	// Loop through the poll options.
	for($i = 1; $i <= $poll['numoptions']; ++$i)
	{
		// Set up the parser options.
		$parser_options = array(
			"allow_html" => $forum['allowhtml'],
			"allow_mycode" => $forum['allowmycode'],
			"allow_smilies" => $forum['allowsmilies'],
			"allow_imgcode" => $forum['allowimgcode'],
			"allow_videocode" => $forum['allowvideocode'],
			"filter_badwords" => 1
		);

		if($mybb->user['showimages'] != 1 && $mybb->user['uid'] != 0 || $mybb->settings['guestimages'] != 1 && $mybb->user['uid'] == 0)
		{
			$parser_options['allow_imgcode'] = 0;
		}

		if($mybb->user['showvideos'] != 1 && $mybb->user['uid'] != 0 || $mybb->settings['guestvideos'] != 1 && $mybb->user['uid'] == 0)
		{
			$parser_options['allow_videocode'] = 0;
		}

		$option = $parser->parse_message($optionsarray[$i-1], $parser_options);
		$votes = $votesarray[$i-1];
		$totalvotes += $votes;
		$number = $i;

		// Mark the option the user voted for.
		if(!empty($votedfor[$number]))
		{
			$optionbg = "trow2";
			$votestar = "*";
		}
		else
		{
			$optionbg = "trow1";
			$votestar = "";
		}

		// If the user already voted or if the results need to be shown, do so; else show voting screen.
		if(isset($alreadyvoted) || isset($showresults))
		{
			if((int)$votes == "0")
			{
				$percent = "0";
			}
			else
			{
				$percent = number_format($votes / $poll['totvotes'] * 100, 2);
			}
			$imagewidth = round($percent);
			eval("\$polloptions .= \"".$templates->get("expolls_poll_resultbit")."\";");
		}
		else
		{
			if($poll['multiple'] == 1)
			{
				eval("\$polloptions .= \"".$templates->get("expolls_poll_option_multiple")."\";");
			}
			else
			{
				eval("\$polloptions .= \"".$templates->get("expolls_poll_option")."\";");
			}
		}
	}

	// If there are any votes at all, all votes together will be 100%; if there are no votes, all votes together will be 0%.
	if($poll['totvotes'])
	{
		$totpercent = "100%";
	}
	else
	{
		$totpercent = "0%";
	}

	// Check if user is allowed to edit posts; if so, show "edit poll" link.
	$edit_poll = '';
	if(is_moderator($fid, 'canmanagepolls'))
	{
		eval("\$edit_poll = \"".$templates->get("expolls_poll_editpoll")."\";");
	}

	// Decide what poll status to show depending on the status of the poll and whether or not the user voted already.
	if(isset($alreadyvoted) || isset($showresults) || isset($nopermission))
	{
		if($alreadyvoted)
		{
			$pollstatus = $lang->already_voted;

			$undovote = '';
			if($mybb->usergroup['canundovotes'] == 1)
			{
				eval("\$undovote = \"".$templates->get("expolls_poll_undovote")."\";");
			}
		}
		elseif($nopermission)
		{
			$pollstatus = $lang->no_voting_permission;
		}
		else
		{
			$pollstatus = $lang->poll_closed;
		}

		$lang->total_votes = $lang->sprintf($lang->extrapoll_total_votes, $totalvotes);
		eval("\$pollbox = \"".$templates->get("expolls_poll_results")."\";");
		$plugins->run_hooks("expolls_poll_results");
	}
	else
	{
		$closeon = '&nbsp;';
		if($poll['timeout'] != 0)
		{
			$closeon = $lang->sprintf($lang->poll_closes, my_date($mybb->settings['dateformat'], $expiretime));
		}

		$publicnote = '&nbsp;';
		if($poll['public'] == 1)
		{
			$publicnote = $lang->public_note;
		}

		eval("\$pollbox = \"".$templates->get("expolls_poll")."\";");
		$plugins->run_hooks("expolls_poll");
	}

	return $pollbox;
}

/**
 * Completely rebuild extra poll counters for a particular poll (useful if they become out of sync)
 *
 * @param int $pid The poll ID
 */
function rebuild_extra_poll_counters($pid)
{
	global $db;

	$query = $db->simple_select("polls2", "pid, numoptions", "pid='".(int)$pid."'");
	$poll = $db->fetch_array($query);

	$votes = array();
	$query = $db->simple_select("pollvotes2", "voteoption, COUNT(vid) AS vote_count", "pid='{$poll['pid']}'", array('group_by' => 'voteoption'));
	while($vote = $db->fetch_array($query))
	{
		$votes[$vote['voteoption']] = $vote['vote_count'];
	}

	$voteslist = '';
	$numvotes = 0;
	for($i = 1; $i <= $poll['numoptions']; ++$i)
	{
		if(trim($voteslist != ''))
		{
			$voteslist .= "||~|~||";
		}

		if(!isset($votes[$i]) || (int)$votes[$i] <= 0)
		{
			$votes[$i] = "0";
		}
		$voteslist .= $votes[$i];
		$numvotes = $numvotes + $votes[$i];
	}

	$updatedpoll = array(
		"votes" => $db->escape_string($voteslist),
		"numvotes" => (int)$numvotes
	);
	$db->update_query("polls2", $updatedpoll, "pid='{$poll['pid']}'");
}

/**
 * Rebuild extra poll counters
 */
$plugins->add_hook("admin_tools_recount_rebuild", "acp_rebuild_extra_poll_counters");
function acp_rebuild_extra_poll_counters()
{
	global $db, $mybb, $cache, $lang;

	if (!isset($lang->tools_recount_rebuild)) {
		$lang->load("tools_recount_rebuild");
	}

	if($mybb->request_method == "post") {

		if(!isset($mybb->input['page']) || $mybb->get_input('page', MyBB::INPUT_INT) < 1)
		{
			$mybb->input['page'] = 1;
		}

		if(isset($mybb->input['do_rebuildexpollcounters'])) {

			if($mybb->input['page'] == 1)
			{
				// Log admin action
				log_admin_action("poll");
			}

			$per_page = $mybb->get_input('expollcounters', MyBB::INPUT_INT);
			if(!$per_page || $per_page <= 0)
			{
				$mybb->input['expollcounters'] = 500;
			}

			$query = $db->simple_select("polls2", "COUNT(*) as num_polls");
			$num_polls = $db->fetch_field($query, 'num_polls');

			$page = $mybb->get_input('page', MyBB::INPUT_INT);
			$per_page = $mybb->get_input('expollcounters', MyBB::INPUT_INT);

			$start = ($page-1) * $per_page;
			$end = $start + $per_page;

			$query = $db->simple_select("polls2", "pid", '', array('order_by' => 'pid', 'order_dir' => 'asc', 'limit_start' => $start, 'limit' => $per_page));
			while($poll = $db->fetch_array($query))
			{
				rebuild_extra_poll_counters($poll['pid']);
			}

			check_proceed($num_polls, $end, ++$page, $per_page, "expollcounters", "do_rebuildexpollcounters", $lang->success_rebuilt_poll_counters);
		}
	}
}

$plugins->add_hook("admin_tools_recount_rebuild_output_list", "acp_rebuild_extra_poll_counters_form");
function acp_rebuild_extra_poll_counters_form() {

	global $lang, $form_container, $form;

	if (!isset($lang->tools_recount_rebuild)) {
		$lang->load("tools_recount_rebuild");
	}
	if (!isset($lang->config_extrapoll)) {
		$lang->load("config_extrapoll");
	}

	$form_container->output_cell("<label>{$lang->extrapoll_rebuild_poll_counters}</label><div class=\"description\">{$lang->rebuild_poll_counters_desc}</div>");
	$form_container->output_cell($form->generate_numeric_field("expollcounters", 500, array('style' => 'width: 150px;', 'min' => 0)));
	$form_container->output_cell($form->generate_submit_button($lang->go, array("name" => "do_rebuildexpollcounters")));
	$form_container->construct_row();
}
?>