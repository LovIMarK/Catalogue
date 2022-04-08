<?php
//On contrôle s'il y a déjà une session. Si non, on la démarre.
if(session_id() == "") {
	session_start();
}
//require_once("/dbfunctions.inc.php");
//require_once("/phpfunctions.inc.php");

if(isset($_POST['articlesToDelete'])) {
	$tab_articlesToDelete = explode(",", $_POST['articlesToDelete']);
	
	for($i = 0; $i < count($tab_articlesToDelete); $i++) {
		unset($_SESSION['cart'][$tab_articlesToDelete[$i]]);
	}
	
	if(count($_SESSION['cart']) == 0) {
		unset($_SESSION['cart']);
	}
}

if(isset($_SESSION['cart'])) {
	//Paramètres par défaut
	$strSortCol = "dbo.ART.Artikelnummer";
	$strSorting = "asc";
	$intNbResults = 20;
	$intPge = 1;
	$intNbPages = 1;
	$strLocation = "cart";
	
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
	
	//On cherche le nombre de pages totales
	$intNbPages = getNumberOfPages($intNbResults, $strLocation);
	
	//Variable contenant le numéro de la page actuellement visualisée
	if(isset($_GET['page'])) {
		$intPge = mssql_real_escape_string(floor($_GET['page']));
	}
	else {
		$intPge = 1;
	}
	
	//Contrôle si le nombre de pages passé en paramètres est compris entre 1 et le nombre de pages totales
	//Si ce n'est pas le cas, affecte la bonne valeur à la variable.
	
	if($intPge > $intNbPages) {
		$intPge = $intNbPages;
	}
	if($intPge < 1) {
		$intPge = 1;
	}

	//Pour le tri des colonnes
	if(isset($_GET['sortCol']) && $_GET['sortCol'] != "") {
		$strSortCol = mssql_real_escape_string($_GET['sortCol']);
	}
	if(isset($_GET['sorting']) && ($_GET['sorting'] == "asc" || $_GET['sorting'] == "desc")) {
		$strSorting = mssql_real_escape_string($_GET['sorting']);
	}
		
	if(strripos($_SERVER['PHP_SELF'], '.inc.php') && stripos($_SERVER['PHP_SELF'], "/include")) {
		$strPhpSelf = "";
		$strPhpSelf = substr($_SERVER['PHP_SELF'], 0, stripos($_SERVER['PHP_SELF'], "/include"));
		$strPhpSelf .= substr($_SERVER['PHP_SELF'], (stripos($_SERVER['PHP_SELF'], "/include") + strlen("/include")), strlen($_SERVER['PHP_SELF']) - strlen($strPhpSelf) - strlen("/include") - strlen(".inc.php"));
		$strPhpSelf .= ".php";
	}
	else {
		$strPhpSelf = $_SERVER['PHP_SELF'];
	}
		
	$intNbEntries = count($_SESSION['cart']);
			
	print("<center><b>Contenu de votre panier (".($intPge-1)*($intNbResults+1)." &agrave; ");
	print($intNbEntries > ($intPge*$intNbResults)?($intPge*$intNbResults):$intNbEntries);
	print(" sur ".$intNbEntries.")</b></center>");
	
	print("<br />\n\n");
	
	getPagesList($intNbResults, $intPge, $strLocation);
	
	$strSorting == "asc"?asort($_SESSION['cart']):arsort($_SESSION['cart']);
	
	$objDB = new dbIfc();
	$tab_strCart = $objDB->getCartInfos($intNbResults, $intPge, $_SESSION['cart'], $strSortCol, $strSorting);
	unset($objDB);
	
	?>
	<br />
		<form action="<?php print($_SERVER['PHP_SELF']."?page=printpdf"); ?>" id="frmPrint" name="frmPrint" method="post" onSubmit="return testPrintForm($(this).serializeArray());" target="_blank">
			<div class="input-prepend">
				<span class="add-on">Pr&eacute;nom *</span> <input class="span3" type="text" id="Surname" name="Prenom" required />
			</div>
			<div class="input-prepend">
				<span class="add-on">Nom *</span> <input class="span3" type="text" id="Name" name="Nom" required />
			</div><br />
			<div class="input-prepend">
				<span class="add-on">Classe *</span> <input type="text" id="Class" name="Classe" class="span2" required />
			</div>
			<div class="input-prepend">
				<span class="add-on">R&eacute;f&eacute;rence/Projet</span> <input class="span2" type="text" id="Ref" name="Reference" />
			</div>
			<div class="input-prepend">
				<select id="selTeacher" name="MaitreId" onChange="$('#Maitre').val($('option:selected',this).text());">
				<span class="add-on">Ma&icirc;tre *</span>
					<option value="100">Autres (VCY)</option>
					<option value="157">Adams Scott (SAS)</option>
					<option value="108">Affolter Dominique (DAR)</option>
					<option value="123">Auberson Thierry (TAN)</option>
					<option value="150">Aubert Pierre (PAT)</option>
					<option value="158">Balet St&eacute;phane (SBT)</option>
					<option value="103">Beutler Yves (YBR)</option>
					<option value="114">Brandt Yves (YBT)</option>
					<option value="110">Chapuis Robert (RCS)</option>
					<option value="151">Deladoey Luc (LDE)</option>
					<option value="115">Ecoffey Aim&eacute; (AEY)</option>
					<option value="102">Fose Fabrice (FRE)</option>
					<option value="104">Ruiz John (JRZ)</option>
					<option value="101">Joss Philippe (PJS)</option>
					<option value="147">Lonia Raffaele (RLA)</option>
					<option value="105">Marouani Noureddine (NMI)</option>
					<option value="109">Millet Patrick (PMT)</option>
					<option value="130">Ollivier Patrick (POR)</option>
					<option value="135">Poulin Philippe (PPO)</option>
					<option value="121">Ravaioli Mich&eacute;l&eacute; (MRI)</option>
					<option value="133">Richoz Fran&ccedil;ois (FRZ)</option>
					<option value="127">Ryser Yann (YRR)</option>
					<option value="117">Schaller Lionel (LSH)</option>
					<option value="124">Zappelli Alexandre (AZI)</option>
					
					
					/////ID à changer
					/*
					<option value="124">Henry Sébastien (SHY)</option>
					<option value="124">Huser Matthias (MHR)</option>
					<option value="124">Piguet Ralph (RPT)</option>
					<option value="124">Sousa Pedro (PSA)</option>
					<option value="124">Seydoux Dominique (DSX)</option>
					<option value="124">Richard Patrick (PRI)</option>
					<option value="124">Rochat Sylvain (SRO)</option>
					<option value="124">Bertholet Thierry (TBT)</option>
					<option value="124">Di Natale Romain (RDE)</option>
					<option value="124">Castoldi Serge (SCA)</option>
					<option value="124">Richard Albert (ARC)</option>
					<option value="124">Sahli Bertrand (BSI)</option>
					<option value="124">Schneider Patrick (PSR)</option>

					*/




				</select>
			</div>
			<input type="hidden" id="Maitre" name="Maitre" value="Autres (VCY)" />
		</form>
		<table id="tableContent" class="table table-hover">
			<thead>
				<th>Image</th>
				<th class="pointer" onmouseover='new tooltip().createTooltip(event, this, "Cliquez pour trier par "+this.innerHTML);' onClick='document.location.href="<?php print(moveParameter('sortCol=', moveParameter('sorting=', $strPhpSelf."?".$_SERVER['QUERY_STRING'], ascOrDesc("dbo.ART.Artikelnummer")), "dbo.ART.Artikelnummer")) ?>";'>No d'article
				
				<?php
					if($strSortCol == 'dbo.ART.Artikelnummer' || $strSortCol == 'ART.Artikelnummer') {
						$strSorting == 'asc' ? print(" <i class='icon-chevron-down'></i>") : print(" <i class='icon-chevron-up'></i>");
					}
				?>
				
				</th>
				<th class="pointer" onmouseover='new tooltip().createTooltip(event, this, "Cliquez pour trier par "+this.innerHTML);' onClick='document.location.href="<?php print(moveParameter('sortCol=', moveParameter('sorting=', $strPhpSelf."?".$_SERVER['QUERY_STRING'], ascOrDesc("dbo.ART.Bezeichnung")), "dbo.ART.Bezeichnung")) ?>";'>D&eacute;signation
				
				<?php
					if($strSortCol == 'dbo.ART.Bezeichnung' || $strSortCol == 'ART.Bezeichnung') {
						$strSorting == 'asc' ? print(" <i class='icon-chevron-down'></i>") : print(" <i class='icon-chevron-up'></i>");
					}
				?>
				
				</th>
				<th class="pointer" onmouseover='new tooltip().createTooltip(event, this, "Cliquez pour trier par "+this.innerHTML);' onClick='document.location.href="<?php print(moveParameter('sortCol=', moveParameter('sorting=', $strPhpSelf."?".$_SERVER['QUERY_STRING'], ascOrDesc("dbo.ARKALK.Kalkulationspreis")), "dbo.ARKALK.Kalkulationspreis")) ?>";'>Prix
				
				<?php
					if($strSortCol == 'dbo.ARKALK.Kalkulationspreis' || $strSortCol == 'ARKALK.Kalkulationspreis') {
						$strSorting == 'asc' ? print(" <i class='icon-chevron-down'></i>") : print(" <i class='icon-chevron-up'></i>");
					}
				?>
				
				</th>
				<th></th>
			</thead>
			<tbody>
			<?php
			//Affichage des valeurs retournées
			foreach($tab_strCart as $key => $tab_values) {
				
				$strOnClick = "onClick='setArticleUrl(\"".$tab_values['Artikelnummer']."\", \"".moveParameter('article=', $strPhpSelf."?".$_SERVER['QUERY_STRING']).$tab_values['Artikelnummer']."\");'";
				$strOnMouseOver = "onmouseover='new tooltip().createTooltip(event, this, \"D&eacute;tails de ".$tab_values['Bezeichnung']."\");'";
				$strImgMouseOver = 'onmouseover=\'var objImg = document.createElement("img"); objImg.src = "./include/blobimg.inc.php?imgid='.$tab_values["Artikelnummer"].'"; new tooltip().createTooltip(event, this, ""); document.getElementById("tooltip").appendChild(objImg);\'';//  
			?>
			
				<tr style="height:70px;">
					<td class="pointer" <?php print($strOnClick. " ". $strImgMouseOver); ?> ><img src="./include/blobimg.inc.php?imgid=<?php print($tab_values['Artikelnummer']); ?>" style="max-width:60px; max-height:60px;" /></td>
					<td class="pointer" <?php print($strOnClick. " ". $strOnMouseOver); ?> ><?php print($tab_values['Artikelnummer']); ?></td>
					<td class="pointer" <?php print($strOnClick. " ". $strOnMouseOver); ?> ><?php print($tab_values['Bezeichnung']); 
					if($tab_values['Zusatz'] != "") {
						?> - <?php print($tab_values['Zusatz']);
					}?></td>
					<td class="pointer" <?php print($strOnClick. " ". $strOnMouseOver); ?> ><?php print(round($tab_values['Kalkulationspreis'], 2)); ?></td>
					<td>
						<input type='checkbox' id="<?php print($tab_values['Artikelnummer']); ?>" name='article' value='<?php print($tab_values['Artikelnummer']); ?>' />
						<input type='text' class="input-mini" id="article_<?php print($tab_values['Artikelnummer']); ?>" name='articleQuantity' maxlength='3' size='3' value='<?php
						if(isset($_SESSION['cart'][$tab_values['Artikelnummer']]))
						{
							print($_SESSION['cart'][$tab_values['Artikelnummer']]);
						}
						else {
							print("0");
						}
						?>' style='text-align:center;' onChange='updateList("article");'/> </td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot style='cursor:auto'>
				<tr>
					<td colspan='4' style='text-align:right;'>
						Tout cocher </td>
					<td>
						<input type="checkbox" name='mainArticle' id='mainArticle' value='mainArticle' onClick='checkAll("article");'/>
					</td>
				</tr>
				<tr>
					<td colspan='4' style='text-align:right;'>
						Supprimer les lignes s&eacute;lectionn&eacute;es </td>
					<td>
						<input type="button" class="btn btn-danger" value="Supprimer la s&eacute;lection" name="btnDelete" id="btnDelete" onClick="deleteSelected('article'); refreshCart();" />
					</td>
				</tr>
				<tr>
					<td colspan='4'> </td>
					<td>
						<input type='button' class="btn btn-primary" onClick="$('#frmPrint').submit()" value='Imprimer le panier' name='btnPrint' id='btnPrint' />
					</td>
				</tr>
			</tfoot>
		</table>

		<br /><br />
<?php
		getPagesList($intNbResults, $intPge, $strLocation);
	
	print("</div>");
}

//Le panier est vide => Erreur
else {
	print("<br /><br /><br /><br />");
	print("<div id='cartContent' style='position:relative; z-index:9;'><center><b>Votre panier est actuellement vide. Vous pouvez <a href='./'>retourner &agrave; l'index</a> ou <a href='javascript:history.back()'>revenir &agrave; la page pr&eacute;c&eacute;dente</a></b></center></div>");
}
?>