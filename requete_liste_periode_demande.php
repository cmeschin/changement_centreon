<?php
$req_liste_plage = $bdd_supervision->prepare('SELECT
		 Nom_Periode as nom_periode,
		 if(Lundi="","-",Lundi) AS lundi,
		 if(Mardi="","-",Mardi) as mardi,
		 if(Mercredi="","-",Mercredi) as mercredi,
		 if(Jeudi="","-",Jeudi) as jeudi,
		 if(Vendredi="","-",Vendredi) as vendredi,
		 if(Samedi="","-",Samedi) as samedi,
		 if(Dimanche="","-",Dimanche) as dimanche,
		 Commentaire as commentaire,
		 Type_Action as type_action,
		 Etat_Parametrage as Etat_Parametrage,
		 ID_Periode_Temporelle as id_periode_temporelle
	FROM periode_temporelle
	 WHERE Type_Action <> "NC"
		 AND ID_Demande = :ID_Demande
	 ORDER BY Nom_Periode');
$req_liste_plage->execute(Array(
		'ID_Demande' => htmlspecialchars($ID_Demande)
)) or die(print_r($req_liste_plage->errorInfo()));
