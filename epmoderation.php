<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'epmoderation.php');

require_once "./global.php";
require_once MYBB_ROOT."inc/functions.php";
require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;

// Load global language phrases
$lang->load("moderation");

$plugins->run_hooks("expolls_mod_start");

$pid = $mybb->get_input('pid', MyBB::INPUT_INT);

//check poll id in extra poll table
if($pid)
{
	$query = $db->simple_select("polls2", "pid,tid", "pid='$pid'");
	$poll = $db->fetch_array($query);
	if(!$poll)
	{
		error($lang->error_invalidpoll, $lang->error);
	}
	$tid = $poll['tid'];
}

if($tid)
{
	$thread = get_thread($tid);
	if(!$thread)
	{
		error($lang->error_invalidthread, $lang->error);
	}
	$fid = $thread['fid'];
}

if($fid)
{
	$modlogdata['fid'] = $fid;
	$forum = get_forum($fid);

	// Make navigation
	build_forum_breadcrumb($fid);

	// Get our permissions all nice and setup
	$permissions = forum_permissions($fid);
}

// Get some navigation if we need it
$mybb->input['action'] = $mybb->get_input('action');

if(isset($thread))
{
	$thread['subject'] = htmlspecialchars_uni($parser->parse_badwords($thread['subject']));
	add_breadcrumb($thread['subject'], get_thread_link($thread['tid']));
	$modlogdata['tid'] = $thread['tid'];
}

if(isset($forum))
{
	// Check if this forum is password protected and we have a valid password
	check_forum_password($forum['fid']);
}

// Begin!
switch($mybb->input['action'])
{
	// Delete the actual poll here!
	case "do_deletepoll":

		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

		if($thread['visible'] == -1)
		{
			error($lang->error_thread_deleted, $lang->error);
		}

		if(!isset($mybb->input['delete']))
		{
			error($lang->redirect_pollnotdeleted);
		}
		if(!is_moderator($fid, "canmanagepolls"))
		{
			if($permissions['candeletethreads'] != 1 || $mybb->user['uid'] != $thread['uid'])
			{
				error_no_permission();
			}
		}

		$plugins->run_hooks("expolls_mod_do_deletepoll");

		$lang->poll_deleted = $lang->sprintf($lang->poll_deleted, $thread['subject']);
		log_moderator_action($modlogdata, $lang->poll_deleted);

		ep_delete_poll($poll['pid']);

		$ep_arrays = array();

		$query = $db->simple_select('polls2', 'pid, tid', "tid='".$thread['tid']."'");
		while($ep_array = $db->fetch_array($query))
		{
			$ep_arrays[$ep_array['pid']] = $ep_array['tid'];
		}

		$dbep_arrays = $db->escape_string(my_serialize($ep_arrays));

		$db->update_query("threads", array('expoll' => $dbep_arrays), "tid='".$thread['tid']."'");

		moderation_redirect(get_thread_link($tid), $lang->redirect_polldeleted);
		break;
}


/**
 * Special redirect that takes a return URL into account
 * @param string $url URL
 * @param string $message Message
 * @param string $title Title
 */
function moderation_redirect($url, $message="", $title="")
{
	global $mybb;
	if(!empty($mybb->input['url']))
	{
		$url = htmlentities($mybb->input['url']);
	}

	if(my_strpos($url, $mybb->settings['bburl'].'/') !== 0)
	{
		if(my_strpos($url, '/') === 0)
		{
			$url = my_substr($url, 1);
		}
		$url_segments = explode('/', $url);
		$url = $mybb->settings['bburl'].'/'.end($url_segments);
	}

	redirect($url, $message, $title);
}

/**
 * Delete a poll
 *
 * @param int $pid Poll id
 * @return boolean
 */
function ep_delete_poll($pid)
{
	global $db;

	$pid = (int)$pid;

	if(empty($pid))
	{
		return false;
	}

	$db->delete_query("polls2", "pid='$pid'");
	$db->delete_query("pollvotes2", "pid='$pid'");

	return true;
}