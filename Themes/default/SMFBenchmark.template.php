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

/**
 * Tempalte for the benchmark maintenance tasks.
 */
function template_maintain_benchmark()
{
	global $context, $txt, $scripturl;

	// If maintenance has finished tell the user.
	if (!empty($context['maintenance_finished']))
		echo '
			<div class="infobox">
				', sprintf($txt['maintain_done'], $context['maintenance_finished']), '
			</div>';

	echo '
	<div id="manage_maintenance">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['benchmark_topic'], '</h3>
		</div>
		<div class="windowbg2 noup">
			<form action="', $scripturl, '?action=admin;area=maintain;sa=benchmark;activity=usercreate" method="post" accept-charset="', $context['character_set'], '">
				<p>', $txt['benchmark_usercreate_info'], '</p>
				<input type="submit" value="', $txt['maintain_run_now'], '" class="button_submit">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-maint_token_var'], '" value="', $context['admin-maint_token'], '">
			</form>
		</div>
		<div class="windowbg2 noup">
			<form action="', $scripturl, '?action=admin;area=maintain;sa=benchmark;activity=postcreate" method="post" accept-charset="', $context['character_set'], '">
				<p>', $txt['benchmark_post_info'], '</p>
				<input type="submit" value="', $txt['maintain_run_now'], '" class="button_submit">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-maint_token_var'], '" value="', $context['admin-maint_token'], '">
			</form>
		</div>
		<div class="windowbg2 noup">
			<form action="', $scripturl, '?action=admin;area=maintain;sa=benchmark;activity=postread" method="post" accept-charset="', $context['character_set'], '">
				<p>', $txt['benchmark_postread_info'], '</p>
				<input type="submit" value="', $txt['maintain_run_now'], '" class="button_submit">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-maint_token_var'], '" value="', $context['admin-maint_token'], '">
			</form>
		</div>';

	echo '
	</div>';
}

/**
 * Simple template for showing results of our benchmark...
 */
function template_benchmarkresult()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="manage_maintenance">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['benchmark_result'], '</h3>
		</div>
		<div class="windowbg">
			<p>';
	
	echo $context['benchmark_result']['test_name'] . ':' . $context['benchmark_result']['amount'];

	echo '
			</p>
			<p><a href="', $scripturl, '?action=admin;area=maintain">', $txt['maintain_return'], '</a></p>
		</div>
	</div>';
}

?>