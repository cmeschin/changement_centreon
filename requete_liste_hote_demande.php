<?php
// Selection de tous les hôtes de la demande
//$req_liste_hote = $bdd_supervision->prepare('SELECT Nom_Hote, IP_Hote, Description, Type_Hote, ID_Localisation, OS, Architecture, Langue, Fonction, Controle_Actif, Commentaire, Consigne, Detail_Consigne, Type_Action, Etat_Parametrage, ID_Hote
//	FROM hote WHERE ID_Demande= :ID_Demande');
// Ajout du tri par localisation et Nom d'hote 28/10/2014

$req_liste_hote = $bdd_supervision->prepare('SELECT
		 Nom_Hote,
		 IP_Hote,
		 Description,
		 Type_Hote,
		 ID_Localisation,
		 OS,
		 Architecture,
		 Langue,
		 Fonction,
		 Controle_Actif,
		 Commentaire,
		 Consigne,
		 Detail_Consigne,
		 Type_Action,
		 Etat_Parametrage,
		 ID_Hote,
		 motif_annulation
	FROM hote
	 WHERE Type_Action <> :Type_Action AND ID_Demande= :ID_Demande
	 ORDER BY ID_Localisation, Nom_Hote');
$req_liste_hote->execute(Array(
		'Type_Action' => "NC",
		'ID_Demande' => htmlspecialchars($ID_Demande)
)) or die(print_r($req_liste_hote->errorInfo()));
