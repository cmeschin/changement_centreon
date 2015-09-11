<?php
$Select_Demande = $bdd_supervision->prepare('SELECT
		 ID_Demande,
		 Type_Demande,
		 ID_Client_Centreon,
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
		 FROM demande
		 WHERE ID_Demande = :ID_Demande;');
$Select_Demande->execute(array(
		'ID_Demande' => $ID_Demande
)) or die(print_r($Select_Demande->errorInfo()));
