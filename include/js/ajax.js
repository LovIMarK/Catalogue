// ********************************************************************
// Nom:	getXhr
// But:	Creer un objet XmlHttpRequest. Adapté aux différents navigateurs
// Param.:	-
// Retour:	l'objet XmlHttpRequest ou false si le navigateur ne supporte
//			pas les objets XmlHttpRequest.
// ********************************************************************
function getXhr() {
	// objet XmlHttpRequest
	var xhr = null;
	// pour Firefox et autres
	if(window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	
	// pour Internet Explorer
 	else if(window.ActiveXObject) {
		try {
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	
	// XMLHttpRequest non supporté par le navigateur
	else {
		alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
		xhr = false;
	}
	
	// retour de l'objet
	return xhr;
} // getXhr()

// ********************************************************************
// Nom: updateArticleDetails
// But: Envoyer une requête afin de mettre à jour les détails de l'objet.
// Param.: idArticle => Article à mettre à jour
// Retour: -
// ********************************************************************
function updateArticleDetails(idArticle) {
	var xhr = getXhr();

	console.log(xhr);
	
	//On définit ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function() {
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        // readyState: 0=non initialisé, 1=connexion établie,2=requête reçue,3=réponse en cours et 4=terminé.
		// status: 200=ok et 404=page non trouvée
		if(xhr.readyState == 4 && xhr.status == 200) {
			articleReturn = xhr.responseText;
			
			//On se sert de innerHTML pour éditer le contenu de la div.
			document.getElementById('infos').innerHTML = articleReturn;
			console.log(articleReturn);
			//$('#articleURL').innerHTML = '<span id="articleUrlText">Lien de cette pièce:</span><input type="text" onClick="this.select();" id="articleUrl" name="articleUrl" readonly />'
			appendDiv();
		}
	}
	//Ouverture de la page avec envoi de données en mode POST
	xhr.open("POST", "include/iteminfos.inc.php", true);
	
	//Ne pas oublier le header
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	
	//Les arguments à utiliser:
	xhr.send("articleToWrite=" + idArticle);
}

// *******************************************************************
// Nom :	addToCart
// But :	Appelle la page "cartquantity.inc.php" pour ajouter les nouveaux
//			articles dans $_SESSION['cart']
// Retour:	-
// Param.: 	tab_strItemsToAdd => Tableau contenant les éléments à mettre dans le panier
// *******************************************************************
function addToCart(tab_strItemsToAdd) {
	var xhr = getXhr();
	
	//On définit ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function() {
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        // readyState: 0=non initialisé, 1=connexion établie,2=requête reçue,3=réponse en cours et 4=terminé.
		// status: 200=ok et 404=page non trouvée
		if(xhr.readyState == 4 && xhr.status == 200) {
			cartReturn = xhr.responseText;
			
			document.getElementById('cartItems').innerHTML = cartReturn;
		}
	}
	
	//Ouverture de la page avec envoi de données en mode POST
	xhr.open("POST", "include/cartquantity.inc.php", true);
	
	//Ne pas oublier le header
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	
	//Les arguments à utiliser:
	xhr.send("articlesToAdd=" + tab_strItemsToAdd);
}

// *******************************************************************
// Nom :	updateCart
// But :	Appelle la page "cartquantity.inc.php" pour mettre à jour les
//			articles de $_SESSION['cart']
// Retour:	-
// Param.: 	tab_strItemsToAdd => Tableau contenant les éléments
// *******************************************************************
function updateCart(tab_strItemsToUpdate) {
	var xhr = getXhr();
	
	//On définit ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function() {
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        // readyState: 0=non initialisé, 1=connexion établie,2=requête reçue,3=réponse en cours et 4=terminé.
		// status: 200=ok et 404=page non trouvée
		if(xhr.readyState == 4 && xhr.status == 200) {
			cartReturn = xhr.responseText;
			
			document.getElementById('cartItems').innerHTML = cartReturn;
		}
	}
	
	//Ouverture de la page avec envoi de données en mode POST
	xhr.open("POST", "include/cartquantity.inc.php", true);
	
	//Ne pas oublier le header
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	
	//Les arguments à utiliser:
	xhr.send("update=" + tab_strItemsToUpdate);
}

// *******************************************************************
// Nom :	deleteFromCart
// But :	Appelle la page "cart.inc.php" et supprime les éléments 
//			sélectionnés du panier
// Retour:	-
// Param.: 	tab_strItemsToDelete => Tableau contenant les éléments à supprimer
// *******************************************************************
function deleteFromCart(tab_strItemsToDelete) {
	var xhr = getXhr();
	
	//On définit ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function() {
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        // readyState: 0=non initialisé, 1=connexion établie,2=requête reçue,3=réponse en cours et 4=terminé.
		// status: 200=ok et 404=page non trouvée
		if(xhr.readyState == 4 && xhr.status == 200) {
			cartReturn = xhr.responseText;
			
			document.getElementById('cartContent').innerHTML = cartReturn;
		}
	}
	
	//Ouverture de la page avec envoi de données en mode POST
	xhr.open("POST", "include/cart.inc.php", true);
	
	//Ne pas oublier le header
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	
	//Les arguments à utiliser:
	xhr.send("articlesToDelete=" + tab_strItemsToDelete);
}

// *******************************************************************
// Nom :	refreshCart
// But :	Appelle la page "cartquantity.inc.php" et rafraichit le
//			nombre d'articles présents à l'intérieur du panier
// Retour:	-
// Param.: 	
// *******************************************************************
function refreshCart() {
	var xhr = getXhr();
	
	//On définit ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function() {
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        // readyState: 0=non initialisé, 1=connexion établie,2=requête reçue,3=réponse en cours et 4=terminé.
		// status: 200=ok et 404=page non trouvée
		if(xhr.readyState == 4 && xhr.status == 200) {
			cartReturn = xhr.responseText;
			
			document.getElementById('cartItems').innerHTML = cartReturn;
		}
	}
	
	//Ouverture de la page avec envoi de données en mode POST
	xhr.open("POST", "include/cartquantity.inc.php", true);
	
	//Ne pas oublier le header
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	
	//Les arguments à utiliser:
	xhr.send();
}