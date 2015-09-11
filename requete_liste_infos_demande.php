<?php
$req_liste_infos = $bdd_supervision->prepare('SELECT
		 Code_Client,
		 Demandeur,
		 Date_Demande,
		 Ref_Demande,
		 Date_Supervision_Demandee,
		 Etat_Demande,
		 Date_PEC,
		 Date_Fin_Traitement,
		 Commentaire,
		 email
	FROM demande WHERE ID_Demande= :ID_Demande');
$req_liste_infos->execute(Array(
		'ID_Demande' => $ID_Demande
)) or die(print_r($req_liste_infos->errorInfo()));
