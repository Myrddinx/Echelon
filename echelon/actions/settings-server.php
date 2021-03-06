<?php
$auth_name = 'manage_settings';
require '../inc.php';

## Check that the form was posted and that the user did not just stumble here ##
if(!isset($_POST['server-settings-sub'])) :
	set_error('Please do not call that page directly, thank you.');
	send('../index.php');
endif;

## What type of request is it ##
if($_POST['type'] == 'add')
	$is_add = true;
elseif($_POST['type'] == 'edit')
	$is_add = false;
else
	sendBack('Missing Data');

## Check Token ##
if($is_add) { // if add server request
	if(verifyFormToken('addserver', $tokens) == false) // verify token
		ifTokenBad('Add Server');
} else { // if edit server settings
	if(verifyFormToken('editserversettings', $tokens) == false) // verify token
		ifTokenBad('Server Settings Edit');
}

## Get Vars ##
$name = cleanvar($_POST['name']);
$ip = cleanvar($_POST['ip']);
$pb = cleanvar($_POST['pb']);
// DB Vars
$rcon_ip = cleanvar($_POST['rcon-ip']);
$rcon_port = cleanvar($_POST['rcon-port']);
$rcon_pw_cng = cleanvar($_POST['cng-pw']);
$rcon_pw = cleanvar($_POST['rcon-pass']);
$server_id = cleanvar($_POST['server']);

if($is_add)
	$game_id = cleanvar($_POST['game-id']);

// Whether to change RCON PW or not
if($rcon_pw_cng == 'on')
	$change_rcon_pw = true;
else
	$change_rcon_pw = false;
	
// Whether to change DB PW or not
if($pb == 'on')
	$pb = 1;
else
	$pb = 0;

## Check for empty vars ##
emptyInput($name, 'server name');
emptyInput($ip, 'server IP');
emptyInput($rcon_ip, 'Rcon IP');
emptyInput($rcon_port, 'Rcon Port');
if($change_rcon_pw == true)
	emptyInput($rcon_pw, 'Rcon password');

// check that the rcon_ip is valid
$rcon_ip = strtolower($rcon_ip);
if( (!filter_var($rcon_ip, FILTER_VALIDATE_IP)) || ($rcon_ip == 'localhost') )
	sendBack('That Rcon IP Address is not valid, localhost can also be used');
	
// check that the rcon_ip is valid
$ip = strtolower($ip);
if( (!filter_var($ip, FILTER_VALIDATE_IP)) || ($ip == 'localhost') )
	sendBack('That server IP Address is not valid, localhost can also be used');
	
// Check Port is a number between 4-5 digits
if( (!is_numeric($rcon_port)) || (!preg_match('/^[0-9]{4,5}$/', $rcon_port)) )
	sendBack('Rcon Port must be a number between 4-5 digits');

if($is_add) : // if is add server request
	if(!is_numeric($game_id)) // game_id is a digit
		sendBack('Invalid data sent');
endif;
	
## Update DB ##
if($is_add)
	$result = $dbl->addServer($game_id, $name, $ip, $pb, $rcon_ip, $rcon_port, $rcon_pw);
	$dbl->addServerUpdateGames($game_id);
else
	$result = $dbl->setServerSettings($server_id, $name, $ip, $pb, $rcon_ip, $rcon_port, $rcon_pw, $change_rcon_pw); // update the settings in the DB

if($result == false)
	sendBack('Something did not update');

## Return ##
if($is_add)
	send('../settings-server.php');
else
	sendGood('Your settings have been updated');
