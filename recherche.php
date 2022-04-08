<!DOCTYPE html>

<!--*************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    08.11.2012
// But:     Page avec les résultats des recherches.
//***************************************************************
// Modifications:
// Date  :  -
// Auteur:  -
// Raison:  -
//************************************************************-->

<?php 
	// si on clique sur recherche sans mettre de texte, renvoie sur la page d'accueil
	if(isset($_GET['txtSearch']) && $_GET['txtSearch'] == "") {
		header('Location: index.php');
	}
?>

<html>
	<head>
		<title>Catalogue ETML - <?php if(isset($_GET['txtSearch'])) { print('Articles contenant "'.$_GET['txtSearch'].'"'); } else { print("Erreur"); } ?></title>
		<?php require_once("./include/includes.inc.php"); ?>
	</head>
	<body>

			<?php
				print("<div id='container' style='position:relative; text-align:center;'>");
				
				createSearch();
			
				//On regarde si la recherche retourne quelque chose. Si oui, on fait l'affichage normal
				if(isset($_GET['txtSearch']) && getNumberOfPages() > 0) {
					
					print("<br /><br />");
				
					getPagesList();
					
					print("<br /><br />");
					
					createList();
					
					print("<br /><br />");
					
					getPagesList();
				}
				//Si non, on prévient qu'il y a une erreur et on donne les alternatives
				else {
					print("<br /><br /><br /><br />");
					print("<center><b>Cette requête n'a retourné aucun résultat. Vous pouvez relancer une recherche, <a href='./'>retourner à l'index</a> ou <a href='javascript:history.back()'>revenir à la page précédente</a></b></center>");
				}
				print("</div>");
			
			?>
	</body>
</html>