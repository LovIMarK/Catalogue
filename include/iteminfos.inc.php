<?php

//Session
if(session_id() == "") {
	session_start();
}

//
if(isset($_GET['article']) && $_GET['article'] != "" && !isset($_POST['articleToWrite'])) {
	print("<script type='text/javascript'>");
	print("updateArticleDetails(\"".$_GET['article']."\");");
	print("</script>");
}

//
if(isset($_POST['articleToWrite'])) {
	require_once("dbfunctions.inc.php");
	$objDB = new dbIfc();
	$tab_strData = $objDB->getItemInfos($_POST['articleToWrite']);
	
	//
	if(isset($tab_strData[0])) {
		print("<b>Identifiant:</b> ".$tab_strData[0]['Artikelnummer'].
		"<br /><b>D&eacute;signation:</b> ".$tab_strData[0]['Bezeichnung'].
		"<br /><b>Compl&eacute;ment:</b> ".$tab_strData[0]['Zusatz']);
	}
	else {
		print("<b><u>Erreur:</u></b> L'article que vous avez recherch&eacute; est invalide !");
	}
}
else {
?>
	
<div id="item" class="modal hide fade">
	
	<div id="infoTop" class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Informations sur l'article</h3>
	</div>
	
	<div id="infos" class="modal-body text-left">
	</div>
	
	<div id="articleUrlDiv" class="modal-footer">
		<span>Lien permanent: </span><input type="url" id="articleURL" />
	</div>

</div>

<?php
}
?>