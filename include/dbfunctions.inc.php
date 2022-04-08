<?php
//***************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    08.11.2012
// But:     Fichier contenant toutes les fonctions liees à la BDD
//***************************************************************
// Modification:
// Auteur:  Pierric Ripoll
// Date  :  09.10.2019
// Raison:  Mise à jour 2019
//***************************************************************

// objet d'interface a la base de donnees
class dbIfc {

	// *******************************************************************
	// Static attributes
	// *******************************************************************

	const STR_SERVER = "tcp:selectline,1533";
	const STR_DATABASE = "SL_M1";
	
	const STR_USERID = "sa";
	const STR_PASSWORD = "@sl9546";
	
	
	// *******************************************************************
	// Attributes
	// *******************************************************************

	// l'objet de connexion
	private $objConnexion = null;

	// *******************************************************************
	// Nom   :	__construct
	// But   :	Constructeur. Ouvre la connexion a la BD
	// Param.: 	-
	// *******************************************************************
	public function __construct() {
		ini_set('mssql.charset', 'UTF-8');

		// appel de la fonction de connexion
		$this->dbConnect();

	} //end __construct()

	// *******************************************************************
	// Nom   :	__destruct
	// But   :	Destructeur. Deconnexion de la BD
	// *******************************************************************
	public function __destruct() {
		// appel de la fonction de deconnexion
		//$this->dbUnconnect();
		
	} //end __destruct()
	
	// *******************************************************************
	// Nom :	dbConnect
	// But :	Connection à la BD
	// Retour:	-
	// Param.: 	-
	// *******************************************************************
	private function dbConnect() {
		//Infos de connexion (user, password et Database)
		//NOTE: comme UID et PWD ne sont pas précisé, prend le login windows
		$tab_strDbInfos = array("UID"=>self::STR_USERID, "PWD"=>self::STR_PASSWORD, "Database"=>self::STR_DATABASE, "CharacterSet" => "UTF-8");
	
		//Connexion à la BD
		try {
			$this->objConnexion = sqlsrv_connect(self::STR_SERVER, $tab_strDbInfos)
				or die("<center><b>Une erreur s'est produite lors de la connexion à la base de données !<br />Veuillez réessayer plus tard !</b><br /> <br /><pre>".print_r(sqlsrv_errors(), true)."</pre></center>");
		}
		
		catch(Exception $e){
			print('<br /><br />'. $e);
		}
	} //dbConnect()

	// *******************************************************************
	// Nom :	dbUnconnect
	// But :	Deconnection de la BD
	// Retour:	-
	// Param.: 	-
	// *******************************************************************
	private function dbUnconnect() {
		if ($this->objConnexion != null) {
			//Ferme la connexion s'il en existe une
			sqlsrv_close($this->objConnexion);
		}
	} //dbUnconnect()
	
	
	// *******************************************************************
	// Nom :	executeQuery
	// But :	Exécute une requête SQL. 
	// Retour:	le recordSet de la requete (null en cas d'erreur)
	// Param.: 	$strQuery = requete SQL a executer
	// *******************************************************************
	private function executeQuery($strQuery){
		//Connection à la BD
		if ($this->objConnexion) {
			$this->dbConnect();
		}
		
		
		//Execution de la requete et recuperation du recordset
		$stmtResult = sqlsrv_query($this->objConnexion, $strQuery);		
		
		//Renvoi des Statements
		return $stmtResult;
	} //executeQuery()
	
	// *******************************************************************
	// Nom :	selectQuery
	// But :	realise une requete de type "Select" dans la BD
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.: 	$strQuery -> la requete a realiser
	// *******************************************************************
	private function selectQuery($strQuery) {		
		//Le tableau de retour
		$tab_values = array();
		
		//Lancement de la requête
		$stmtResult = $this->executeQuery($strQuery);

		//Pour savoir si la requete renvoie au moins un record
		if($stmtResult && sqlsrv_has_rows($stmtResult)) {
			//Si oui, on crée le tableau
			for($i = 0; $row = sqlsrv_fetch_array($stmtResult, SQLSRV_FETCH_ASSOC);$i++){
				$tab_values[$i] = $row;
			}
			sqlsrv_free_stmt($stmtResult);

			//Deconnection de la BD
			$this->dbUnconnect();
			
			//Renvoi du tableau
			return $tab_values;
		} //If au moins un record
		
		//Renvoi du tableau vide
		return $tab_values;
	} //selectQuery()
	
	// *******************************************************************
	// Nom :	getList
	// But :	Va rechercher les données pour la liste des pièces.
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.: 	$intLimit ->	Défaut à 20, nombre de résultats à retourner
	//			$intNoPage ->	Défaut à 1, permet de déterminer entre quels
	//							numéros de ligne prendre les données
	//			$strSortCol ->	Défaut: "Artikelnummer", colonne sur laquelle
	//							les données vont être triées
	//			$strSorting ->	Défaut: "asc", ordre de tri
	//			$strSearch ->	Défaut: null, recherche éventuelle à faire.
	// *******************************************************************
	public function getList($intLimit = 20, $intNoPage = 1, $strSortCol = "ART.Artikelnummer", $strSorting = "asc", $strSearch = null) {
	
		$strWhere = " WHERE ShopAktiv = 1";
		
		//S'il y a une recherche à faire, on ajouter un WHERE à la requête.
		if(!is_null($strSearch)) {
			$strWhere .= " AND UPPER(Bezeichnung) LIKE '%".strtoupper($strSearch)."%' 
			OR UPPER(Zusatz) LIKE '%".strtoupper($strSearch)."%' 
			OR UPPER(ART.Artikelnummer) LIKE '%".strtoupper($strSearch)."%'";
		}
	
		//Requête
		$strQuery = "SELECT Bezeichnung, Zusatz, Artikelnummer, Kalkulationspreis FROM (
		
						SELECT ROW_NUMBER() OVER (
							ORDER BY ".$strSortCol." ".$strSorting."
						) AS RowNumber, Bezeichnung, Zusatz, ART.Artikelnummer, Kalkulationspreis
						
						FROM dbo.ART
						LEFT OUTER JOIN dbo.ARKALK
						
						ON dbo.ART.Artikelnummer = dbo.ARKALK.Artikelnummer
						
						".$strWhere."
					) c 
					
					WHERE RowNumber
					BETWEEN ".($intLimit*($intNoPage-1)+1)." AND ".$intLimit*$intNoPage;
		
		//Exécution et retour des données
		return $this->selectQuery($strQuery);
	}
		
	// *******************************************************************
	// Nom :	getItemsNumber
	// But :	Va rechercher le nombre de données présente dans la BDD
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.:	$strSearch ->	Défaut: null, recherche éventuelle à faire
	//							avant de compter.
	//			$strSortCol ->	Défaut: "Artikelnummer", colonne sur laquelle
	//							les données vont être triées
	//			$strSorting -> Défaut: "asc", ordre de tri
	// *******************************************************************
	public function getItemsNumber($strSearch = null) {
		$strWhere = " WHERE ShopAktiv = 1";
	
		//S'il y a une recherche à faire, on ajouter un WHERE à la requête.
		if($strSearch) {
			$strWhere .= " AND UPPER(Bezeichnung) LIKE '%".strtoupper($strSearch)."%' OR UPPER(Zusatz) LIKE '%".strtoupper($strSearch)."%' OR UPPER(Artikelnummer) LIKE '%".strtoupper($strSearch)."%'";
		}
		
		//Requête
		$strQuery = "SELECT count(Artikelnummer) AS 'nb_articles' FROM dbo.ART".$strWhere;
		
		//Exécution de la requête
		$tab_value = $this->selectQuery($strQuery);
		
		//Si la requête s'est bien passée,
		//on retourne le nombre de lignes totales
		if($tab_value) {
			return $tab_value[0]['nb_articles'];
		}
		
		//Sinon, on retourne le tableau vide
		//Le tableau ne sera pas affiché.
		return null;
	}
		
	// *******************************************************************
	// Nom :	getItemInfos
	// But :	Va rechercher les informations sur une pièce dans la BDD
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.:	$intArticleNum ->	Défaut: ID de l'article à rechercher
	// *******************************************************************
	public function getItemInfos($intArticleNum) {
		//Requête
		$strQuery = "SELECT Artikelnummer, Bezeichnung, Zusatz FROM dbo.ART WHERE Artikelnummer = '".$intArticleNum."' AND ShopAktiv = 1";
		
		//Exécution de la requête
		return $this->selectQuery($strQuery);
	}
		
	// *******************************************************************
	// Nom :	getCartInfos
	// But :	Va rechercher les informations sur les pièce du panier dans la BDD
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.:	$tab_strInfos ->	Tableau contenant les IDs des pièces à rechercher
	// *******************************************************************
	public function getCartInfos($intLimit = 20, $intNoPage = 1, $tab_ArticleNum, $strSortCol = "Artikelnummer", $strSorting = "asc") {
		$blnFirst = true;
		
		//Début de la construction de la requête
		$strQuery = "SELECT Bezeichnung, Zusatz, Artikelnummer, Kalkulationspreis FROM (
						
						SELECT ROW_NUMBER() OVER (
							ORDER BY ".$strSortCol." ".$strSorting."
						)
						AS RowNumber, Bezeichnung, Zusatz, ART.Artikelnummer, Kalkulationspreis
						
						FROM dbo.ART
						LEFT OUTER JOIN dbo.ARKALK
						
						ON dbo.ART.Artikelnummer = dbo.ARKALK.Artikelnummer
						
						WHERE ART.Artikelnummer
						IN ('";
						
						//Ajout des No d'article dans le "IN"
						foreach($tab_ArticleNum AS $strArticleNum => $intQuantity) {
							//Pour le premier No d'article, pas de virgule
							if($blnFirst) {
								$strQuery .= $strArticleNum;
								$blnFirst = false;
							}
							//A partir du second => Virgule avant le No d'article
							else {
								$strQuery .= "', '".$strArticleNum;
							}
						}
						
		$strQuery .= "')
			AND ShopAktiv = 1
				) table_temp
				WHERE RowNumber
				BETWEEN ".($intLimit*($intNoPage-1)+1)." AND ".$intLimit*$intNoPage;
			
		//Exécution de la requête
		return $this->selectQuery($strQuery);
	}
		
	// *******************************************************************
	// Nom :	getImg
	// But :	Va rechercher l'image de l'article dans la BDD
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.:	$strImgId ->	id de l'image de l'article à rechercher
	// *******************************************************************
	public function getImg($strImgId) {
		//Requête
		$strQuery = "SELECT Bild FROM dbo.BILD WHERE Blobkey LIKE '%".$strImgId."'";
		
		//Exécution de la requête
		return $this->selectQuery($strQuery);
	}
		
	// *******************************************************************
	// Nom :	getPrice
	// But :	Va rechercher le prix des articles affichés
	// Retour:	le resultat de la requete sous forme de tableau associatif
	// Param.:	$tab_strArticleNum ->	Tableau contenant les N° d'article
	//									associés aux prix.
	// *******************************************************************
	public function getPrice($tab_strArticleNum) {
		//Requête
		$strQuery = "SELECT Kalkulationspreis FROM dbo.ARKALK WHERE Artikelnummer IN ('";
		
		//Boucle afin de mettre tous les N° d'article dans le IN
		foreach($tab_strArticleNum as $key => $tab_strDatas) {
			if($key != 0){
				$strQuery .= "', '";
			}
			
			$strQuery .= $tab_strDatas['Artikelnummer'];
		}
		
		//Fin du IN
		$strQuery .= "')";
		
		//Exécution de la requête
		return $this->selectQuery($strQuery);
	}
}
?>