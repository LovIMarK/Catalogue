<script type="text/javascript" src="include/js/tooltip.js"></script>
<script type="text/javascript" src="include/js/functions.js"></script>
<script type="text/javascript" src="include/js/ajax.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
<script type="text/javascript" src="include/bootstrap/js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="include/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="include/bootstrap/css/bootstrap-responsive.min.css">
<link rel="stylesheet" type="text/css" href="include/styles/styles.css">

<?php
	if(session_id() == "") {
		session_start();
	}
	require_once("include/dbfunctions.inc.php");
	require_once("include/phpfunctions.inc.php");
	require_once("include/iteminfos.inc.php");
?>