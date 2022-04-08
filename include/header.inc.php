<?php
$strSearch = "";

//Bezeichnung de recherche
if(isset($_GET['txtSearch'])) {
	$strSearch = $_GET['txtSearch'];
}

//Affichage du formulaire de recherche
?>
<nav style="position:fixed; z-index:6;" class="navbar" id="searchDiv">
	<table style="text-align:center;">
		<tr>
			<td style="width:34%;">
				<b><a href="index.php">Index du catalogue</a></b>
			</td>
			<td style="width:33%;">
				<form id="frmSearch" name="frmSearch" action="../recherche.php" class="form-search" method="get">
				<div class="input-append">
					<input type="search" name="txtSearch" id="txtSearch" class="span2 search-query" placeholder="Entrez votre recherche" value="<?php print($strSearch); ?>" />
					<button type="submit" class="btn"><i class="icon-search"></i></button>
				</div>
				</form>
			</td>
			<td style="width:33%;">
				<a href="../cart.php"><i class="icon-shopping-cart"></i>Paanier(<span id="cartItems">
				<?php
					//Quantité d'articles dans le panier
					include_once("./cartquantity.inc.php");
				?>
				</span>)</a>
			</td>
		</tr>
	</table>
</nav>