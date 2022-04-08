//***************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    27.11.2012
// But:     Fichier javascript principal
//***************************************************************
// Modifications:
// Date :   -
// Auteur : -
// Raison:  -
//***************************************************************

var intDivX, intDivY;
var movedDiv;

// *******************************************************************
// Nom :	appendDiv
// But :	Fait apparaître une div d'info et une div qui masque
//			l'arrière-plan.
// Retour:	-
// Param.: 	-
// *******************************************************************
function appendDiv() {
	//On récupère la div qui va contenir les infos et celle
	//qui va 'cacher' la page
	var infosDiv = document.getElementById("item");
	
	$("#articleURL").value = window.location.href;
	
	//Centrage de la div horizontalement et verticalement
	if(window.innerHeight && window.innerWidth) {
		infosDiv.style.top = (parseInt(window.innerHeight) - parseInt(infosDiv.offsetHeight)) / 2 + "px";
		infosDiv.style.left = (parseInt(window.innerWidth) - parseInt(infosDiv.offsetWidth)) / 2 + "px";
	}
	//Centrage de la div horizontalement et verticalement
	else if(document.documentElement.clientHeight && document.documentElement.clientWidth) {
		infosDiv.style.top = (parseInt(document.documentElement.clientHeight) - parseInt(infosDiv.offsetHeight)) / 2 + "px";
		infosDiv.style.left = (parseInt(document.documentElement.clientWidth) - parseInt(infosDiv.offsetWidth)) / 2 + "px";
	}

	$("#item").modal('show');
	
	//Evènements relatifs aux touches du clavier
	/*
	document.onkeydown = function(event) {
		event = event || window.event;
		
		//Echap (Esc)
		if (event.keyCode == 27) {
			//Ferme la div
			removeDiv();
			
		}
		
		if (event.keyCode == 37) {
			//Pièce précédente
			
		}
		
		if (event.keyCode == 39) {
			//Pièce suivante
			
		}
	};*/
}

// *******************************************************************
// Nom :	removeDiv
// But :	Fait disparaître la div d'info et la div qui masque
//			l'arrière-plan.
// Retour:	-
// Param.: 	-
// *******************************************************************
function removeDiv() {
	//On récupère la div qui va contenir les infos et celle
	//qui va 'cacher' la page
	var infosDiv = document.getElementById("item");
	
	//Changement de son opacité (compatiblité avec les navigateurs)
	infosDiv.style.opacity = "0";
	infosDiv.style.filter = "alpha(opacity = 0)";
	
	//Passage à l'arrière-plan avec un timeout pour que la transition se fasse.
	setTimeout(function(){ infosDiv.style.zIndex = '-2' }, 200);
	
	//On réinitialise la position de la div
	setTimeout(function(){ 
		infosDiv.style.left = '';
		infosDiv.style.top = '';
	}, 300);
	
	url = removeParameter("article=", document.location.href);
	setListUrl(url);
	
	//On supprime les évènements
	document.onscroll = "";
	document.onkeydown = "";
}

// *******************************************************************
// Nom :	grabDiv
// But :	Récupère les coordonnées de la souris lors d'un clic maintenu
//			et crée les évènement pour le drag and drop
// Retour:	-
// Param.: 	div -> La div qu'on veut déplacer
//			event -> L'évènement qui a lancé la fonction
// *******************************************************************
function grabDiv(div, event) {
	//Si event est vide, on lui assigne l'évènement.
	if(!event) {
		event = event || window.event;
	}
	
	//Empêche de sélectionner le texte en arrière-plan
	event.returnValue = false;
	
	if(event.preventDefault) {
		event.preventDefault();
	}
	
	movedDiv = div;
	
	//Récupération des coordonnées de la souris
	var intPosX = event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft);
	var intPosY = event.clientY + (document.documentElement.scrollTop || document.body.scrollTop);
	

	//Récupération des coordonnées dans l'élément
	var intElementX = 0;
	var intElementY = 0;
	var element = div;
	do {
		intElementX += element.offsetLeft;
		intElementY += element.offsetTop;
		element = element.offsetParent;
	}
	while(element && element.style.position != 'absolute');

	
	//Calcul de la position initiale de la souris dans une variable globale
	intDivX = intPosX - intElementX;
	intDivY = intPosY - intElementY;
	
	//Ajout de l'évènement onMouseMove
	document.onmousemove = moveDiv;
	
	//Suppression des évènements lors de la fin du drag and drop
	document.onmouseup = function() {
		//Fait en sorte que la fenêtre soit tout le temps à l'intérieur de la page.
		if(window.innerWidth && window.innerHeight) {
			//On regarde s'il y a une scrollbar sur la page. Si oui, on applique le décalage.
			var divShift = parseInt(window.innerHeight) < parseInt(document.documentElement.scrollHeight) ? 18 : 0;
			
			if((parseInt(movedDiv.style.left) + parseInt(movedDiv.offsetWidth) + parseInt(divShift)) > window.innerWidth) {
				movedDiv.style.left = parseInt(window.innerWidth) - parseInt(movedDiv.offsetWidth) - parseInt(divShift) + "px";
			}
		
			//On regarde s'il y a une scrollbar horizontale sur la page. Si oui, on applique le décalage.
			var divShift = parseInt(window.innerWidth) < parseInt(document.documentElement.scrollWidth) ? 18 : 0;
			
			//On corrige de 52px pour que ça soit pile la bonne taille (testé et approuvé, seal of approval)
			if((parseInt(movedDiv.style.top) + parseInt(movedDiv.offsetHeight) + parseInt(divShift)) > (window.innerHeight - 52)) {
				movedDiv.style.top = (parseInt(window.innerHeight) - parseInt(movedDiv.offsetHeight) - parseInt(divShift) - 52) + "px";
			}
		}
		
		//Empêche la pop-up de sortir sur la gauche
		if(parseInt(movedDiv.style.left) < 0) {
			movedDiv.style.left = 0 + "px";
		}
		
		//Empêche la pop-up de sortir en haut
		if(parseInt(movedDiv.style.top) < 0) {
			movedDiv.style.top = 0 + "px";
		}
		
		document.onmousemove = "";
		document.onmouseup = "";
	};
}

// *******************************************************************
// Nom :	moveDiv
// But :	Déplace une div en suivant la souris
// Retour:	-
// Param.: 	event -> L'évènement qui a lancé la fonction
// *******************************************************************
function moveDiv(event) {
	//Si event est vide, on lui assigne l'évènement.
	if(!event) {
		event = event || window.event;
	}

	//Calcul de la nouvelle position de la souris
	var intPosX = event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft);
	var intPosY = event.clientY + (document.documentElement.scrollTop || document.body.scrollTop);
	
	//On applique le décalage
	intPosX -= intDivX;
	intPosY -= intDivY;

	//Pour pouvoir déplacer la div
	movedDiv.style.position = 'absolute';
	
	//Changement des positions de la div
	movedDiv.style.left = intPosX + 'px';
	movedDiv.style.top = intPosY + 'px';
	
	//On remet la div en fixed afin qu'elle ne bouge plus quand on scroll
	movedDiv.style.position = 'fixed';
}


//------------ PUUSHSTATE NE FONCTIONNE PAS SUR IE < 10 ------------//

// *******************************************************************
// Nom :	setArticleUrl
// But :	Met à jour l'url dans l'historique pour qu'il contienne une
//			référence à l'article visualisé. Ne recharge pas la page.
// Retour:	-
// Param.: 	idArticle, int => id de l'article qui est affiché
//			url => Lien à rajouter dans l'historique
// *******************************************************************
function setArticleUrl(idArticle, url) {
	//Teste si le navigateur utilisé est IE 10 ou > ou un autre navigateur.
	if(navigator.appVersion.indexOf("MSIE") == -1 || navigator.appVersion.indexOf("MSIE 1") != -1) {
		//Rajout d'une entrée dans l'historique mais ne rafraîchit pas la page.
		var stateObj = { article: idArticle };
		history.pushState(stateObj, "Affichage d'un article", url);
		updateArticleDetails(idArticle);
	}
	
	//Si le navigateur est IE 9 ou <, on doit rafraîchir la page avec l'url.
	else {
		document.location.href = url;
	}
}


// *******************************************************************
// Nom :	setListUrl
// But :	Met à jour l'url dans l'historique pour que l'url redevienne
//			celle qui était utilisée avant la visualisation de l'article.
// Retour:	-
// Param.: 	url => Lien à rajouter dans l'historique
// *******************************************************************
function setListUrl(url) {
	//Teste si le navigateur utilisé est IE 10 ou > ou un autre navigateur.
	if(navigator.appVersion.indexOf("MSIE") == -1 || navigator.appVersion.indexOf("MSIE 1") != -1) {
		//Rajout d'une entrée dans l'historique mais ne rafraîchit pas la page.
		var stateObj = { page: "List" };
		history.pushState(stateObj, "Liste des pièces", url);
	}
	//Si le navigateur est IE 9 ou <, on doit rafraîchir la page avec l'url.
	else {
		document.location.href = url;
	}
}

// *******************************************************************
// Nom :	removeParameter
// But :	Supprime un paramètre et sa valeur dans un lien
// Retour:	newLink => Nouveau lien après la suppression du paramètre
// Param.: 	parameter => Paramètre à supprimer
//			url => Lien dans lequel supprimer le paramètre
// *******************************************************************
function removeParameter(parameter, url) {
	//Variables contenant le nouvel URL ainsi que les
	//positions de début et de fin du paramètre à supprimer
	var newLink = "";
	var paramStart = url.indexOf(parameter);
	var paramEnd;

	//On teste s'il y a un paramètre avant le paramètre.
	//Si oui, on supprime aussi le "&" du paramètre à supprimer.
	if(url.indexOf("&") < paramStart) {
		paramStart -= 1;
	}
	
	//On regarde s'il y a un paramètre après celui à supprimer.
	paramEnd = url.indexOf("&", (paramStart + parameter.length));
	
	//S'il n'y en a pas ou que le paramètre à supprimer n'est pas trouvé dans le lien,
	//on fait en sorte de ne pas modifier le lien après la fin de paramStart
	if(paramEnd == -1 || paramStart == -1) {
		paramEnd = url.length;
	}
	
	//Recréation du lien
	for(i = 0; i < url.length; i++) {
		//On recrée le lien sans prendre le paramètre à supprimer
		if(i < paramStart || i >= paramEnd) {
			newLink += url[i];
		}
	}
	
	//Retour du lien
	return newLink;
}

// *******************************************************************
// Nom :	checkAll
// But :	Coche ou décoche toutes les checkboxes de la page
// Retour:	-
// Param.: 	checkName -> le nom des checkboxes à cocher/décocher
// *******************************************************************
function checkAll(checkName) {
	//On récupère toutes les checkboxes et on décoche par défaut
	var tab_objCheckboxes = document.getElementsByName(checkName);
	var tab_objInputs = document.getElementsByName("articleQuantity");
	var check = false;
	
	//On teste si le checkbox principal est coché, dans ce cas on coche
	if(document.getElementById('mainArticle').checked) {
		var check = true;
	}
	//Cochage de tous les checkboxes
	for(var i = 0; i < tab_objCheckboxes.length; i++) {
		tab_objCheckboxes[i].checked = check;
		if(check && tab_objInputs[i].value == 0) {
			tab_objInputs[i].value = 1;
		}
	}
}

// *******************************************************************
// Nom :	addToList
// But :	Crée un tableau avec les éléments à ajouter dans le panier
//			ainsi que leur quantité
// Retour:	-
// Param.: 	strCheckGroup -> le nom des checkboxes
// *******************************************************************
function addToList(strCheckGroup) {
	//On récupère le tableau de checkboxes, inputs et initialise celui des IDs
	var tab_objCheckboxes = document.getElementsByName(strCheckGroup);
	var tab_objInput = document.getElementsByName(strCheckGroup + 'Quantity');
	var tab_listIds = new Array();
	
	//On regarde si les cases sont cochées et une valeur numérique est à l'intérieur des cases. Si oui,
	//on ajoute l'ID dans le tableau
	for(var i = 0; i < tab_objCheckboxes.length; i++) {
		if(tab_objCheckboxes[i].checked && !isNaN(tab_objInput[i].value) && tab_objInput[i].value > 0) {
			tab_listIds.push(tab_objCheckboxes[i].value + "," + tab_objInput[i].value);
		}
	}
	
	//Si on n'a aucun objet dans le tableau, on envoie un message d'erreur
	if(tab_listIds.length == 0) {
		alert("Veuillez sélectionner au moins un article !");
	}
	else {
		addToCart(tab_listIds);
	}
}

// *******************************************************************
// Nom :	updateList
// But :	Crée un tableau avec les éléments du panier pour qu'ils
//			soient modifiés
// Retour:	-
// Param.: 	strCheckGroup -> le nom des checkboxes
// *******************************************************************
function updateList(strCheckGroup) {
	//On récupère le tableau de checkboxes, inputs et initialise celui des IDs
	var tab_objCheckboxes = document.getElementsByName(strCheckGroup);
	var tab_objInput = document.getElementsByName(strCheckGroup + 'Quantity');
	var tab_listIds = new Array();
	
	//On regarde si les cases sont cochées et une valeur numérique est à l'intérieur des cases. Si oui,
	//on ajoute l'ID dans le tableau
	for(var i = 0; i < tab_objCheckboxes.length; i++) {
		if(!isNaN(tab_objInput[i].value) && tab_objInput[i].value > 0) {
			tab_listIds.push(tab_objCheckboxes[i].value + "," + tab_objInput[i].value);
		}
		else if(tab_objInput[i].value == 0) {
			//S'il y a au moins un objet à supprimer du panier et que
			if(confirm("Voulez-vous vraiment supprimer cet article de votre panier ?")) {
				deleteFromCart(tab_objCheckboxes[i].value);
			}
		}
	}
	
	//Si on n'a aucun objet dans le tableau, on envoie un message d'erreur
	if(tab_listIds.length == 0) {
		alert("Veuillez sélectionner au moins un article !");
	}
	else {
		updateCart(tab_listIds);
	}
}

// *******************************************************************
// Nom :	deleteSelected
// But :	Crée un tableau avec les éléments à supprimer du panier
// Retour:	-
// Param.: 	strCheckGroup -> le nom des checkboxes
// *******************************************************************
function deleteSelected(strCheckGroup) {
	//On récupère le tableau de checkboxes
	var tab_objCheckboxes = document.getElementsByName(strCheckGroup);
	var tab_listIds = new Array();
	
	//Ajoute les cases cochées dans le tableau d'éléments
	for(var i = 0; i < tab_objCheckboxes.length; i++) {
		if(tab_objCheckboxes[i].checked) {
			tab_listIds.push(tab_objCheckboxes[i].value);
		}
	}
	
	//Message de confirmation
	var strConfirmMessage = "";
	
	//Actions à faire en fonction du nombre de cases cochées
	switch(tab_listIds.length) {
		case 0:
			//Message d'erreur
			alert("Veuillez sélectionner au moins un article !");
			break;
		case 1:
			//Message de confirmation au singulier
			strConfirmMessage = "Etes-vous vraiment sûr de vouloir supprimer cet article de votre panier ?";
			break;
		default:
			//Si + de 1, message de confirmation au pluriel
			strConfirmMessage = "Etes-vous vraiment sûr de vouloir supprimer ces "+tab_listIds.length+" articles de votre panier ?";
			break;
	}
	
	//S'il y a au moins un objet à supprimer du panier et que
	if(tab_listIds.length != 0 && confirm(strConfirmMessage)) {
		deleteFromCart(tab_listIds);
	}
}

// *******************************************************************
// Nom :	resizeImage
// But :	Redimmensionner les images qui sont affichées dans le tooltip
//			si elles sont trop grandes.
// Retour:	-
// Param.: 	objImg -> l'image à tester
// *******************************************************************
function resizeImage(objImg) {
	//On garde en mémoire la taille de base de l'image
	var intHeight = objImg.height;
	var intWidth = objImg.width;
	
	//test de la hauteur
	if(objImg.height > window.innerHeight/2-30){
		objImg.height = window.innerHeight/2 -30;
	}
	//Test de la largeur en prenant l'éventuelle nouvelle hauteur en compte
	if(objImg.width*(objImg.height/intHeight) > (window.innerWidth/2)) {
		objImg.width = window.innerWidth/2;
		objImg.height = intHeight*(objImg.width/intWidth)
	}
}

function changePage(strUrl, intNewPage, intNbPages) {
	
	if(!isNaN(intNewPage) && intNewPage >= 1 && intNewPage <= intNbPages && intNewPage != null && intNewPage != "") {
		document.location.href=strUrl+Math.floor(intNewPage);
	}
	else {
		alert("Erreur: Veuillez entrer un nombre compris entre 1 et "+intNbPages+" !");
	}
}

function testPrintForm(tab_strInfos) {
	console.log(tab_strInfos);
	
	var blnReturn = true;
	var tab_strFailInputs = new Array();
	var strProblemText = '';
	
	for(var i = 0; i < tab_strInfos.length; i++) {
		if(tab_strInfos[i]['value'] == '' && tab_strInfos[i]['name'] != 'Reference') {
			blnReturn = false;
			
			tab_strFailInputs.push(tab_strInfos[i]['name']);
		}
	}
	
	if(!blnReturn) {
		if(tab_strFailInputs.length == 1) {
			strProblemText = "Veuillez remplir le champ suivant avant d'envoyer l'impression :\n"+tab_strFailInputs[0];
		}
		else  {
			strProblemText = "Veuillez remplir les champs suivants avant d'envoyer l'impression :\n";
			
			for(var i = 0; i < tab_strFailInputs.length; i++) {
				strProblemText += tab_strFailInputs[i];
				if(i+1 != tab_strFailInputs.length) {
					strProblemText += "\n";
				}
			}
		}
		
		alert(strProblemText);
	}
	
	return blnReturn;
}