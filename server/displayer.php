<?php
include_once('import/constants.php');

if (isset($_REQUEST['DOMAINID'])){
  $domainId = (int) $_REQUEST['DOMAINID'];
}

// RTL Support
$_REQUEST['DIRECTION'] = !isset( $_REQUEST['DIRECTION'] ) ? '' : (string) $_REQUEST['DIRECTION'];

if (isset($_REQUEST['DIRECTION'])){
   $text_dir = htmlspecialchars( (string) $_REQUEST['DIRECTION'], ENT_QUOTES );
}

include('./import/config_database.php');
include('./import/class.mysql.php');
include('./import/config.php');

header('Content-type: text/html; charset=' . CHARSET);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo($livehelp_name); ?></title>
</head>
<frameset rows="2,*" frameborder="NO" border="0" framespacing="0">
  <frame src="./blank.php?<?php echo('DOMAINID='.$domainId); ?><?php echo('&DIRECTION='.$text_dir); ?>" name="displayRefreshFrame" scrolling="NO">
  <frame src="blank.php?LANGUAGE=<?php echo LANGUAGE_TYPE; ?><?php echo('&DOMAINID='.$domainId); ?><?php echo('&DIRECTION='.$text_dir); ?>" name="displayContentsFrame">
</frameset><noframes></noframes>
</html>
