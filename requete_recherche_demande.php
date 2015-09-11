<?php
$req_dem = $bdd_supervision->prepare ( 'SELECT D.ID_Demande,
		D.Ref_Demande,
		D.Date_Demande,
		D.Demandeur,
		D.Date_Supervision_Demandee,
		Code_Client,
		(SELECT count(ID_Demande) FROM hote AS H WHERE H.Type_Action NOT IN ("NC") AND H.ID_Demande=D.ID_Demande) AS NbHote,
		(SELECT count(ID_Demande) FROM service AS S WHERE S.ID_Demande=D.ID_Demande) AS NbService,
		(SELECT count(ID_Demande) FROM periode_temporelle AS P WHERE P.Type_Action IN ("Modifier","Creer") AND P.ID_Demande=D.ID_Demande) AS NbPlage,
		D.Etat_Demande,
		D.temps_hote + D.temps_service AS Temps
	FROM demande AS D
	WHERE D.ID_Demande = :ID_Demande
	GROUP BY D.ID_Demande
	ORDER BY D.Date_Demande, D.Date_Supervision_Demandee' );
$req_dem->execute ( array (
		'ID_Demande' => $ID_Demande
) ) or die ( print_r ( $req_dem->errorInfo () ) );
