<?php
//***************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    08.11.2012
// But:     Fichier contenant toutes les fonctions PHP
//***************************************************************
// Modifications:
// Date  :  -
// Auteur:  -
// Raison:  -
//***************************************************************

// *******************************************************************
// Nom :	mssql_real_escape_string
// But :	Echappe une string pour éviter les injections sql
// Retour:	La string échappée
// Param.: 	$strInput -> La string à échapper
// *******************************************************************
function mssql_real_escape_string($strInput) {
	$tab_chars = array('NULL', '\x00', '\n', '\r', '\\', "'", '"', '\x1a');				//Les caractères à échapper
	$tab_escapes = array('\NULL', '\\x00', '\\n', '\\r', '\\\\', "''", '\"', '\\x1a');	//Les caractères sous formes échappée

	return str_replace($tab_chars, $tab_escapes, $strInput);	//On renvoit la string échappée
}

// *******************************************************************
// Nom :	createList
// But :	Crée une liste des données sous forme de tableau HTML
// Retour:	-
// Param.: 	$intNbResults -> Le nombre de lignes à afficher. Par défaut à 20.
// *******************************************************************
function createList($strLocation = "shop") {
	
	//Paramètres par défaut
	$strSortCol = "ART.Artikelnummer";
	$strSorting = "asc";
	$strSearch = null;
	$intNbResults = 20;
	
	//Nombre de ligne par page
	if(isset($_GET['nbItems'])) {
		//Si c'est trop élevé, on met à 100 lignes.
		if($_GET['nbItems'] > 100) {
			$intNbResults = 100;
		}
		//Sinon, on regarde pour que ça soit plus grand que 1
		else if($_GET['nbItems'] >= 1 && $_GET['nbItems'] <= 100) {
			$intNbResults = mssql_real_escape_string(floor($_GET['nbItems']));
		}
	}
	
	///Recherche éventuelle
	if(isset($_GET['txtSearch'])) {
		$strSearch = mssql_real_escape_string($_GET['txtSearch']);
	}
	else {
		$strSearch = null;
	}
	
	//On cherche le nombre de pages totales
	$intNbPages = getNumberOfPages($intNbResults, $strLocation);
	
	//Variable contenant le numéro de la page actuellement visualisée
	if(isset($_GET['page'])) {
		$intNumPage = mssql_real_escape_string(floor($_GET['page']));
	}
	else {
		$intNumPage = 1;
	}
	
	//Contrôle si le nombre de pages passé en paramètres est compris entre 1 et le nombre de pages totales
	//Si ce n'est pas le cas, affecte la bonne valeur à la variable.
	if($intNumPage > $intNbPages) {
		$intNumPage = $intNbPages;
	}
	if($intNumPage < 1) {
		$intNumPage = 1;
	}
	
	//Pour le tri des colonnes
	if(isset($_GET['sortCol']) && $_GET['sortCol'] != "") {
		$strSortCol = mssql_real_escape_string($_GET['sortCol']);
	}
	if(isset($_GET['sorting']) && ($_GET['sorting'] == "asc" || $_GET['sorting'] == "desc")) {
		$strSorting = mssql_real_escape_string($_GET['sorting']);
	}
	
	//On va chercher les données de la page
	$objDB = new dbIfc();
	$tab_strDatas = $objDB->getList($intNbResults, $intNumPage, $strSortCol, $strSorting, $strSearch);
	
	if($tab_strDatas) {
		//On va chercher le nombre d'entrées totales afin de calculer quelles entrées on regarde actuellement
		$intNbEntries = $objDB->getItemsNumber($strSearch);
	
		print("<div>\n");
		//Affichage du premier chiffre
		print("<b>Résultats de recherche: ".(($intNumPage-1)*$intNbResults+1));
		
		//Affichage du second chiffre (si nécessaire)
		if(count($tab_strDatas) > 1) {
			print(" à ");
			print($intNbEntries > ($intNumPage*$intNbResults)?($intNumPage*$intNbResults):$intNbEntries);
		}
		
		print(" sur ".$intNbEntries.".</b></div><br />");
		
		//Début du tableau contenant les valeurs
		print("<div>\n");
		print("<table id='tableContent' class='table table-hover'>\n");
		print("<tr>\n<thead>\n");
		
		//Au click sur une des colonne du tableau de valeurs, un tri est effectué
		print("<th>Image</th>\n");
		print("<th class=\"pointer\" onmouseover='new tooltip().createTooltip(event, this, \"Cliquez pour trier par \"+this.innerHTML);' onClick='document.location.href=\"".moveParameter('sortCol=', moveParameter('sorting=', $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'], ascOrDesc("dbo.ART.Artikelnummer")), "dbo.ART.Artikelnummer")."\";'>No d'article");
		
		if($strSortCol == "ART.Artikelnummer" || $strSortCol == "dbo.ART.Artikelnummer") {
			
			$strSorting == 'asc' ? print(" <i class='icon-chevron-down'></i>") : print(" <i class='icon-chevron-up'></i>");
		}
		
		print("</th>\n");
		print("<th class=\"pointer\" onmouseover='new tooltip().createTooltip(event, this, \"Cliquez pour trier par \"+this.innerHTML);' onClick='document.location.href=\"".moveParameter('sortCol=', moveParameter('sorting=', $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'], ascOrDesc("dbo.ART.Bezeichnung")), "dbo.ART.Bezeichnung")."\";'>Désignation");
		
		if($strSortCol == "ART.Bezeichnung" || $strSortCol == "dbo.ART.Bezeichnung") {
			
			$strSorting == 'asc' ? print(" <i class='icon-chevron-down'></i>") : print(" <i class='icon-chevron-up'></i>");
		}
		
		print("</th>\n");
		print("<th class=\"pointer\" onmouseover='new tooltip().createTooltip(event, this, \"Cliquez pour trier par \"+this.innerHTML);' onClick='document.location.href=\"".moveParameter('sortCol=', moveParameter('sorting=', $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'], ascOrDesc("dbo.ARKALK.Kalkulationspreis")), "dbo.ARKALK.Kalkulationspreis")."\";'>Prix");
		
		if($strSortCol == "ARKALK.Kalkulationspreis" || $strSortCol == "dbo.ARKALK.Kalkulationspreis") {
			
			$strSorting == 'asc' ? print(" <i class='icon-chevron-down'></i>") : print(" <i class='icon-chevron-up'></i>");
		}
		
		print("</th>\n");
		print("<th></th>\n");
		print("</thead>\n</tr>\n\n<tbody></n>"); 
		
		//Affichage des valeurs retournées
		foreach($tab_strDatas as $key => $tab_values) {
			
			$strOnClick = "onClick='setArticleUrl(\"".$tab_values['Artikelnummer']."\", \"".moveParameter('article=', $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']).$tab_values['Artikelnummer']."\");'";
			$strOnMouseOver = "onmouseover='new tooltip().createTooltip(event, this, \"Détails de ".str_replace(array('\'', '"'), array('\x27','\x22'), $tab_values['Bezeichnung'])."\");'";
			$strImgMouseOver = 'onmouseover=\'var objImg = document.createElement("img"); new tooltip().createTooltip(event, this, ""); objImg.src = "./include/blobimg.inc.php?imgid='.$tab_values["Artikelnummer"].'"; resizeImage(objImg);';
			
			$strImgMouseOver .= 'document.getElementById("tooltip").appendChild(objImg);\'';
			
			
			//Affichage des lignes du tableau
			print("<tr style='height:70px;'>\n");
			
			//Affichage des colonnes
			print("<td class=\"pointer\" ".$strOnClick." ".$strImgMouseOver."><img src='./include/blobimg.inc.php?imgid=".$tab_values['Artikelnummer']."'  style='max-width:60px; max-height:60px;' /></td>\n");
			print("<td class=\"pointer\" ".$strOnClick." ".$strOnMouseOver.">".$tab_values['Artikelnummer'])."</td>\n";
			print("<td class=\"pointer\" style='text-align:left;' ".$strOnClick." ".$strOnMouseOver.">".$tab_values['Bezeichnung']."\n");
			if($tab_values['Zusatz'] != "") {
				print(" - ".$tab_values['Zusatz']."</td>\n");
			}else{
				print("</td>\n");
			}
			print("<td class=\"pointer\" ".$strOnClick." ".$strOnMouseOver.">".round($tab_values['Kalkulationspreis'], 2)." CHF</td>\n");
			print("<td>");
			print("<input type='checkbox' id='".$tab_values['Artikelnummer']."' class='checkbox' name='article' onClick='if(this.checked && document.getElementById(\"article_".$tab_values['Artikelnummer']."\").value == \"0\") { document.getElementById(\"article_".$tab_values['Artikelnummer']."\").value=\"1\"; document.getElementById(\"article_".$tab_values['Artikelnummer']."\").focus(); }' value='".$tab_values['Artikelnummer']."' />");
			print("<input type='text' id='article_".$tab_values['Artikelnummer']."' name='articleQuantity' maxlength='3' size='3' class='input-mini' value='");
			
			//Remplissage du nombre d'articles déjà dans le panier pour chaque article
			if(isset($_SESSION['cart'][$tab_values['Artikelnummer']]))
			{
				print($_SESSION['cart'][$tab_values['Artikelnummer']]);
			}
			else {
				print("0");
			}
			
			print("' style='text-align:center;'/> </td>\n");
			
			print("</tr>\n\n");
		}
		print("</tbody>\n<tfoot style='cursor:auto'>\n<tr>\n");
		
		print("<td colspan='4' style='text-align:right;'>\nTout cocher </td>\n<td>\n<input type=\"checkbox\" name='mainArticle' id='mainArticle' value='mainArticle' onClick='checkAll(\"article\");'/>\n</td>\n");
		
		print("</tr>\n<tr>\n");
		
		print("<td colspan='4' style='text-align:right;'>\nAjouter les lignes cochées à la liste d'impression </td>\n<td>\n<input type='button' class='btn' value='Ajouter' name='btnAddToList' id='btnAddToList' onClick='addToList(\"article\");' />\n</td>\n");
		
		print("</tr>\n</tfoot>\n</table>\n");
		
		print("</div>\n\n");
	}
	else {
		//Message d'erreur !
		print("<center><b>Cette requête n'a retourné aucun résultat. Vous pouvez lancer une recherche, <a href='./'>retourner à l'index</a> ou <a href='javascript:history.back()'>revenir à la page précédente</a></b></center>");
	}
	
	//Suppression de l'objet
	unset($objDB);
	
	//Div qui va contenir le message d'information (tooltip).
?>	
	<div id="tooltip"></div>
<?php
}//createList ()

// *******************************************************************
// Nom :	getNumberOfPages
// But :	Calcule le nombre de pages en fonction du nombre de lignes par page.
// Retour:	$intPages -> Le nombre de pages qu'il y aura au total.
// Param.: 	$intNbResultsPerPage -> Le nombre de lignes par page. Par défaut à 20.
// *******************************************************************
function getNumberOfPages($intNbResultsPerPage = 20, $strLocation = "shop") {
	
	//Initialisation des valeurs par défaut
	$strSearch = null;
	$intPages = false;
	$strSortCol = "Artikelnummer";
	$strSorting = "asc";
	
	//Pour le tri des colonnes
	if(isset($_GET['sortCol']) && $_GET['sortCol'] != "") {
		$strSortCol = mssql_real_escape_string($_GET['sortCol']);
	}
	if(isset($_GET['sorting']) && ($_GET['sorting'] == "asc" || $_GET['sorting'] == "desc")) {
		$strSorting = mssql_real_escape_string($_GET['sorting']);
	}
	
	//Si le nombre de résultats par pages est précisé, on l'utilise.
	if(isset($_GET['nbItems'])) {
		//Si ça dépasse le max, on le met au maximum
		if($_GET['nbItems'] > 100) {
			$intNbResultsPerPage = 100;
		}
		//Sinon, si c'est précisé, on affecte la valeur à la variable.
		else if($_GET['nbItems'] >= 1 && $_GET['nbItems'] <= 100) {
			$intNbResultsPerPage = mssql_real_escape_string(floor($_GET['nbItems']));
		}
		//Si ça n'entre pas dans les critères, on garde le 20 par défaut.
	}
	
	//Recherche éventuelle
	if(isset($_GET['txtSearch']) && $_GET['txtSearch'] != "") {
		$strSearch = mssql_real_escape_string($_GET['txtSearch']);
	}
	
	$intNbResults = 0;
	
	switch($strLocation) {
		
		case "cart":
			$intNbResults = count($_SESSION['cart']);
			break;
			
		case "shop":
		default:
			//On compte combien il y a d'entrées dans la BDD
			$objDB = new dbIfc();
			$intNbResults = $objDB->getItemsNumber($strSearch);
			unset($objDB);
			break;
	}
	
	//A partir de là, on prend l'arrondi supérieur du résultat de
	//la division du nombre de résultats total par le nombre de résultats par page.
	$intPages = ceil($intNbResults/$intNbResultsPerPage);
	
	//On retourne le nombre de pages
	return $intPages;
}// getNumberOfPages()

// *******************************************************************
// Nom :	ascOrDesc
// But :	Teste si le prochain tri doit être ascendant ou descendant
// Retour:	"asc" ou "desc" -> dit si le tri est ascendant ou descendant
// Param.: 	$strNewSortCol -> Le nom de la colonne qui va être triée
// *******************************************************************
function ascOrDesc($strNewSortCol = "dbo.ART.Artikelnummer") {

	$strSortCol = "dbo.ART.Artikelnummer";
	$strSorting = "asc";
	
	if(isset($_GET['sortCol'])) {
		$strSortCol = $_GET['sortCol'];
	}
	
	if(isset($_GET['sorting']) && ($_GET['sorting'] == "asc" || $_GET['sorting'] == "desc")) {
		$strSorting = $_GET['sorting'];
	}
	
	//Si on veut trier une autre colonne que celle qui
	//est actuellement triée, on fait un tri ascendant
	if($strSortCol != $strNewSortCol) {
		return "asc";
	}
	//Sinon, on change son tri
	else {
		switch($strSorting) {
			case "asc":
				return "desc";
			case "desc":
			default:
				return "asc";
		}
	}

}// ascOrDesc()

// *******************************************************************
// Nom :	moveParameter
// But :	Déplace le paramètre d'une URL pour qu'il soit à la fin de l'URL
// Retour:	$strTmpLink -> L'URL modifiée
// Param.: 	$parameter -> Le paramètre à mettre à la fin de l'URL
//			$url -> l'URL à traiter
// *******************************************************************
function moveParameter($parameter, $url, $newval = "") {
	//URL temporaire, il est possible
	//que l'URL de base ne soit pas modifiée
	$strTmpLink = $url;
	
	//Teste s'il y a un paramètre après le paramètre à déplacer
	if (strpos($url, "&", strrpos($url, $parameter))) {
		$strTmpLink = "";
		
		//Si oui, création de intNextArg et intArg:
		
		//intNextArg = Position du premier caractère du
		//paramètre situé après $parameter dans $url
		$intNextArg = strpos($url, "&", strrpos($url, $parameter));		
		
		//intArg = Position du premier caractère de $parameter dans $url
		$intArg = strrpos($url, $parameter);
		
		//Reconstruction de l'URL sans ajouter le paramètre
		for($i = 0; $i < strlen($url); $i++) {
			$strTmpLink .= $url[$i];
			
			//Dès qu'on arrive à la position d'avant celle du paramètre,
			//on passe à la position après le paramètre
			if($i == $intArg - 1) {
				$i = $intNextArg;
			}
		}
	}
	
	//Si $parameter est à la fin de $url
	//Ceci est surtout fait pour supprimer sa valeur !
	else if(strrpos($url, $parameter)) {
		$strTmpLink = "";
	
		$intNumber = strrpos($url, $parameter) + strlen($parameter);
		
		//Reconstruction du lien sans ajouter la valeur de $parameter
		for($i = 0; $i < $intNumber; $i++) {
			$strTmpLink .= $url[$i];
		}
	}
	
	//Teste si $parameter se trouve dans le lien recréé
	if(!strrpos($strTmpLink, $parameter)) {
		//Teste s'il faut mettre un "?" ou un "&" avant de réécrire le paramètre.
		if(!strrpos($strTmpLink, "?")) {
			$strTmpLink .= "?";
		}
		//Test de s'il y a un argument après le "?". Si oui, on met un "&".
		else if(isset($strTmpLink[strrpos($strTmpLink, "?")+1])) {
			$strTmpLink .= "&";
		}
		//Ajout du paramètre à la fin de l'url
		$strTmpLink .= $parameter.$newval;
	}
	
	return $strTmpLink;
}// moveParameter()

// *******************************************************************
// Nom :	getPagesList
// But :	Affiche une liste des pages afin de naviguer.
// Retour:	-
// Param.: 	$intNbResultsPerPage -> Le nombre de lignes par page. Par défaut à 20.
//			$intPage -> La page sur laquelle on se trouve. Par défaut à 1.
// *******************************************************************
function getPagesList($intNbResultsPerPage = 20, $intPage = 1, $strLocation = "shop") {
	//Paramètre contenant le numéro de la page actuellement visualisée
	if(isset($_GET['page'])) {
		$intPage = mssql_real_escape_string(floor($_GET['page']));
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
		
		preg_match('/.inc.php$/', $_SERVER['PHP_SELF'], $tab_strMatches);
		
		//Construction de l'URL
		if($tab_strMatches) {
			$strNewUrl = moveParameter("page=", $_SERVER['HTTP_REFERER']."?".$_SERVER['QUERY_STRING']);
		}
		else {
			$strNewUrl = moveParameter("page=", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']);
		}
		
		//Initialisation de la variable de comptage
		$i = $intPage;
		if($i-3 <= 0) {
			$i = 1;
		}
		else {
			$i -= 2;
		}
		
		
		require_once(dirname(__FILE__).'/gotopage.inc.php');
		
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
} //getPagesList()

// *******************************************************************
// Nom :	createSearch
// But :	Affiche un lien vers l'index, un champ de recherche et le panier
// Retour:	-
// Param.: 	-
// *******************************************************************
function createSearch() {
	$strSearch = "";

	//Bezeichnung de recherche
	if(isset($_GET['txtSearch'])) {
		$strSearch = $_GET['txtSearch'];
	}

	//Affichage du formulaire de recherche
	?>
	<div style="position:relative; z-index:6;" id="searchDiv">
		<table style="text-align:center;">
			<tr>
				<td style="width:34%;">
					<b><a href="index.php">Index du catalogue</a></b>
				</td>
				<td style="width:33%;">
					<form id="frmSearch" name="frmSearch" action="recherche.php" class="form-search" method="get">
					<div class="input-append">
						<input type="search" name="txtSearch" id="txtSearch" class="span2 search-query" placeholder="Entrez votre recherche" value="<?php print($strSearch); ?>" />
						<button type="submit" class="btn"><i class="icon-search"></i></button>
					</div>
					</form>
				</td>
				<td style="width:33%;">
					<a href="./cart.php"><i class="icon-shopping-cart"></i> Panier(<span id="cartItems">
					<?php
						//Quantité d'articles dans le panier
						include_once("include/cartquantity.inc.php");
					?>
					</span>)</a>
				</td>
			</tr>
		</table>
	</div>
	<?php
} //createSearch()
	
// *******************************************************************
// Nom :	calcPrice
// But :	Calcule le prix pour un article et l'arrondit au 0.01
// Retour:	writePrice() ->	Adapte le prix pour qu'il soit affiché
//							correctement
// Param.: 	$fltPrice ->	Prix de l'article 
//			$intQuantity ->	Quantité commandée de l'article
// *******************************************************************
function calcPrice($fltPrice, $intQuantity = 1) {
	//Calcul du nouveau prix (Majoration de 10%, prix ETML)
	$fltPrice = round(1.1*$fltPrice*$intQuantity,2, PHP_ROUND_HALF_UP);
	
	//Retourne la valeur formatée
	return writePrice($fltPrice);
}

// *******************************************************************
// Nom :	writePrice
// But :	Teste la fin du prix pour voir s'il y a besoin de rajouter
//			des zéros à l'affichage
// Retour:	$fltPrice ->	Prix total correctement formaté
// Param.: 	$fltPrice ->	Prix total
// *******************************************************************
function writePrice($strPrice) {
	//Expode du prix pour se concentrer sur la 2ème partie uniquement
	$tab_strPriceParts = explode(".",$strPrice);
	
	$strNewPrice = "";
	
	//$debug = "<script>alert('".$strNewPrice." ".$tab_strPriceParts[0][$i]."');</script>";
	
	for($i = 0; $i < strlen($tab_strPriceParts[0]); $i++) {
		if((strlen($tab_strPriceParts[0])-$i)%3 == 0 && $i != 0) {
			$strNewPrice .= "'";
		}
		$strNewPrice .= $tab_strPriceParts[0][$i];
		
	}
		//print("<script>alert('".$strNewPrice." - ".$tab_strPriceParts[0]."');</script>");
	
	$strNewPrice .= ".";
	
	//S'il n'y a pas de 2ème partie, rajouter "00"
	if(!isset($tab_strPriceParts[1])) {
		$strNewPrice .= "00";
	}
	//S'il n'y a pas 2 caractères sur la 2ème partie, rajouter "0"
	else if(!isset($tab_strPriceParts[1][1])) {
		$strNewPrice .= $tab_strPriceParts[1][0];
		$strNewPrice .= "0";
	}
	else {
		$strNewPrice .= $tab_strPriceParts[1];
	}
	
	//Retour de la valeur
	return $strNewPrice;
}
?>