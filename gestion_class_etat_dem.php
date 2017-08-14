<?php
switch ($etat_dem) {
	case "Brouillon":
		$etat_class="brou";
		break;
	case "A Traiter":
		$etat_class="atra";
		break;
	case "En cours":
		$etat_class="enco";
		break;
	case "Validation":
		$etat_class="vali";
		break;
	case "Traité":
		$etat_class="trai";
		break;
	case "Annulé":
		$etat_class="annu";
		break;
	case "Supprimer":
		$etat_class="supp";
		break;
	default:
		$etat_class="brou";
};
