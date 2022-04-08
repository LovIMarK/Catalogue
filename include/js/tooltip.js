//***************************************************************
// Societe: ETML 
// Auteur:  Bryan Perroud
// Date:    19.11.2012
// But:     Fichier javascript contenant l'objet tooltip()
//***************************************************************
// Modifications:
// Date :   -
// Auteur : -
// Raison:  -
//***************************************************************

// *******************************************************************
// Nom :	tooltip
// But :	Crée une infobulle au passage sur l'objet qui suit la souris.
//			Cache l'infobulle lorsqu'on sort de l'objet avec la souris.
// Retour:	-
// Param.: 	-
// *******************************************************************
function tooltip(){
	//Variable qui contiendra les informations de la bulle d'infos
	this.tooltipDiv = document.getElementById('tooltip');
	
	//Variables qui vont contenir les positions
	this.intPosX = 0;
	this.intPosY = 0;
	
	//Les méthodes de l'objet
	this.createTooltip = createTooltip;	//Appelle toutes les autres fonctions et ajoute les évènements
	this.append = append;				//Montre l'infobulle
	this.changePos = changePos;			//Rafraichît la position de l'infobulle
	this.hide = hide;					//Cache l'infobulle
	
	// *******************************************************************
	// Nom :	createTooltip
	// But :	Appelle la fonction d'affichage et met en place les évènements
	//			pour les évènements onmousemove et onmouseout
	// Retour:	-
	// Param.: 	event -> L'évènement déclenché et ses informations
	//			element -> L'élément sur lequel on veut afficher l'infobulle
	//			strTexte -> Le texte à mettre à l'intérieur de l'infobulle
	// *******************************************************************
	function createTooltip(event, element, strText) {
		//Teste s'il n'y a pas d'évènement
		if(!event) {
			event = window.event;
		}
		
		//Affiche l'infobulle
		this.append(event, strText);
		
		//Ajout des évènements
		//Au déplacement de la souris, actualise les coordonnées X et Y
		element.onmousemove = function(event) {
			new tooltip().changePos(event);
		};
		
		//Cache la div et supprime les évènements lorsqu'on sort de l'élément
		element.onmouseout = function() {
			new tooltip().hide();
			this.onmousemove = "";
			this.onmouseout = "";
		};
	}
	
	// *******************************************************************
	// Nom :	append
	// But :	Affiche l'infobulle
	// Retour:	-
	// Param.: 	event -> L'évènement déclenché et ses informations
	//			strTexte -> Le texte à mettre à l'intérieur de l'infobulle
	// *******************************************************************
	function append(event, strText) {		
		//Teste s'il n'y a pas d'évènement
		if(!event) {
			var event = window.event;
		}
		
		//Met les positions de la div à jour
		this.changePos(event);
		
		//Met le texte dans la div
		this.tooltipDiv.innerHTML = strText;
		this.tooltipDiv.style.display = "inline-block";
	}

	// *******************************************************************
	// Nom :	changePos
	// But :	Change la position de l'infobulle pour qu'elle suive la souris
	// Retour:	-
	// Param.: 	event -> L'évènement déclenché et ses informations
	// *******************************************************************
	function changePos(event) {
		//Teste s'il n'y a pas d'évènement
		if(!event) {
			var event = window.event;
		}
		
		//La différence entre pageX/Y et clientX/Y est que pageX/Y prend la position par rapport à la page entière, peu
		//importe ce que l'on voit et clientX/Y prend la position par rapport à la taille de la page que l'on voit.
		
		//Récupère les positions X et Y par rapport à la page entière si possible
		if(event.pageX || event.pageY) {
			this.intPosX = event.pageX;
			this.intPosY = event.pageY;
		}
		
		//Récupère les positions X et Y par rapport à ce qu'on voit de la page actuelle
		//Ajoute la différence avec les scrolls. Se fait uniquement si les positions X ou Y n'ont pas déjà été déterminées ci-dessus.
		else if(event.clientX || event.clientY) {
			this.intPosX = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			this.intPosY = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		}
		
		//On met les positions de la div pour qu'elles soient en haut à droite du curseur
		this.tooltipDiv.style.left = this.intPosX + 25 + "px";	//Coordonnée X
		
		//Si le curseur est situé dans la moitié du bas de la page, affiche la div vers le haut.
		if(this.intPosY > (parseInt(window.innerHeight)/2 + document.body.scrollTop + document.documentElement.scrollTop)){
			this.tooltipDiv.style.top = this.intPosY - this.tooltipDiv.offsetHeight - 10 + "px";	//Coordonnée Y
			this.tooltipDiv.setAttribute("class", "tooltip-top");
		}
		//Si le curseur est situé dans la moitié du haut de la page, affiche la div vers le haut.
		else {
			this.tooltipDiv.style.top = this.intPosY + "px"; //Coordonnée Y
			this.tooltipDiv.setAttribute("class", "tooltip-bottom");
		}
	}

	// *******************************************************************
	// Nom :	hide
	// But :	Cache l'infobulle
	// Retour:	-
	// Param.: 	-
	// *******************************************************************
	function hide(){
		//Cache l'infobulle
		this.tooltipDiv.style.display = "none";
	}
}