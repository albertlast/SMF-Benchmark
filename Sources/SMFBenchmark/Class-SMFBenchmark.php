<?php

/**
* @package manifest file for SMF-Benchmark
* @version 1.1
* @author albertlast (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* @copyright Copyright (c) 2017
* @license BSD 3-Clause License
*/

/*
 * BSD 3-Clause License
 * 
 * Copyright (c) 2017, albertlast
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
 
if (!defined('SMF'))
	die('Hacking attempt...');

class SMFBenchmark {

	public static function addManageMaintenancePanel(&$subActions) {
		global $context;
		
		loadTemplate('SMFBenchmark');

		$context[$context['admin_menu_name']]['tab_data']['tabs']['benchmark'] = array();

		$subActions['benchmark'] =	 array(
								'function' => 'SMFBenchmark::MaintainBenchmark',
								'template' => 'maintain_Benchmark',
								'activities' => array(
									'usercreate' => 'SMFBenchmark::UserCreate',
									'postcreate' => 'SMFBenchmark::PostCreate',
									'postread' => 'SMFBenchmark::PostRead',
								),
							
						);
	}

	public static function addAdminPanel(&$areas) {
		global $txt;
		
		loadLanguage('SMFBenchmark');

		$areas['maintenance']['areas']['maintain']['subsections'] =	array_merge(
																		$areas['maintenance']['areas']['maintain']['subsections'] ,
																		array(
																			'benchmark' => array($txt['maintain_sub_benchmark'], 'admin_forum')
																		)
																	);
	}

	/**
	 * Benchmark for User creation tries to create as many as possible in 1 minute
	 * It requires the admin_forum permission.
	 * It shows as the maintain_forum admin area.
	 * It is accessed from ?action=admin;area=maintain;sa=benchmark;activity=usercreate.
	 * It also updates the optimize scheduled task such that the tables are not automatically optimized again too soon.
	 *
	 * @uses the benchmarkresult sub template
	 */
	public static function UserCreate()
	{
		global $db_prefix, $txt, $context, $smcFunc, $sourcedir;

		require_once($sourcedir . '/Subs-Members.php');

		$prefixUsername = 'UserCreateBench';
		$count = 0;
		$usersID = array();
		$start = 0;
		$maxRuntime = 60;
		$cleanupTime = 40;

		isAllowedTo('admin_forum');

		checkSession('request');

		if (!isset($_SESSION['optimized_tables']))
			validateToken('admin-maint');
		else
			validateToken('admin-optimize', 'post', false);

		ignore_user_abort(true);

		$context['page_title'] = $txt['benchmark_usercreate'];
		$context['sub_template'] = 'benchmarkresult';
		$context['continue_post_data'] = '';
		$context['continue_countdown'] = 3;

		// Try for extra time
		@set_time_limit($cleanupTime + $maxRuntime);

		$start = microtime(true);
		$end = $start + $maxRuntime;

		while (microtime(true) < $end)
		{
			$regOptions = array(
				'interface' => 'admin',
				'username' => $prefixUsername . $count,
				'email' => $prefixUsername. '_' . $count . '@' . $_SERVER['SERVER_NAME'] . (strpos($_SERVER['SERVER_NAME'], '.') === FALSE ? '.com' : ''),
				'password' => '',
				'require' => 'nothing'
			);

			$usersID[] = registerMember($regOptions);
			$count++;
		}
		
		$context['benchmark_result']['amount'] = $count;
		$context['benchmark_result']['test_name'] = $txt['benchmark_usercreate'];
		deleteMembers($usersID);
	}

	/**
	 *Dummy Function for MaintainBenchmark
	 */
	public static function MaintainBenchmark()
	{}

	/**
	 * Benchmakr for Post creation tries to create as many as possible in 1 minute
	 * It requires the admin_forum permission.
	 * It shows as the maintain_forum admin area.
	 * It is accessed from ?action=admin;area=maintain;sa=benchmark;activity=postcreate.

	 * @uses the benchmarkresult sub template
	 */
	public static function PostCreate()
	{
		global $db_prefix, $txt, $context, $smcFunc, $sourcedir;
		
		require_once($sourcedir . '/Subs-Members.php');
		require_once($sourcedir . '/Subs-Post.php');
		require_once($sourcedir . '/RemoveTopic.php');
		
		$prefixUsername = 'UserCreateBench';
		$count = 0;
		$userID = 0;
		$start = 0;
		$maxRuntime = 60;
		$cleanupTime = 40;
		$username = $prefixUsername . '_' . $count;
		$email = $username . '@' . $_SERVER['SERVER_NAME'] . (strpos($_SERVER['SERVER_NAME'], '.') === FALSE ? '.com' : '');
		$postid = 0;
		$boardid = 0;
		
		// find a board
		$request = $smcFunc['db_query']('', '
			SELECT id_board
			FROM {db_prefix}boards
			LIMIT 1',
			array()
		);

		$row = $smcFunc['db_fetch_assoc']($request);
		$boardid = $row['id_board'];

		isAllowedTo('admin_forum');

		checkSession('request');

		if (!isset($_SESSION['optimized_tables']))
			validateToken('admin-maint');
		else
			validateToken('admin-optimize', 'post', false);

		ignore_user_abort(true);

		$context['page_title'] = $txt['benchmark_usercreate'];
		$context['sub_template'] = 'benchmarkresult';
		$context['continue_post_data'] = '';
		$context['continue_countdown'] = 3;
		
		$regOptions = array(
				'interface' => 'admin',
				'username' => $prefixUsername . $count,
				'email' => $email,
				'password' => '',
				'require' => 'nothing'
			);

		$userID = registerMember($regOptions);
		
		// Try for extra time
		@set_time_limit($cleanupTime + $maxRuntime);
		
		//create the inital topic
		$msgOptions = array(
			'subject' => 'Post Benchmark Topic',
			'body' => 'nothing',
			'approved' => TRUE
		);

		$topicOptions = array(
					'board' => $boardid,
					'mark_as_read' => TRUE,
				);

		$posterOptions = array(
			'id' => $userID,
			'name' => $username,
			'email' => $email,
			'update_post_count' => TRUE,
		);

		createPost($msgOptions, $topicOptions, $posterOptions);
		
		$postid = $topicOptions['id'];
		
		$start = microtime(true);
		$end = $start + $maxRuntime;
		
		while (microtime(true) < $end)
		{
			createPost($msgOptions, $topicOptions, $posterOptions);
			$count++;
		}
		
		removeTopics($postid);
		
		$context['benchmark_result']['amount'] = $count;
		$context['benchmark_result']['test_name'] = $txt['benchmark_post'];
		
		deleteMembers($userID);
	}

	/**
	 * Benchmark for Post reads tries to access as many as possible in 1 minute
	 * It requires the admin_forum permission.
	 * It shows as the maintain_forum admin area.
	 * It is accessed from ?action=admin;area=maintain;sa=benchmark;activity=postread.
	 * It also updates the optimize scheduled task such that the tables are not automatically optimized again too soon.
	 *
	 * @uses the benchmarkresult sub template
	 */
	public static function PostRead()
	{
		global $db_prefix, $txt, $context, $smcFunc, $sourcedir, $topic, $board;

		require_once($sourcedir . '/Subs-Members.php');
		require_once($sourcedir . '/Display.php');
		require_once($sourcedir . '/Load.php');

		$prefixUsername = 'UserCreateBench';
		$count = 0;
		$usersID = array();
		$start = 0;
		$maxRuntime = 60;
		$cleanupTime = 40;
		$topicid = 0;
		$boardid = 0;

		// find a topic
		$request = $smcFunc['db_query']('', '
			SELECT id_topic, id_board
			FROM {db_prefix}topics
			LIMIT 1',
			array()
		);

		$row = $smcFunc['db_fetch_assoc']($request);
		$boardid = $row['id_board'];
		$topicid = $row['id_topic'];

		isAllowedTo('admin_forum');

		checkSession('request');

		if (!isset($_SESSION['optimized_tables']))
			validateToken('admin-maint');
		else
			validateToken('admin-optimize', 'post', false);

		ignore_user_abort(true);

		$context['page_title'] = $txt['benchmark_usercreate'];
		$context['sub_template'] = 'benchmarkresult';
		$context['continue_post_data'] = '';
		$context['continue_countdown'] = 3;

		// catch all output	
		ob_start();

		// Try for extra time
		@set_time_limit($cleanupTime + $maxRuntime);

		$start = microtime(true);
		$end = $start + $maxRuntime;

		while (microtime(true) < $end)
		{
			$topic = $topicid;
			$board = $boardid;
			loadBoard();
			Display();
			$count++;
		}

		// throw the output away
		ob_end_clean();

		$context['benchmark_result']['amount'] = $count;
		$context['benchmark_result']['test_name'] = $txt['benchmark_postread'];
		deleteMembers($usersID);
	}
}
