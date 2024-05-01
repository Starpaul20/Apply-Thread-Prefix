<?php
/**
 * Apply Thread Prefix
 * Copyright 2011 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Neat trick for caching our custom template(s)
if(defined('THIS_SCRIPT'))
{
	if(THIS_SCRIPT == 'showthread.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'showthread_moderationoptions_applyprefix';
	}

	if(THIS_SCRIPT == 'forumdisplay.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'forumdisplay_inlinemoderation_applyprefix';
	}
}

// Tell MyBB when to run the hooks
$plugins->add_hook("moderation_start", "applythreadprefix_run");
$plugins->add_hook("showthread_start", "applythreadprefix_thread");
$plugins->add_hook("forumdisplay_start", "applythreadprefix_forum");

// The information that shows up on the plugin manager
function applythreadprefix_info()
{
	global $lang;
	$lang->load("applythreadprefix", true);

	return array(
		"name"				=> $lang->applythreadprefix_info_name,
		"description"		=> $lang->applythreadprefix_info_desc,
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.3",
		"codename"			=> "applythreadprefix",
		"compatibility"		=> "18*"
	);
}

// This function runs when the plugin is activated.
function applythreadprefix_activate()
{
	global $db;
	$insert_array = array(
		'title'		=> 'moderation_applyprefix',
		'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->apply_thread_prefix}</title>
{$headerinclude}
</head>
<body>
{$header}
<form action="moderation.php" method="post">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->apply_thread_prefix}</strong></td>
</tr>
{$loginbox}
<tr>
<td class="trow1"><strong>{$lang->new_prefix}</strong><br /><span class="smalltext">{$lang->prefix_note}</span></td>
<td class="trow2">{$prefixselect}</td>
</tr>
</table>
<br />
<div align="center"><input type="submit" class="button" name="submit" value="{$lang->apply_thread_prefix}" /></div>
<input type="hidden" name="action" value="do_applyprefix" />
<input type="hidden" name="tid" value="{$tid}" />
</form>
{$footer}
</body>
</html>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'moderation_inline_applyprefix',
		'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->apply_thread_prefix}</title>
{$headerinclude}
</head>
<body>
{$header}
<form action="moderation.php" method="post">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->apply_thread_prefix}</strong></td>
</tr>
{$loginbox}
<tr>
<td class="trow1"><strong>{$lang->new_prefix}</strong><br /><span class="smalltext">{$lang->prefix_note}</span></td>
<td class="trow2">{$prefixselect}</td>
</tr>
</table>
<br />
<div align="center"><input type="submit" class="button" name="submit" value="{$lang->apply_thread_prefix}" /></div>
<input type="hidden" name="action" value="do_multiapplyprefix" />
<input type="hidden" name="fid" value="{$fid}" />
<input type="hidden" name="threads" value="{$inlineids}" />
</form>
{$footer}
</body>
</html>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'showthread_moderationoptions_applyprefix',
		'template'	=> $db->escape_string('<option class="option_mirage" value="applyprefix">{$lang->apply_thread_prefix}</option>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'forumdisplay_inlinemoderation_applyprefix',
		'template'	=> $db->escape_string('<option value="multiapplyprefix">{$lang->apply_thread_prefix}</option>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("showthread_moderationoptions_manage", "#".preg_quote('{$lang->remove_subscriptions}</option>')."#i", '{$lang->remove_subscriptions}</option>{$applyprefix}');
	find_replace_templatesets("forumdisplay_inlinemoderation_manage", "#".preg_quote('{$lang->move_threads}</option>')."#i", '{$lang->move_threads}</option>{$applyprefix}');
}

// This function runs when the plugin is deactivated.
function applythreadprefix_deactivate()
{
	global $db;
	$db->delete_query("templates", "title IN('moderation_applyprefix','moderation_inline_applyprefix','showthread_moderationoptions_applyprefix','forumdisplay_inlinemoderation_applyprefix')");

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("showthread_moderationoptions_manage", "#".preg_quote('{$applyprefix}')."#i", '', 0);
	find_replace_templatesets("forumdisplay_inlinemoderation_manage", "#".preg_quote('{$applyprefix}')."#i", '', 0);
}

// Apply Thread Prefix moderation page
function applythreadprefix_run()
{
	global $mybb, $lang, $templates, $theme, $headerinclude, $header, $footer, $moderation;
	$lang->load("applythreadprefix");

	if($mybb->input['action'] != "applyprefix" && $mybb->input['action'] != "do_applyprefix" && $mybb->input['action'] != "multiapplyprefix" && $mybb->input['action'] != "do_multiapplyprefix")
	{
		return;
	}

	if($mybb->user['uid'] != 0)
	{
		$mybb->user['username'] = htmlspecialchars_uni($mybb->user['username']);
		eval("\$loginbox = \"".$templates->get("changeuserbox")."\";");
	}
	else
	{
		eval("\$loginbox = \"".$templates->get("loginbox")."\";");
	}

	$tid = $mybb->get_input('tid', MyBB::INPUT_INT);
	$thread = get_thread($tid);

	$fid = $mybb->get_input('fid', MyBB::INPUT_INT);
	$forum = get_forum($fid);

	if($mybb->input['action'] == "applyprefix" && $mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

		if(!is_moderator($thread['fid'], "canmanagethreads"))
		{
			error_no_permission();
		}

		if(!$thread['tid'])
		{
			error($lang->error_invalidthread);
		}

		check_forum_password($thread['fid']);

		$thread['subject'] = htmlspecialchars_uni($thread['subject']); 

		build_forum_breadcrumb($thread['fid']);
		add_breadcrumb($thread['subject'], get_thread_link($thread['tid']));
		add_breadcrumb($lang->nav_apply_prefix);

		$prefixselect = build_prefix_select($thread['fid'], $mybb->get_input('threadprefix', MyBB::INPUT_INT));

		// If there are no prefixes, throw error message
		if(!$prefixselect)
		{
			error($lang->error_no_prefixes);
		}

		eval("\$applyprefix = \"".$templates->get("moderation_applyprefix")."\";");
		output_page($applyprefix);
	}

	if($mybb->input['action'] == "do_applyprefix" && $mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

		if(!is_moderator($thread['fid'], "canmanagethreads"))
		{
			error_no_permission();
		}

		$threadprefix = $mybb->get_input('threadprefix', MyBB::INPUT_INT);

		$moderation->apply_thread_prefix($thread['tid'], $threadprefix);

		log_moderator_action(array("tid" => $thread['tid'], "fid" => $thread['fid']), $lang->thread_prefix_applied);

		moderation_redirect(get_thread_link($thread['tid']), $lang->redirect_thread_prefix_applied);
	}

	if($mybb->input['action'] == "multiapplyprefix" && $mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

		if(!empty($mybb->input['searchid']))
		{
			// From search page
			$threads = getids($mybb->get_input('searchid'), 'search');
			if(!is_moderator_by_tids($threads, 'canmanagethreads'))
			{
				error_no_permission();
			}
		}
		else
		{
			$threads = getids($forum['fid'], 'forum');
			if(!is_moderator($forum['fid'], 'canmanagethreads'))
			{
				error_no_permission();
			}
		}

		if(count($threads) < 1)
		{
			error($lang->error_inline_nothreadsselected);
		}

		$inlineids = implode("|", $threads);
		if($mybb->get_input('inlinetype') == 'search')
		{
			clearinline($mybb->get_input('searchid', MyBB::INPUT_INT), 'search');
		}
		else
		{
			clearinline($forum['fid'], 'forum');
		}

		check_forum_password($forum['fid']);

		build_forum_breadcrumb($forum['fid']);
		add_breadcrumb($lang->nav_apply_prefix);

		$prefixselect = build_prefix_select($forum['fid'], $mybb->get_input('threadprefix', MyBB::INPUT_INT));

		// If there are no prefixes, throw error message
		if(!$prefixselect)
		{
			error($lang->error_no_prefixes);
		}

		eval("\$multiapplyprefix = \"".$templates->get("moderation_inline_applyprefix")."\";");
		output_page($multiapplyprefix);
	}

	if($mybb->input['action'] == "do_multiapplyprefix" && $mybb->request_method == "post")
	{
		// Verify incoming POST request
		verify_post_check($mybb->get_input('my_post_key'));

		$threadlist = explode("|", $mybb->get_input('threads'));
		if(!is_moderator_by_tids($threadlist, "canmanagethreads"))
		{
			error_no_permission();
		}
		foreach($threadlist as $tid)
		{
			$tlist[] = (int)$tid;
		}

		$threadprefix = $mybb->get_input('threadprefix', MyBB::INPUT_INT);

		$moderation->apply_thread_prefix($tlist, $threadprefix);

		log_moderator_action(array("fid" => $forum['fid']), $lang->thread_prefix_applied);

		moderation_redirect(get_forum_link($forum['fid']), $lang->redirect_inline_thread_prefix_applied);
	}
	exit;
}

// Add option to show thread mod menu
function applythreadprefix_thread()
{
	global $lang, $templates, $applyprefix, $thread;
	$lang->load("applythreadprefix");

	$applyprefix = '';
	if(is_moderator($thread['fid'], 'canmanagethreads'))
	{
		$prefixes = false;
		$prefix_cache = build_prefixes(0);

		// Go through each of our prefixes and decide which ones we can use
		if(!empty($prefix_cache))
		{
			foreach($prefix_cache as $prefix)
			{
				if($prefix['forums'] != "-1")
				{
					// Decide whether this prefix can be used in our forum
					$forums = explode(",", $prefix['forums']);

					if(!in_array($thread['fid'], $forums))
					{
						// This prefix is not in our forum list
						continue;
					}
				}

				if(is_member($prefix['groups']))
				{
					// The current user can use this prefix
					$prefixes = true;
				}
			}
		}

		if($prefixes == true)
		{
			eval("\$applyprefix = \"".$templates->get("showthread_moderationoptions_applyprefix")."\";");
		}
	}
}

// Add option to forum display mod menu
function applythreadprefix_forum()
{
	global $mybb, $lang, $templates, $applyprefix;
	$lang->load("applythreadprefix");

	$fid = $mybb->get_input('fid', MyBB::INPUT_INT);

	$applyprefix = '';
	if(is_moderator($fid, 'canmanagethreads'))
	{
		$prefixes = false;
		$prefix_cache = build_prefixes(0);

		// Go through each of our prefixes and decide which ones we can use
		if(!empty($prefix_cache))
		{
			foreach($prefix_cache as $prefix)
			{
				if($prefix['forums'] != "-1")
				{
					// Decide whether this prefix can be used in our forum
					$forums = explode(",", $prefix['forums']);

					if(!in_array($fid, $forums))
					{
						// This prefix is not in our forum list
						continue;
					}
				}

				if(is_member($prefix['groups']))
				{
					// The current user can use this prefix
					$prefixes = true;
				}
			}
		}

		if($prefixes == true)
		{
			eval("\$applyprefix = \"".$templates->get("forumdisplay_inlinemoderation_applyprefix")."\";");
		}
	}
}
