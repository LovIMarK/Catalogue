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