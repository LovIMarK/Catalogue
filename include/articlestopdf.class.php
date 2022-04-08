<?php 
	/*
	** ********************************************
	**
	** Auteur: Bryan Perroud
	** Date de création : 12.02.2013
	** Version : 0.1
	** Description : classe d'une liste 
	** 				 d'articles en de PDF
	**
	** Remarque :
	** 		la classe fpdf est nécessaire
	** 		
	** ********************************************
	**
	** Date de modification : 
	** Modifications(s) :
	** 
	** ********************************************
	*/
?>

<?php
	class articlesList_pdf extends FPDF
	{
	
		// *******************************************************************
		// Nom :	setNbreEnregistrement
		// But :	Affiche le nombre d'articles différents
		// Retour:	-
		// Param.: 	$intNbResults -> Le nombre de lignes à afficher.
		// *******************************************************************
		function setNbreEnregistrement($iNbre){
			//Calcul de la largeur du titre et positionnement
			$w = $this->GetStringWidth($iNbre);

			//on se positionne dans le bord droit
			$this->SetXY(203-$w,9);
			
			//Nombre d'articles différents
			$this->Cell($w,3,'Nombre de lignes : '.$iNbre,0,1,'R',false);
			
		}
	
		// *******************************************************************
		// Nom :	setProjectInfos
		// But :	Affiche le nom, prénom, la classe et la référence du projet
		// Retour:	-
		// Param.: 	-
		// *******************************************************************
		function setProjectInfos(){
			
		
			$fontName = 'Arial';
			
		
			$this->SetFont($fontName,'',13);
			
			$intLineWidth = $this->GetStringWidth("Nom: ".$_POST['Nom']."Prénom: ".$_POST['Prenom']."Classe: ".$_POST['Classe']."Référence/Projet: ")+9;
			
			$_POST['Reference'] == ""?$intLineWidth += 15:$intLineWidth += $this->GetStringWidth($_POST['Reference']);

			$intNameWidth = $this->GetStringWidth("Nom: ".$_POST['Nom'])+3;
			$intSurnameWidth = $this->GetStringWidth("Prénom: ".$_POST['Prenom'])+3;
			$intClassWidth = $this->GetStringWidth("Classe: ".$_POST['Classe'])+3;
			$intReferenceWidth = $this->GetStringWidth("Référence/Projet: ".$_POST['Reference']);
			
			$_POST['Reference'] == ""?$intReferenceWidth += 15:$intReferenceWidth += $this->GetStringWidth($_POST['Reference']);
			
			$this->SetFont($fontName,'',13);
			
			$intX = (210/2)-($intLineWidth/2);
			$intY = 16;
			
			//on se positionne dans le bord droit
			$this->SetXY($intX, $intY);
			//Nombre d'articles différents
			$this->Cell($intNameWidth,3,'Nom: '.$_POST['Nom'],0,1,'L',false);
			
			$intX += $intNameWidth;
			
			$this->SetXY($intX, $intY);
			$this->Cell($intSurnameWidth,3,utf8_decode('Prénom: ').$_POST['Prenom'],0,1,'L',false);
			
			$intX += $intSurnameWidth;
			
			//on se positionne dans le bord droit
			$this->SetXY($intX, $intY);
			//Nombre d'articles différents
			$this->Cell($intClassWidth,3,'Classe: '.$_POST['Classe'],0,1,'L',false);
			
			$intX += $intClassWidth;
			
			$this->SetXY($intX, $intY);
			$this->Cell($intReferenceWidth,3,utf8_decode('Référence/Projet: ').$_POST['Reference'],0,1,'L',false);
			
		}
	
		// *******************************************************************
		// Nom :	Header
		// But :	Affiche un header pour le fichier PDF avec un titre et un
		//			logo.
		// Retour:	-
		// Param.: 	-
		// *******************************************************************
		function Header()
		{
			global $titre;
			//on trace une ligne
			$this->Line(5, 8, 205, 8);
			//Arial gras 8
			$this->SetFont('Arial','B',8);
			//Calcul de la largeur du titre et positionnement
			$w = $this->GetStringWidth($titre)+6;
			//on se positionne dans le bord droit
			$this->SetXY(205-$w,4);
			
			//Couleurs du cadre, du fond et du texte
			$this->SetDrawColor(0,80,180);
			$this->SetFillColor(230,230,0);
			$this->SetTextColor(220,50,50);
			//Epaisseur du cadre (1 mm)
			$this->SetLineWidth(0.2);
			//Titre
			$this->Cell($w,3,$titre,0,1,'C',false);
			//Saut de ligne
			$this->Ln(10);
		}
	
		// *******************************************************************
		// Nom :	Footer
		// But :	Affiche un footer pour le fichier PDF le numéro de la page
		//			sur le nombre de pages.
		// Retour:	-
		// Param.: 	-
		// *******************************************************************
		function Footer()
		{
			//Positionnement à 1,5 cm du bas
			$this->SetY(-12);
			//Arial italique 8
			$this->SetFont('Arial','I',8);
			//Couleur du texte en gris
			$this->SetTextColor(128);
			//on trace une ligne séparatrice
			$this->Line(5, 288, 205, 288);
			//Numéro de page
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
	
		// *******************************************************************
		// Nom :	setListHeader
		// But :	Affichage du header du tableau en haut du tableau
		// Retour:	-
		// Param.: 	-
		// *******************************************************************
		function setListHeader() {
			
			//On se place à 21 en-dessous du haut
			$this->SetY(30);
			
			//Arial gras 12
			$this->SetFont('Arial','B',12);
			
			//Affichage des en-têtes du tableau
			$this->cell(110,0,utf8_decode('Désignation'),0,0,'C');
			$this->cell(30,0,utf8_decode('N° Article'),0,0,'C');
			$this->cell(15,0,utf8_decode('Qté'),0,0,'C');
			$this->cell(23,0,utf8_decode('Prix/unité'),0,0,'C');
			$this->cell(23,0,'Prix total',0,0,'C');
			
			//Création de la structure "Tableau"
			$this->Line(6, 33, 202, 33);
			$this->Line(111,28,111,33);
			$this->Line(141,28,141,33);
			$this->Line(156,28,156,33);
			$this->Line(179,28,179,33);
		}
	
		// *******************************************************************
		// Nom :	articleInfo
		// But :	Affiche une ligne dans le tableau
		// Retour:	-
		// Param.: 	$iPosX ->	Position en coordonnée X du début de la ligne
		//			$iPosY ->	Position en hauteur de la ligne
		//			$tab_strCartInfo ->	Tableau contenant toutes les infos des articles
		//			$strArticleNum ->	N° de l'article traité
		// *******************************************************************
		function articleInfo($iPosX, $iPosY, $tab_strCartInfo = "", $strArticleNum='')
		{
			
			//Crée une lignes avec 5 cellules dans le tableau
			$intLineHaut = intval($iPosY)-7;
			$intLineBas = intval($iPosY)+1.8;
			$intTxtPosY = intval($iPosY) - 2;
			$this->Line(111,$intLineHaut,111,$intLineBas);
			$this->Line(141,$intLineHaut,141,$intLineBas);
			$this->Line(156,$intLineHaut,156,$intLineBas);
			$this->Line(179,$intLineHaut,179,$intLineBas);
			
			//Change la position
			$this->SetXY($iPosX, $intTxtPosY);
			
			// Arial gras 12
			$this->SetFont('Arial', '', 12);
			
			//Affiche les textes dans les cellules
			$this->Cell(105, 0, utf8_decode($this->AdaptFont($tab_strCartInfo['Bezeichnung'], 105)), 0, 0, "L");
			$this->Cell(30, 0, $this->AdaptFont($strArticleNum, 30), 0, 0, "C");
			$this->Cell(15, 0, $this->AdaptFont($_SESSION['cart'][$strArticleNum], 15), 0, 0, "C");
			$this->Cell(23, 0, $this->AdaptFont(floatval(round($tab_strCartInfo['Kalkulationspreis'],2)). " CHF", 23), 0, 0, "C");
			//$this->Cell(23, 0, $this->AdaptFont(floatval(round($tab_strCartInfo['Kalkulationspreis'], 2),floatval($_SESSION['cart'][$strArticleNum])). " CHF", 23), 0, 0, "C");
			$this->Cell(23, 0, $this->AdaptFont(floatval(round($tab_strCartInfo['Kalkulationspreis'], 2)). "CHF", 23), 0, 0, "C");
					

		
			//Crée une ligne en bas pour passer à la suite.
			$this->Line(6, $intLineBas, 202, $intLineBas);
		}
	
		// *******************************************************************
		// Nom :	AdaptFont
		// But :	Modifie la taille du texte de sorte à ce qu'il entre dans
		//			la cellule sans déborder.
		// Retour:	$strText ->	Le texte à afficher dans la cellule
		// Param.: 	$strText ->	Texte à mettre dans la cellule
		//			$intCellWidth ->	Largeur de la cellule
		// *******************************************************************
		function AdaptFont($strText, $intCellWidth) {
			//Tant que le texte déborde de la cellule, on baisse la taille du texte
			for($i = 11; floatval($this->GetStringWidth($strText)) > floatval($intCellWidth)-1 && $i > 0;$i--) {
				$this->SetFontSize($i);
			}
			//On retourne le texte afin de l'afficher
			return $strText;
		}
	
		// *******************************************************************
		// Nom :	calcTotalPrice
		// But :	Calcule le prix total arrondi au 0.05
		// Retour:	writePrice() ->	Adapte le prix pour qu'il soit affiché
		//							correctement
		// Param.: 	$fltPrice ->	Prix total
		// *******************************************************************
		function calcTotalPrice($tab_strInfos) {
			
			$fltPrice = 0;
			
			foreach($tab_strInfos AS $key => $tab_Infos) {
				$fltPrice += round(floatval(round($tab_Infos['Kalkulationspreis'], 2))*floatval($_SESSION['cart'][$tab_Infos['Artikelnummer']]),2, PHP_ROUND_HALF_EVEN);
			}
			
			$fltPrice = round($fltPrice*2, 1, PHP_ROUND_HALF_EVEN)/2;
			
			return writePrice($fltPrice);
		}
	
		// *******************************************************************
		// Nom :	setTotalPrice
		// But :	Affiche le prix total dans une cellule tout en bas du tableau
		// Retour:	-
		// Param.: 	$iPosX ->	Position à partir de la gauche de la page
		//			$iPosY ->	Position à partir du haut de la page.
		//			$tab_strCatInfo ->	Infos de tous les articles du panier
		// *******************************************************************
		function setTotalPrice($iPosX, $iPosY, $tab_strCartInfo = "")
		{
			//Changement de position et de police
			$this->SetFont('Arial','BU', 12);
			$this->SetXY($iPosX-$this->GetStringWidth("Total")-1, $iPosY);
			$this->Cell($this->GetStringWidth("Total"), 0, "Total", 0, 0, "R");
			
			$this->SetFont('Arial','B', 12);
			$this->SetXY($iPosX, $iPosY);
			
			//Affichage de la cellule avec le prix total correctement formaté.
			$this->Cell(23, 0, $this->AdaptFont($this->calcTotalPrice($tab_strCartInfo). " CHF", 23), 0, 0, "C");
		}
	
		// *******************************************************************
		// Nom :	setProfessor
		// But :	Affiche la demande de visa du professeur
		// Retour:	-
		// Param.: 	$iPosY ->	Position à partir du haut de la page.
		// *******************************************************************
		function setProfessor($iPosY)
		{
			//Changement de position et de police
			$this->SetFont('Arial','BU', 15);
			$this->SetXY(205/2-$this->GetStringWidth("Visa du professeur:")/2, $iPosY);
			
			//Affichage de la cellule avec le prix total correctement formaté.
			$this->Cell(10, 0, "Visa du professeur:", 0, 0, "C");
		}
	}
?>