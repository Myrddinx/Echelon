<?php
$page = "client";
$page_title = "Clients Listing";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "id";
$order = "ASC";

$is_search = false;

//$limit_rows = 75; // limit_rows can be set by the DB settings // uncomment this line to manually overide the number of table rows per page

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('id', 'name', 'connections', 'group_bits', 'time_add', 'time_edit');
// Check if the sent varible is in the allowed array 
if(!in_array($orderby, $allowed_orderby))
	$orderby = 'id'; // if not just set to default id

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;

## Search Request handling ##
if($_GET['s']) {
	$search_string = addslashes($_GET['s']);
	$is_search = true; // this is then a search page
}

if($_GET['t']) {
	$search_type = $_GET['t']; //  no need to escape it will be checked off whitelist
	$allowed_search_type = array('all', 'alias', 'pbid', 'ip', 'id');
	if(!in_array($search_type, $allowed_search_type))
		$search_type = 'all'; // if not just set to default all
}


###########################
######### QUERIES #########

$query = "SELECT c.id, c.name, c.connections, c.time_edit, c.time_add, g.name as level
			FROM clients c LEFT JOIN groups g
			ON c.group_bits = g.id WHERE c.id > 1 ";

if($is_search == true) : // IF SEARCH
	if($search_type == 'name') { // ALIAS
		$query .= sprintf("AND c.name LIKE '%%%s%%' ORDER BY %s", $search_string, $orderby);
		
	} elseif($search_type == 'id') { // ID
		$query .= sprintf("AND c.id LIKE '%%%s%%' ORDER BY %s", $search_string, $orderby);
		
	} elseif($search_type == 'pbid') { // PBID
		$query .= sprintf("AND c.pbid LIKE '%%%s%%' ORDER BY %s", $search_string, $orderby);
		
	} elseif($search_type == 'ip') { // IP
		$query .= sprintf("AND c.ip LIKE '%%%s%%' ORDER BY %s", $search_string, $orderby);
		
	} else { // ALL
		$query .= sprintf("AND c.name LIKE '%%%s%%' OR c.pbid LIKE '%%%s%%' OR c.ip LIKE '%%%s%%' OR c.id LIKE '%%%s%%'
			ORDER BY %s", $search_string, $search_string, $search_string, $search_string, $orderby);
	}
else : // IF NOT SEARCH
	$query .= sprintf("ORDER BY %s ", $orderby);

endif; // end if search request

## Append this section to all queries since it is the same for all ##
if($order == "desc") {
	$query .= " DESC"; // set to desc 
} else {
	$query .= " ASC"; // default to ASC if nothing adds up
}

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Require Header ##	
require 'inc/header.php';

if(!$db->error) :
?>

<fieldset class="search">
	<legend>Client Search</legend>
	<form action="clients.php" method="get" id="c-search">
	
		<img src="images/indicator.gif" alt="Loading...." title="We are searching for posible matches, please wait" id="c-s-load" />
	
		<input type="text" autocomplete="off" name="s" id="search" onkeyup="suggest(this.value);" onBlur="fill();" value="<?php echo $search_string; ?>" />
		
		<div class="suggestionsBox" id="suggestions" style="display: none;">
			<div class="suggestionList" id="suggestionsList">&nbsp;</div>
		</div>
		
		<select name="t">
			<option value="all" <?php if($search_type == "all") echo 'selected="selected"' ?>>All</option>
			<option value="alias" <?php if($search_type == "names") echo 'selected="selected"' ?>>Name</option>
			<option value="pbid" <?php if($search_type == "pbid") echo 'selected="selected"' ?>>PBID</option>
			<option value="ip" <?php if($search_type == "ip") echo 'selected="selected"' ?>>IP Address</option>
			<option value="id" <?php if($search_type == "id") echo 'selected="selected"' ?>>Player ID</option>
		</select>
		
		<input type="submit" id="sub-search" value="Search" />
	</form>
</fieldset>

<table summary="A list of <?php echo limit_rows; ?> players who have connected to the server at one time or another.">
	<caption>Client Listings
		<small>
			<?php
			if($search_type == "all") {
				echo 'You are searching all clients that match <strong>'.$search_string.'</strong>.';
			} elseif($search_type == 'alias') {
				echo 'You are searching all clients names for <strong>'.$search_string.'</strong>.';
			} elseif($search_type == 'pbid') {
				echo 'You are searching all clients Punkbuster Guids for <strong>'.$search_string.'</strong>.';
			} elseif($search_type == 'id') {
				echo 'You are searching all clients B3 IDs for <strong>'.$search_string.'</strong>.';
			} elseif($search_type == 'ip') {
				echo 'You are searching all clients IP addresses for <strong>'.$search_string.'</strong>.';
			} else {
				echo 'A list of all players who have ever connected to the server.';
			}
			?>
		</small>
	</caption>
	<thead>
		<tr>
			<th>Name
				<?php linkSortClients('name', 'Name', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Client-id
				<?php linkSortClients('id', 'Client-id', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Level
				<?php linkSortClients('group_bits', 'Level', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Connections
				<?php linkSortClients('connections', 'Connections', $is_search, $search_type, $search_string); ?>
			</th>
			<th>First Seen
				<?php linkSortClients('time_add', 'First Seen', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Last Seen
				<?php linkSortClients('time_edit', 'Last Seen', $is_search, $search_type, $search_string); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6">Click client name to see details</th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$rowcolor = 0;

	 if($num_rows > 0) { // query contains stuff
	 
		foreach($data_set as $client): // get data from query and loop
			$cid = $client['id'];
			$name = $client['name'];
			$level = $client['level'];
			$connections = $client['connections'];
			$time_edit = $client['time_edit'];
			$time_add = $client['time_add'];
			
			$time_add = date($tformat, $time_add);
			$time_edit = date($tformat, $time_edit);
			$rowcolor = 1 - $rowcolor;
			
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
				
			$client = clientLink($name, $cid);
			
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong>$client</strong></td>
				<td>@$cid</td>
				<td>$level</td>
				<td>$connections</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
			</tr>
EOD;

		echo $data;
		endforeach;
	} else {
		$no_data = true;
	
		echo '<tr class="odd"><td colspan="6">';
		if($is_search == false)
			echo 'There are no clients in the database.';
		else
			echo 'Your search for <strong>'.$search_string.'</strong> has returned no results.';
		echo '</td></tr>';
	} // end if query contains
	?>
	</tbody>
</table>

<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>