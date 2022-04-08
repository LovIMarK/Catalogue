<?php 

	//On contrôle s'il y a déjà une session. Si non, on la démarre.
	if(session_id() == "") {
		session_start();
	}
	
	
	
	//Si on est en mode PDF, crée un PDF du panier
	if(isset($_GET['page']) && $_GET['page'] == "printpdf" && isset($_SESSION['cart']) && !is_null($_SESSION['cart']) && isset($_POST['Prenom']) && !is_null($_POST['Prenom']) && isset($_POST['Nom']) && !is_null($_POST['Nom']) && isset($_POST['Classe']) && !is_null($_POST['Classe'])) {
		require_once("./module/fpdf.php");
		require_once("./include/articlestopdf.class.php");
		require_once("./include/dbfunctions.inc.php");
		require_once("./include/phpfunctions.inc.php");
		
		/********************************************
		*	created by DLS
		*	used to export data to PDF
		********************************************/
		
		// instanciation de l'objet d'interface a la base de donnees
		$objDB = new dbIfc();
		// on récupère les infos des pièces dans la BD
		$tab_strCartInfo = $objDB->getCartInfos(count($_SESSION['cart']), 1, $_SESSION['cart'], "ART.Artikelnummer", "asc");
		
		// on instancie l'objet qui construit le PDF
		$pdf = new articlesList_pdf();


		// titre du PDF
		$titre = utf8_decode('Liste des pièces à prendre au Magasin de l\'ETML');
		
		//-------------------------------------
		//configuration de la page
		//-------------------------------------
		$pdf->SetMargins(0.8, 1.4);
		
		$pdf->SetTitle($titre);
		// permet d'avoir le nombre de page total
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		// on ne fait pas de saut de page automatique
		$pdf->SetAutoPageBreak(false);
		
		// variable permettant de placer les blocs infos des enseignants
		$intRow=0;	// indice de l'enregistrement courant
		$iSpaceX=1; // déplacement dans l'axe des x
		$iSpaceY=1; // déplacement dans l'axe des y

		// On insère le nombre d'enregistrement
		$pdf->setNbreEnregistrement(count($_SESSION['cart']));
		
		$intRow=0;
		while ($intRow<count($tab_strCartInfo)) {
				
				if($iSpaceY == 1) {
					$pdf->setProjectInfos();
					$pdf->setListHeader();
				}
				
				//Récupération des fonctions sur l'enseignant sous forme de tableau
				$strCartInfo=$tab_strCartInfo[$intRow]['Artikelnummer'];
				
				// on construit le corps de la page PDF
				$pdf->articleInfo(6,30+intval($iSpaceY)*9,$tab_strCartInfo[$intRow],$strCartInfo);
					
				$iSpaceY++;
				// on place 29 lignes par page

				if($iSpaceY==29){
					//saut de page
					$pdf->AddPage();
					$iSpaceY=1;
				}
			
			$intRow++;
		
			
		}
		
		//Affichage du prix total
		$pdf->setTotalPrice(179, 27+intval($iSpaceY)*9, $tab_strCartInfo);
		
		$intLineHaut = intval($iSpaceY)*9+22;
		$intLineBas = intval($iSpaceY)*9+30.8;
		
		if($iSpaceY == 1) {
			$pdf->setProjectInfos();
			$pdf->Line(6,$intLineHaut,202,$intLineHaut);
		}
		
		$pdf->Line(179,$intLineHaut,179,$intLineBas);
		$pdf->Line(6,$intLineBas,202,$intLineBas);
		
		$iSpaceY += 2;
		
		if($iSpaceY >= 29) {
			$pdf->AddPage();
			$pdf->setProjectInfos();
			$iSpaceY = 1;
		}
		
		$pdf->setProfessor(27+intval($iSpaceY)*9);
		
		// suppression de l'objet d'ifc a la BD
		unset($objDB);

		// on envoie l'apperçu
		$pdf->Output();
	}
	else {
?>
<!DOCTYPE html>

<!--*************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    08.11.2012
// But:     Page d'accueil où les informations seront affichées
//***************************************************************
// Modifications:
// Date  :  -
// Auteur:  -
// Raison:  -
//************************************************************-->

<html>
	<head>
		<title>Catalogue ETML - Panier</title>
		<?php require_once("./include/includes.inc.php"); ?>
	</head>
	<body>

<?php
				print("<div id='container' style='position:relative;'>");
				
				createSearch();
				
				print("<br /><br />\n\n");?>
				
				<div id='tooltip'></div>
				<div id='cartContent' style='position:relative;'>
				<?php
				require_once("./include/cart.inc.php");
			
				print("</div>");
?>
	</body>
</html>
<?php
}
?>