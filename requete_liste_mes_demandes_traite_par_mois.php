<?php
$req_dem = $bdd_supervision->prepare(
		'SELECT D.ID_Demande,
		D.Ref_Demande,
		D.Date_Demande,
		D.Demandeur,
		D.Date_Supervision_Demandee,
		Code_Client,
		(SELECT count(ID_Demande) FROM hote AS H WHERE H.Type_Action NOT IN ("NC") AND H.ID_Demande=D.ID_Demande) AS NbHote,
		(SELECT count(ID_Demande) FROM service AS S WHERE S.ID_Demande=D.ID_Demande) AS NbService,
		(SELECT count(ID_Demande) FROM periode_temporelle AS P WHERE P.Type_Action IN ("Modifier","Creer") AND P.ID_Demande=D.ID_Demande) AS NbPlage,
		D.Etat_Demande,
		CONCAT(FLOOR(sum(D.temps_hote + D.temps_service)/60),"h",LPAD(sum(D.temps_hote + D.temps_service)%60,2,"00")) as Temps,
		D.Type_Demande,
		D.motif_annulation
	FROM demande AS D
	WHERE D.Etat_Demande IN ("Traité", "Annulé") 
		AND CONCAT(SUBSTRING(Date_Demande,1,4),SUBSTRING(Date_Demande,6,2))= :date_demande_groupee
		AND D.Demandeur = :user 
		GROUP BY D.ID_Demande,
		D.Ref_Demande,
		D.Date_Demande,
		D.Demandeur,
		D.Date_Supervision_Demandee,
		Code_Client,
		NbHote,
		NbService,
		NbPlage,
		D.Etat_Demande,
		D.Type_Demande,
		D.motif_annulation
	ORDER BY D.Date_Supervision_Demandee, D.Date_Demande');
$req_dem->execute(array(
		'date_demande_groupee' => htmlspecialchars($sID_Date),
		'user' => htmlspecialchars($_SESSION['user_changement_centreon'])
)) or die(print_r($req_dem->errorInfo()));
