<?php

require_once('./dbfunctions.inc.php');
require_once('./phpfunctions.inc.php');

$strLocation = "shop";

//Paramètre contenant le numéro de la page actuellement visualisée
if(isset($_GET['page'])) {
	$intPage = mssql_real_escape_string(floor($_GET['page']));
}
else {
	$intPage = 1;
}

//Paramètre contenant le nombre de lignes par page.
if(isset($_GET['nbItems'])) {
	if($_GET['nbItems'] > 100) {
		$intNbResultsPerPage = 100;
	}
	if($_GET['nbItems'] >= 1 && $_GET['nbItems'] <= 100) {
		$intNbResultsPerPage = mssql_real_escape_string(floor($_GET['nbItems']));
	}
}
else {
	$intNbResultsPerPage = 20;
}

$intNbPages = getNumberOfPages($intNbResultsPerPage, $strLocation);

if($intNbPages) {
	//Contrôle si le nombre de pages passé en paramètres est compris entre 1 et le nombre de pages totales
	//Si ce n'est pas le cas, affecte la bonne valeur à la variable.
	if($intPage > $intNbPages) {
		$intPage = $intNbPages;
	}
	if($intPage <= 0) {
		$intPage = 1;
	}
	
	//Construction de l'URL
	$strNewUrl = moveParameter("page=", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']);
	
	//Initialisation de la variable de comptage
	$i = $intPage;
	if($i-3 <= 0) {
		$i = 1;
	}
	else {
		$i -= 2;
	}
	
	
	require_once('./gotopage.inc.php');
	
	//Début de l'écriture de la liste des pages
	print("<div>\n");
	print("<table>\n<tr>\n");
	//Partie "Aller à la page..."
	print("<td>&nbsp;\n<ul class='pager'>\n");
	if($intNbPages > 1){
		print("<li class='pointer'><a onClick='$(\"#selectPage\").modal(\"show\"); $(\"#txtPageToGo\").focus();'>Aller à la page...</a>\n</li>\n</ul>\n");
		//Le OnClick demande à quelle page on veut aller. Si on met un chiffre puis OK => Va à cette page.
		//Si on clique sur annuler, ne fait rien.
	}
	print("&nbsp;</td>\n<td>&nbsp;\n");
	
	print("<div class='pagination pagination-centered'>\n<ul>\n");
			
	//Partie "<< Début, < Précédent
	//Si on est sur la page 1, on ne met pas de lien
	if($intPage-1 >= 1) {
		print("<li>\n<a href='".$strNewUrl."1'>&laquo;</a></li>\n<li><a href='".$strNewUrl.($intPage-1)."'>&lsaquo;</a>\n</li>\n");
	}
	else {
		print("<li class='disabled'><span>&laquo;</span></li>\n<li class='disabled'><span>&lsaquo;</span></li>\n");
	}
	
	//Numérotation de la page. On veut qu'il n'y ait que les 2 pages d'avant et les 2 pages d'après qui soient affichées.
	//Sauf si on est à la page 1 où on ne veut pas que les pages 0 et -1 soient affichées et idem pour la dernière page.
	//print("<div style='display:inline-block;overflow:auto;float:center;margin:auto 5px;'>\n<b>");
	while($i <= $intNbPages) {
		
		//Pages d'avant la page actuelle
		if($i > 0 && $i < $intPage) {
			print("<li><a href='".$strNewUrl.$i."'>".$i."</a></li>\n");
		}
		//Page sur laquelle on se trouve actuellement
		else if($i == $intPage) {
			print("<li class='active'><span>".$i."</span></li>\n");
		}
		//Pages d'après la page actuelle
		else if($i <= $intPage + 2) {
			print("<li><a href='".$strNewUrl.$i."'>".$i."</a></li>\n");
		}
		//Incrémentation du compteur
		$i++;
	}
	
	//Partie "Suivant >, Fin >>"
	//Si on est sur la dernière page, on ne met pas de lien
	//print("</b></div>\n</td>\n<td>\n<div style='display:inline-block;overflow:auto;margin:auto 5px;'>\n");
	if($intPage+1 <= $intNbPages) {
		print("<li>\n<a href='".$strNewUrl.($intPage+1)."'>&rsaquo;</a></li><li><a href='".$strNewUrl.$intNbPages."'>&raquo;</a></li>\n");
	}
	else {
		print("<li class='disabled'><span>&rsaquo;</span></li><li class='disabled'><span>&raquo;</span></li>\n\n");
	}
	
	print("</div>&nbsp;</td><td>&nbsp;");
	
	//Mise en place du changement de valeur du paramètre "page" pour aller à la page 1
	//et du paramètre "nbItems"
	$strNewUrl = moveParameter("page=", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'], "1");
	$strNewUrl = moveParameter("nbItems=", $strNewUrl);
	
	//Select qui permet de sélectionner le nombre de lignes à afficher et de rafraichir la page.
	//1, 2, 3, 4, 5, 10, 20, 50 et 100
	print("<span>\n<select onChange='if(this.selectedIndex > 0) { document.location.href=\"".$strNewUrl."\"+this.value; }' >\n");
	print("<option value='0'>Lignes par page..</option>\n");
	print("<option value='1'>1</option>\n");
	print("<option value='2'>2</option>\n");
	print("<option value='3'>3</option>\n");
	print("<option value='4'>4</option>\n");
	print("<option value='5'>5</option>\n");
	print("<option value='10'>10</option>\n");
	print("<option value='20'>20</option>\n");
	print("<option value='50'>50</option>\n");
	print("<option value='100'>100</option>\n");
	print("</select>\n</span>\n");
	
	print("</div>\n</td>\n</tr>\n</table>\n");
	print("</div>\n\n");
	//Fin de la création de la numérotation
}

?>