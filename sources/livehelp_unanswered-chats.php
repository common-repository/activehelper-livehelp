<?php
/**
 * @package ActiveHelper Live Help
 * @version   : 3.6
 * @author    : ActiveHelper Inc.
 * @copyright : (C) 2010- ActiveHelper Inc.
 * @license   : GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

if (!defined('ACTIVEHELPER_LIVEHELP'))
	die('Hi there! I\'m just a plugin, not much I can do when called directly.');

function activeHelper_liveHelp_unansweredChats()
{
	global $wpdb, $activeHelper_liveHelp;

	$actions = array(
		'list' => 'activeHelper_liveHelp_unansweredChatsList'
	);
	if (!empty($_REQUEST['action']) && isset($actions[$_REQUEST['action']]))
		return $actions[$_REQUEST['action']]();

	return $actions['list']();
}

function activeHelper_liveHelp_unansweredChatsPost()
{
	$actions = array(
		'export' => 'activeHelper_liveHelp_unansweredChatsExportPost',
		'list' => 'activeHelper_liveHelp_unansweredChatsListPost'
	);

	if (!empty($_REQUEST['action']) && isset($actions[$_REQUEST['action']]))
		return $actions[$_REQUEST['action']]();

	if (empty($_REQUEST['action']))
		return $actions['list']();
}

function activeHelper_liveHelp_unansweredChatsExportPost()
{
	global $wpdb, $activeHelper_liveHelp;

	$timeEnd = !empty($_REQUEST['export_end_date']) ? strtotime((string) $_REQUEST['export_end_date']) : time();
	$timeStart = !empty($_REQUEST['export_start_date']) ? strtotime((string) $_REQUEST['export_start_date']) : mktime(0, 0, 0, date("n", $timeEnd) - 1, date("j", $timeEnd), date("Y", $timeEnd));

	$unansweredChatsList = $wpdb->get_results("
	SELECT jls.id , jls.username, jls.email , jls.server , jls.department , jls.phone, DATE_FORMAT(jls.datetime ,'%d-%m-%Y') as date
		FROM
			{$wpdb->prefix}livehelp_sessions AS jls,
			{$wpdb->prefix}livehelp_messages AS jlm
		WHERE
			DATE_FORMAT(jls.datetime, '%Y%m%d') >= DATE_FORMAT('" . date("Y-m-d", $timeStart) . "', '%Y%m%d')
			AND DATE_FORMAT(jls.datetime, '%Y%m%d') <= DATE_FORMAT('" . date("Y-m-d", $timeEnd) . "', '%Y%m%d')
			AND jls.active = 0 and jls.id not in ( jlm.session )
           group by jls.id 
		   order by jls.id desc", ARRAY_A);
  
	$export = '"id","username","email","department","server","Date"';

	if (!empty($unansweredChatsList))
		foreach ($unansweredChatsList as $unansweredChats)
		{
			$export .= "\n" . '"' .
				$unansweredChats['id'] . '","' .
				$unansweredChats['username'] . '","' .
				$unansweredChats['email'] . '","' .
				$unansweredChats['department'] . '","' .
				$unansweredChats['server'] . '","' .
                $unansweredChats['phone'] . '","' .
				$unansweredChats['date'] .
			'"';
		}

	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=LiveHelp_unansweredChats_" . date("Y-m-d", $timeStart) . "~" . date("Y-m-d", $timeEnd) . ".csv");
	header("Content-Type: text/csv; text/comma-separated-values");
	header("Content-Transfer-Encoding: binary");

	echo $export;
	exit();
}

function activeHelper_liveHelp_unansweredChatsListPost()
{
	global $wpdb, $activeHelper_liveHelp;

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker', $activeHelper_liveHelp['baseUrl'] . '/scripts/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
	wp_enqueue_style('jquery.ui.theme', $activeHelper_liveHelp['baseUrl'] . '/scripts/smoothness/jquery-ui-1.8.16.custom.css');
}

function activeHelper_liveHelp_unansweredChatsList()
{
	global $wpdb, $activeHelper_liveHelp;

	$timeEnd = !empty($_REQUEST['export_end_date']) ? strtotime((string) $_REQUEST['export_end_date']) : time();
	$timeStart = !empty($_REQUEST['export_start_date']) ? strtotime((string) $_REQUEST['export_start_date']) : mktime(0, 0, 0, date("n", $timeEnd) - 1, date("j", $timeEnd), date("Y", $timeEnd));

	$unansweredChatsList = $wpdb->get_results("
			SELECT
			jls.id, jls.username , jls.email , jls.server , jls.department , jls.phone, DATE_FORMAT(jls.datetime ,'%d-%m-%Y') as date
		FROM
			{$wpdb->prefix}livehelp_sessions AS jls,
			{$wpdb->prefix}livehelp_messages AS jlm
		WHERE
			DATE_FORMAT(jls.datetime, '%Y%m%d') >= DATE_FORMAT('" . date("Y-m-d", $timeStart) . "', '%Y%m%d')
			AND DATE_FORMAT(jls.datetime, '%Y%m%d') <= DATE_FORMAT('" . date("Y-m-d", $timeEnd) . "', '%Y%m%d')
			AND jls.active = 0 and jls.id not in ( jlm.session )
    group by jls.id 
		order by jls.id desc
	", ARRAY_A);

	echo '
<div class="wrap"><form action="admin.php?page=' . strtolower('activeHelper_liveHelp_unansweredChats') . '" method="post" accept-charset="utf-8" id="activeHelper_liveHelp_form">
	<h2 style="padding-right: 0;">
		LiveHelp » ' . __('unanswered chats', 'activehelper_livehelp') . '
		<a class="button add-new-h2" href="admin.php?page=' . strtolower('activeHelper_liveHelp_unansweredChats') . '&amp;action=export&amp;type=csv" id="activeHelper_liveHelp_export">' . __('export to CSV', 'activehelper_livehelp') . '</a>
	</h2>';

	echo '
	<div class="metabox-holder" style="padding-bottom: 10px;">
		<div id="normal-sortables" class="meta-box-sortables ui-sortable">
			<div class="stuffbox postbox">
				<div class="inside" style="padding-top: 1ex; padding-bottom: 1ex;">
					<div style="float: left; height: 26px; line-height: 26px;">
						<label for="export_start_date">' . __('Start date', 'activehelper_livehelp') . '</labe>
					</div>
					<div style="padding-left: 1ex; float: left; height: 26px; line-height: 26px;">
						<input readonly="readonly" style="background: #fff; cursor: pointer;" tabindex="6" maxlength="255" type="text" style="width: 140px;" value="' . date("Y-m-d", $timeStart) . '" id="export_start_date" name="export_start_date" />
					</div>
					<div style="padding-left: 1ex; float: left; height: 26px; line-height: 26px;">
						<label for="export_end_date">' . __('End date', 'activehelper_livehelp') . '</labe>
					</div>
					<div style="padding-left: 1ex; float: left; height: 26px; line-height: 26px;">
						<input readonly="readonly" style="background: #fff; cursor: pointer;" tabindex="6" maxlength="255" type="text" style="width: 140px;" value="' . date("Y-m-d", $timeEnd) . '" id="export_end_date" name="export_end_date" />
					</div>
					<div style="padding-left: 1ex; float: left; height: 26px; line-height: 26px;">
						<input name="submit" value="' . __('Apply', 'activehelper_livehelp') . '" type="submit" accesskey="p" tabindex="4" class="button-primary">
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
		</div>
		<table cellspacing="0" class="wp-list-table widefat fixed">
			<thead>
				<tr>
					<th style="width: 50px" class="manage-column" scope="col">
						' . __('ID', 'activehelper_livehelp') . '</th>
					<th style="width: 25%" class="manage-column" scope="col">
						' . __('Username', 'activehelper_livehelp') . '</th>
					<th style="width: 25%" class="manage-column" scope="col">
						' . __('Email', 'activehelper_livehelp') . '</th>
					<th style="width: 25%" class="manage-column" scope="col">
						' . __('Department', 'activehelper_livehelp') . '</th>
					<th style="width: 25%" class="manage-column" scope="col">
						' . __('Domain name', 'activehelper_livehelp') . '</th>
	                <th style="width: 85px" class="manage-column" scope="col">
						' . __('Phone', 'activehelper_livehelp') . '</th>                        
					<th style="width: 85px" class="manage-column" scope="col">
						' . __('Date', 'activehelper_livehelp') . '</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="manage-column" scope="col">
						' . __('ID', 'activehelper_livehelp') . '</th>
					<th class="manage-column" scope="col">
						' . __('Username', 'activehelper_livehelp') . '</th>
					<th class="manage-column" scope="col">
						' . __('Email', 'activehelper_livehelp') . '</th>
					<th class="manage-column" scope="col">
						' . __('Department', 'activehelper_livehelp') . '</th>
					<th class="manage-column" scope="col">
						' . __('Domain name', 'activehelper_livehelp') . '</th>
			        <th class="manage-column" scope="col">
						' . __('Phone', 'activehelper_livehelp') . '</th>                        
					<th class="manage-column" scope="col">
						' . __('Date', 'activehelper_livehelp') . '</th>
				</tr>
			</tfoot>
			<tbody id="the-list">';

			if (empty($unansweredChatsList))
				echo '
				<tr valign="top" class="format-default">
					<td class="colspanchange" colspan="6"><p style="margin: 0; padding: .8ex; color: #888;">
						' . sprintf(__('No %s found.', 'activehelper_livehelp'), __('chats', 'activehelper_livehelp')) . '
					</p></td>
				</tr>';
			else
			{
				$alternate = false;
				foreach ($unansweredChatsList as $unansweredChats)
				{
					echo '
				<tr valign="top" class="' . ($alternate ? 'alternate' : '') . ' format-default">
					<td style="padding: 1ex;">
						' . $unansweredChats['id'] . '
					</td>
					<td style="padding: 1ex;" class="post-title page-title column-title">
						<strong>' . $unansweredChats['username'] . '</strong>
					</td>
					<td style="padding: 1ex;">
						' . $unansweredChats['email'] . '
					</td>
					<td style="padding: 1ex;">
						' . $unansweredChats['department'] . '
					</td>
					<td style="padding: 1ex;">
						' . $unansweredChats['server'] . '
					</td>
	               <td style="padding: 1ex;">
						' . $unansweredChats['phone'] . '
					</td>                    
					<td style="padding: 1ex;">
						' . $unansweredChats['date'] . '
					</td>
				</tr>';

					$alternate = !$alternate;
				}
			}

			echo '
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
		var export_start_date = "' . date("Y-m-d", $timeStart) . '";
		var export_end_date = "' . date("Y-m-d", $timeEnd) . '";

		jQuery(document).ready(function($){
			$("#export_end_date").datepicker({
				dateFormat : "yy-mm-dd",
				defaultDate: 0,
				maxDate: "+0d",
				onSelect: function(date){
					export_end_date = date;
					$("#export_start_date").datepicker("option", "maxDate", date);
				}
			});
			$("#export_start_date").datepicker({
				dateFormat : "yy-mm-dd",
				defaultDate: 0,
				maxDate: export_end_date,
				onSelect: function(date){
					export_start_date = date;
				}
			});

			$("#activeHelper_liveHelp_export").click(function(){
				$(this).attr("href", $(this).attr("href") + "&export_start_date="
					+ export_start_date + "&export_end_date=" + export_end_date);
			});
		});
	</script>
</form></div>';
}

