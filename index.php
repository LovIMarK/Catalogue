<!DOCTYPE html>

<!--*************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    08.11.2012
// But:     Page d'accueil où les informations seront affichées
//***************************************************************
// Modifications:
// Date  :  08.04.2022
// Auteur:  João Pedro Ferrera Magalhães Rodrigues, Béchir Boumaza, Mark Lovink
// Raison:  Modifications avec la TODO liste
//************************************************************-->

<html>
	<head>
		<title>Catalogue ETML - Index</title>
		<meta charset="UTF-8">
		<?php require_once("include/includes.inc.php");?>
	</head>
	<body style="margin-top:20px;">

			<?php	
				print("<div id='container' style='position:relative;'>");
				
				createSearch();
				
				print("<br /><br />\n\n");
				
				getPagesList();
				
				print("<br /><br />\n\n");
				
				createlist("shop");
				
				print("<br /><br />\n\n");
				
				getPagesList();
				
				print("</div>");
			?>
	</body>
</html>