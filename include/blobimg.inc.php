<?php
require_once("./dbfunctions.inc.php");
	
header('Content-type: image/jpg');

if(isset($_GET['imgid'])) {
	//On va rechercher l'image dans la base de données
	$objDB = new dbIfc();
	$objBlob = $objDB->getImg($_GET['imgid']);
	unset($objDB);
	
	//S'il y a bien une image, on l'affiche
	if(isset($objBlob[0]['Bild']) && $objBlob[0]['Bild'] != "") {
		print($objBlob[0]['Bild']);
	}
	
	//Sinon, on affiche l'image qui dit qu'il n'y en a pas.
	else{
		$image = '../images/noimg.jpg';
		readfile($image);
	}

}
//Sinon, on affiche l'image qui dit qu'il n'y en a pas.
else{
	$image = '../images/noimg.jpg';
	readfile($image);
}
?>