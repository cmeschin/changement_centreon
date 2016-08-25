<?php
$req_liste_service = $bdd_supervision->prepare(
	'SELECT DISTINCT(S.Nom_Service) AS Nom_Service,
			S.Nom_Hote AS Nom_Hote,
			H.IP_Hote AS IP_Hote,
			H.ID_Localisation AS ID_Localisation,
			S.Nom_Periode AS Nom_Periode,
			S.Frequence AS Frequence,
			S.Consigne AS Consigne,
			S.Controle_Actif AS Controle_Actif,
			MS.Modele_Service AS MS_Modele_Service,
			MS.MS_Libelles AS MS_Libelles,
			S.Parametres AS Parametres,
			S.Detail_Consigne AS Detail_Consigne,
			S.Type_Action AS Type_Action,
			S.Etat_Parametrage AS Etat_Parametrage,
			S.ID_Service AS ID_Service,
			S.Commentaire AS Commentaire,
			MS.MS_Description AS MS_Description,
			MS.MS_Arguments AS MS_Arguments,
			MS.MS_Macro AS MS_Macro,
			MS.MS_EST_MACRO AS MS_EST_MACRO,
			H.ID_Hote AS ID_Hote,
			S.ID_Hote_Centreon AS ID_Hote_Centreon,
			S.ID_Service_Centreon AS ID_Service_Centreon,
		 	S.motif_annulation
	FROM ((service AS S 
		LEFT JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service)
		LEFT JOIN hote AS H ON S.ID_Hote=H.ID_Hote)
	WHERE S.ID_Demande= :ID_Demande
	ORDER BY H.ID_Localisation, S.Nom_Hote, S.Nom_Service'); // Ajout tri par loc Nom hote et nom service le 28/10/2014
$req_liste_service->execute(Array(
	'ID_Demande' => $ID_Demande
	)) or die(print_r($req_liste_service->errorInfo()));

/**
 *  toute modification des champs de la requête impacte les pages php suivantes: gestion_arguments.php, remplissage_DEC_service.php, remplissage_param_service.php et insere_fieldset_Admin_Service.php
 *		=> normalement la modif n'a plus d'impact à condition d'utiliser le nom de champs.
	Detail de la requête
	Nom_Service			0
	Nom_Hote				1
	IP_Hote				2
	ID_Localisation		3
	Nom_Periode			4
	Frequence			5
	Consigne				6
	Controle_Actif		7
	MS_Modele_Service	8
	MS_Libelles			9
	Parametres			10
	Detail_Consigne		11
	Type_Action			12
	Etat_Parametrage		13
	ID_Service			14
	Commentaire			15
	MS_Description		16
	MS_Arguments			17
	MS_Macro				18
	MS_EST_MACRO			19
	ID_Hote				20
	ID_Hote_Centreon		21
	ID_Service_Centreon	22
  
 */
