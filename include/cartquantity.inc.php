<?php
//On contrôle s'il y a déjà une session. Si non, on la démarre.
if(session_id() == "") {
	session_start();
}
	
//On teste si la variable est bien passée en paramètre
if(isset($_POST['articlesToAdd'])) {
	//On recrée le tableau d'origine
	$tab_Temp = explode(",",$_POST['articlesToAdd']);
	for($i = 0; $i < count($tab_Temp); $i++) {
		$objArticle[$tab_Temp[$i]] = $tab_Temp[$i+1];
		$i++;
	}
	$blnUpdate = false;
}
	
//On teste si la variable est bien passée en paramètre
if(isset($_POST['update'])) {
	//On recrée le tableau d'origine
	$tab_Temp = explode(",",$_POST['update']);
	for($i = 0; $i < count($tab_Temp); $i++) {
		$objArticle[$tab_Temp[$i]] = $tab_Temp[$i+1];
		$i++;
	}
	$blnUpdate = true;
}

//Traitement à faire uniquement s'il y a déjà qqch dans le panier et que le paramètre est bien passé
if(isset($_SESSION['cart']) && isset($objArticle)) {
	
	//On teste chaque article passé en paramètre pour voir s'il figure déjà dans le tableau de la session
	foreach($objArticle as $key => $objId) {
		$blnNotInCart = true;
		for($y = 0; $y < count($_SESSION['cart']); $y++) {
			
			//Si c'est le cas, on ne le rajoutera pas au tableau
			if(isset($_SESSION['cart'][$key]) && !$blnUpdate && $_SESSION['cart'][$key] >= $objId) {
				$blnNotInCart = false;
				break;
			}
		}
		
		//Ajout de l'article au tableau de session s'il n'est pas déjà dedans
		if($blnNotInCart) {
			$_SESSION['cart'][$key] = $objId;
		}
	}
}

//Si le tableau de session n'existe pas et qu'il y a un tableau passé en paramètre,
//On met les valeurs dans le tableau de session.
if(!isset($_SESSION['cart']) && isset($objArticle)) {
	foreach($objArticle as $key => $objId) {
		$_SESSION['cart'][$key] = $objId;
	}
}

//Affichage du nombre d'articles dans le panier
$intNumberOfArticles = 0;

if(isset($_SESSION['cart'])) {

	$intNumberOfArticles = count($_SESSION['cart']);
}

print($intNumberOfArticles);
?>