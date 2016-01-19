<?php
if (session_id()=='')
{
	session_start();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement enregistrement_donnees.php");

$sinfo_gen = (isset($_POST["info_gen"])) ? $_POST["info_gen"] : NULL;
$sliste_hote = (isset($_POST["liste_hote"])) ? $_POST["liste_hote"] : NULL;
$sliste_service = (isset($_POST["liste_service"])) ? $_POST["liste_service"] : NULL;
$sliste_plage = (isset($_POST["liste_plage"])) ? $_POST["liste_plage"] : NULL;

$info_gen = explode("$",$sinfo_gen); // découpe la chaine en tableau avec comme séparateur le $
$liste_hote = explode("$",$sliste_hote); // découpe la chaine en tableau avec comme séparateur le $
$liste_service = explode("$",$sliste_service); // découpe la chaine en tableau avec comme séparateur le $
$liste_plage = explode("$",$sliste_plage); // découpe la chaine en tableau avec comme séparateur le $
// chaque tableau devra être redécoupé pour mise à jour unitaire

$ID_Demande = htmlspecialchars($_SESSION['ID_dem']);

if ($info_gen[0] != "")  // S'il y a des données ce qui doit forcément être la cas
{
	addlog("Demandeur=".($info_gen[0]));
	addlog("Date_Demande=".($info_gen[1]));
	addlog("Etat_Demande=".($info_gen[2]));
	addlog("Ref_Demande=".($info_gen[3]));
	addlog("Code_Client=".($info_gen[4]));
	addlog("Date_supervision=".($info_gen[5]));
	addlog("email=".($info_gen[6]));
	addlog("Commentaire=".($info_gen[7]));

	addlog("MAJ Table Demande");
	$MAJ_infos = $bdd_supervision->prepare('UPDATE demande 
		SET Date_Demande= :Date_Demande,
			Date_Supervision_Demandee= :Date_Supervision_Demandee,
			Commentaire= :Commentaire,
			email= :email
		WHERE ID_Demande= :ID_Demande;');
	$MAJ_infos->execute(array(
		'Date_Demande' => date("Y-m-d H:i:s"), // date de la demande mise à jour au moment de la validation de la demande
		'Date_Supervision_Demandee' => htmlspecialchars($info_gen[5]),
		'Commentaire' => htmlspecialchars($info_gen[7]),
		'email' => htmlspecialchars(strtolower($info_gen[6])), // forçage en minuscule pour la compatibilité des mails
		'ID_Demande' => $ID_Demande
	)) or die(print_r($MAJ_infos->errorInfo()));
};

if ($liste_hote[0] != "")  // S'il y a au moins un hôte
{
	// Calcul du nombre d'hôte à mettre à jour
	$NbHote = count($liste_hote);
	addlog("NbHote=".$NbHote);
	for ($i = 0;$i<$NbHote;$i++)
	{
		addlog("liste_hote=".($liste_hote[$i]));
		$liste_T_hote = explode("|",$liste_hote[$i]);
		addlog("Nom_hote=".($liste_T_hote[0]));
		addlog("INSERT Table Hote");
		$MAJ_hote = $bdd_supervision->prepare('INSERT INTO hote 
			(Nom_Hote, ID_Demande, Description, IP_Hote, Type_Hote, ID_Localisation, OS, Architecture, Langue, Fonction, Controle_Actif, Commentaire, Consigne, Detail_Consigne, Type_Action)
			VALUES (:Nom_Hote, :ID_Demande, :Description, :IP_Hote, :Type_Hote, :ID_Localisation, :OS, :Architecture, :Langue, :Fonction, :Controle_Actif, :Commentaire, :Consigne, :Detail_Consigne, :Type_Action)
			ON DUPLICATE KEY UPDATE Nom_Hote= :nom_hote2, ID_Demande= :id_demande2, Description= :description2, IP_Hote= :ip_hote2, Type_Hote= :type_hote2, ID_Localisation= :id_localisation2, OS= :os2, Architecture= :architecture2, Langue= :langue2, Fonction= :fonction2, Controle_Actif= :controle_actif2, Commentaire= :commentaire2, Consigne= :consigne2, Detail_Consigne= :detail_consigne2, Type_Action= :type_action2');
		$MAJ_hote->execute(array(
			'Nom_Hote' => htmlspecialchars($liste_T_hote[0]),
			'ID_Demande' => $ID_Demande,
			'Description' => htmlspecialchars($liste_T_hote[2]),
			'IP_Hote' => htmlspecialchars($liste_T_hote[1]),
			'Type_Hote' => htmlspecialchars($liste_T_hote[4]),
			'ID_Localisation' => htmlspecialchars($liste_T_hote[3]),
			'OS' => htmlspecialchars($liste_T_hote[5]), // récupérer la valeur texte pour affichage
			'Architecture' => htmlspecialchars($liste_T_hote[6]),
			'Langue' => htmlspecialchars($liste_T_hote[7]),
			'Fonction' => htmlspecialchars($liste_T_hote[8]),
			'Controle_Actif' => htmlspecialchars($liste_T_hote[12]),
			'Commentaire' => htmlspecialchars($liste_T_hote[9]),
			'Consigne' => htmlspecialchars($liste_T_hote[10]),
			'Detail_Consigne' => htmlspecialchars($liste_T_hote[11]),
			'Type_Action' => htmlspecialchars($liste_T_hote[13]),
			'nom_hote2' => htmlspecialchars($liste_T_hote[0]),
			'id_demande2' => $ID_Demande,
			'description2' => htmlspecialchars($liste_T_hote[2]),
			'ip_hote2' => htmlspecialchars($liste_T_hote[1]),
			'type_hote2' => htmlspecialchars($liste_T_hote[4]),
			'id_localisation2' => htmlspecialchars($liste_T_hote[3]),
			'os2' => htmlspecialchars($liste_T_hote[5]), // récupérer la valeur texte pour affichage
			'architecture2' => htmlspecialchars($liste_T_hote[6]),
			'langue2' => htmlspecialchars($liste_T_hote[7]),
			'fonction2' => htmlspecialchars($liste_T_hote[8]),
			'controle_actif2' => htmlspecialchars($liste_T_hote[12]),
			'commentaire2' => htmlspecialchars($liste_T_hote[9]),
			'consigne2' => htmlspecialchars($liste_T_hote[10]),
			'detail_consigne2' => htmlspecialchars($liste_T_hote[11]),
			'type_action2' => htmlspecialchars($liste_T_hote[13])
		)) or die(print_r($MAJ_hote->errorInfo()));
		addlog("INSERTION données: nom_hote=" . htmlspecialchars($liste_T_hote[0]) . ",id_demande=" . $ID_Demande . ",description=" . htmlspecialchars($liste_T_hote[2]) .",ip_hote=" . htmlspecialchars($liste_T_hote[1]) .",type_hote=" . htmlspecialchars($liste_T_hote[4]).",id_localisation=" . htmlspecialchars($liste_T_hote[3]).",os=" . htmlspecialchars($liste_T_hote[5]).",architecture=" . htmlspecialchars($liste_T_hote[6]).",langue=" . htmlspecialchars($liste_T_hote[7]).",fonction=" . htmlspecialchars($liste_T_hote[8]).",controle_actif=" . htmlspecialchars($liste_T_hote[12]).",commentaire=" . htmlspecialchars($liste_T_hote[9]).",consigne=" . htmlspecialchars($liste_T_hote[10]).",detail_consigne=" . htmlspecialchars($liste_T_hote[11]).",type_action=" . htmlspecialchars($liste_T_hote[13]) ."");
	};

	// epuration de la demande des hotes retirés
	$Lst_Hote_Base = $bdd_supervision->prepare('SELECT Nom_Hote,IP_Hote FROM hote WHERE Type_Action<> :type_action AND ID_Demande= :ID_Demande;');
	$Lst_Hote_Base->execute(array(
		//'type_action' => "Traite",
		'type_action' => "NC",
		'ID_Demande' => $ID_Demande
	)) or die(print_r($Lst_Hote_Base->errorInfo()));

	while ($Res_Hote_Base = $Lst_Hote_Base->fetch())
	{
		//addlog("boucle principale");
		addlog("Hote_en_Base=". $Res_Hote_Base['Nom_Hote']);
		$Trouve = "Non";
		for ($i = 0;$i<$NbHote;$i++)
		{
			$Lst_Hote_Dem = explode("|",$liste_hote[$i]);
			if ($Lst_Hote_Dem[0].$Lst_Hote_Dem[1] == $Res_Hote_Base['Nom_Hote'].$Res_Hote_Base['IP_Hote'])
			{
				$Trouve = "Oui";
				addlog("Hôte trouvé dans l'interface, on ne supprime pas.");
				break;
			};
		};

		if ($Trouve == "Non")
		{
			addlog("hote non trouvé dans l'interface, purge:".$Res_Hote_Base['Nom_Hote']);
					
			$DEL_Hote = $bdd_supervision->prepare('DELETE FROM hote 
				WHERE Nom_Hote= :Nom_Hote AND IP_Hote = :IP_Hote AND Type_Action <> :type_action AND ID_Demande= :ID_Demande;');
			$DEL_Hote->execute(array(
				'Nom_Hote' => $Res_Hote_Base['Nom_Hote'],
				'IP_Hote' => $Res_Hote_Base['IP_Hote'],
				'type_action' => "NC",
				'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Hote->errorInfo()));
		};
	};
};

if ($liste_service[0] != "")  // S'il y a au moins un service
{
	// Calcul du nombre de service à mettre à jour
	$NbService = count($liste_service);
	addlog("NbService=".$NbService);
	for ($i = 0;$i<$NbService;$i++)
	{
		addlog("liste_service".$i."=".($liste_service[$i]));
			// Nom_service							|ID_Hote	|Periode	|Controle	| ID_Hote_Centreon	| ID_Modele_service	| Frequence		| Consigne	|Description Consigne	| Action	|Commentaire	| Parametres
			//	[0]									|[1]		|[2]		|[3]		|[4]				|[5]				|[6]			|[7]		|[8]					|[9]		|[10]			|[11]
			// Traffic - vmxnet3 Ethernet Adapter #3|24796		|24/24 - 7/7|actif		|1752				|66					|1 min / 1 min	|			|gdfhfghfghh			|Modifier	|				|12!4294!80!90!NC
		$liste_T_service = explode("|",$liste_service[$i]);
		/**
		 * Traitement des arguments de type Macro
		 * Similaire au traitement effectué dans complete_table.php
		 */
//		echo "Valeur ID_Modele_Service=".$liste_T_service[5] . "\n";
		// vérifie le type d'arguments du modèle (argument ou Macro) via le flag MS_EST_MACRO
		//$req_type_modele = $bdd_supervision->prepare('SELECT MS.MS_EST_MACRO FROM service AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service WHERE S.ID_Modele_Service = :ID_Modele_Service AND ID_Demande = :ID_Demande');
		$req_type_modele = $bdd_supervision->prepare('SELECT MS_EST_MACRO FROM modele_service WHERE ID_Modele_Service = :ID_Modele_Service');
// 		$req_type_modele->execute(Array(
// 				'ID_Modele_Service' => $liste_T_service[5],
// 				'ID_Demande' => $ID_Demande
// 		)) or die(print_r($req_type_modele->errorinfo()));
		$req_type_modele->execute(Array(
				'ID_Modele_Service' => $liste_T_service[5]
		)) or die(print_r($req_type_modele->errorinfo()));
		
		while ($res_type_modele = $req_type_modele->fetch()) // ne doit retourner qu'un seul enregistrement
		{
			if ($res_type_modele[0] == 1) // Si MS_EST_MACRO = 1 les arguments sont de type MACRO
			{
				$EST_MACRO = True;
				addlog("EST_MACRO=".$EST_MACRO);
		
				// récupère les arguments de type Macro
				//1) récupère la liste exhaustive des macro liées à la commande à partir du modele de service
// 				$req_Select_Macro = $bdd_supervision->prepare('
// 					SELECT 
// 						MS.MS_Macro,
// 					 FROM service AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service
// 					 WHERE S.ID_Modele_Service = :ID_Modele_Service AND ID_Demande = :ID_Demande');
// 				$req_Select_Macro->execute(Array(
// 						'ID_Modele_Service' => $liste_T_service[5],
// 						'ID_Demande' => $ID_Demande
// 				)) or die(print_r($req_Select_Macro->errorinfo()));
				$req_Select_Macro = $bdd_supervision->prepare('
					SELECT MS_Macro FROM modele_service WHERE ID_Modele_Service = :ID_Modele_Service');
				$req_Select_Macro->execute(Array(
						'ID_Modele_Service' => $liste_T_service[5]
				)) or die(print_r($req_Select_Macro->errorinfo()));
				
// 				echo '<pre>';
// 				print_r($req_Select_Macro);
// 				echo '</pre>';
		
				//2) extrait chaque Macro de la chaine et stocke dans un nouveau tableau
				while ($res_Select_Macro = $req_Select_Macro->fetch())
				{
					$Liste_Macro = explode("!",$res_Select_Macro[0]); // stocke la valeur de chaque macro dans un nouveau tableau
				};
// 				echo '<pre>';
// 				print_r($Liste_Macro);
// 				echo '</pre>';
				/**
				 * 3) reconstruction de la chaine de paramètres
				 * basé sur la valeur $liste_T_service[11] correspondant aux paramètres de la sonde
				 * 	Parametres: 12!4294!80!90!NC
				 * 	MS_Macro: INTERFACEID!IFSPEED!WARNING!CRITICAL!64BITS
				 */
				$Liste_Valeur = explode("!",$liste_T_service[11]);
// 				echo '<pre>';
// 				print_r($Liste_Valeur);
// 				echo '</pre>';
				$j=0;
				$Val_Macro=Array();
				foreach ($Liste_Macro AS $Macro_Name) // on boucle sur les valeurs de Macro pour associer les arguments
				{
					/**
					 addlog("Liste_Macro=".$Liste_Macro[$j]);
					 addlog("Macro_Name=".$Macro_Name[0]);
					 addlog("Macro_value=".$Macro_Name[1]);
					 */
					addlog("Macro_Name=".$Macro_Name . "\n" . "Macro_value=".$Liste_Valeur[$j]);
// 					//if (($Liste_Macro[$j] == $Macro_Name) AND ($Macro_Valeur != "")) // Si Liste_Macro = Macro_Name et MAcro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 					//if (($Liste_Macro[$j] == $Macro_Name[0]) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et MAcro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 					if ((strcasecmp($Liste_Macro[$j], $Macro_Name[0]) == 0) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et Macro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 					// strcasecmp => comparaison insensible à la casse
// 					{
// 						//$Val_Macro[$Macro_Name] = $Macro_Valeur; // tableau nommé
// 						//									addlog("Val_Macro:".$Macro_Name[0]."=".$Macro_Name[1]);
// 						// 									$Val_Macro[$Macro_Name[0]] = substr($Macro_Name[0],8,-1) . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// 						//									$Val_Macro[$Macro_Name[0]] = substr($Macro_Name[0],8) . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// 						//									$Val_Macro[$Macro_Name[0]] = substr($Macro_Name[0]) . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
						$Val_Macro[$Macro_Name] = $Macro_Name . ":" . $Liste_Valeur[$j]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
						//											exemple	IFSPEED:1000
						addlog("valeur stockée=". $Val_Macro[$Macro_Name]);
//					};
					$j++;
				};
// 				//7) On récupère la consigne éventuelle pour être cohérent avec les arguments classique
// 				$req_Consigne = $bdd_centreon->prepare('SELECT Consigne_Sonde AS Consigne FROM vInventaireServices WHERE service_id = :ID_Service_Centreon');
// 				$req_Consigne->execute(Array(
// 						'ID_Service_Centreon' => $ID_Service_Centreon
// 				)) or die(print_r($req_Consigne->errorInfo()));
// 				while ($res_Consigne = $req_Consigne->fetch())
// 				{
// 					$Consigne = $res_Consigne[0];
// 				}
				/**
				 * reconstruction de la chaine d'arguments
				 */
				$Chaine_Val_Macro = "";
				foreach($Val_Macro as $Macro_Nom => $Macro_Val)
				{
					$Chaine_Val_Macro .= "!" . $Macro_Val;
				};
				/**
				 * re affectation dans le tableau initial $liste_T_service[11]
				 */
				$liste_T_service[11] = $Chaine_Val_Macro;
// 				echo 'Liste_T_service[11] = ' . $liste_T_service[11];
// 				echo 'Chaine_Val_Macro = ' . $Chaine_Val_Macro;
			};
		};
		
		
		/**
		 * Insertion des données de service
		 */
		addlog("requete insertion Service:");
		addlog("##### DEBUG SI NECESSAIRE pour réinsertion manuelle ##### UPDATE service SET ID_Hote= ".htmlspecialchars($liste_T_service[1]).", Nom_Periode= '".htmlspecialchars($liste_T_service[2])."', Frequence= '".htmlspecialchars($liste_T_service[6])."', Controle_Actif= '".htmlspecialchars($liste_T_service[3])."', ID_Modele_Service= ".htmlspecialchars($liste_T_service[5]).", Parametres= '".htmlspecialchars($liste_T_service[11])."', Consigne= '".htmlspecialchars($liste_T_service[7])."', Detail_Consigne= '".htmlspecialchars($liste_T_service[8])."', Type_Action= '".htmlspecialchars($liste_T_service[9])."', Commentaire= '".htmlspecialchars($liste_T_service[10])."', ID_Hote_Centreon= ".htmlspecialchars($liste_T_service[4])." WHERE Nom_Service= '".htmlspecialchars($liste_T_service[0])."' AND ID_Hote= ".htmlspecialchars($liste_T_service[1])." AND ID_Demande= ".$ID_Demande.";");
		/**
		 * procedure ON DUPLICATE KEY UPDATE
		 *
 		$MAJ_service = $bdd_supervision->prepare('INSERT INTO service 
			(Nom_Service, ID_Demande, ID_Hote, Nom_Periode, Frequence, Controle_Actif, ID_Modele_Service, Parametres, Consigne, Detail_Consigne, Type_Action, Commentaire, ID_Hote_Centreon)
			VALUES (:Nom_Service, :ID_Demande, :ID_Hote, :Nom_Periode, :Frequence, :Controle_Actif, :ID_Modele_Service, :Parametres, :Consigne, :Detail_Consigne, :Type_Action, :Commentaire, :id_hote_centreon)
			ON DUPLICATE KEY UPDATE Nom_Service= :nom_service2, ID_Demande= :id_demande2, ID_Hote= :id_hote2, Nom_Periode= :nom_periode2, Frequence= :frequence2, Controle_Actif= :controle_actif2, ID_Modele_Service= :id_modele_service2, Parametres= :parametres2, Consigne= :consigne2, Detail_Consigne= :detail_consigne2, Type_Action= :type_action2, Commentaire= :commentaire2, ID_Hote_Centreon= :id_hote_centreon2');
		$MAJ_service->execute(array(
			'Nom_Service' => htmlspecialchars($liste_T_service[0]),
			'ID_Demande' => $ID_Demande,
			'ID_Hote' => htmlspecialchars($liste_T_service[1]),
			'Nom_Periode' => htmlspecialchars($liste_T_service[2]),
			'Frequence' => htmlspecialchars($liste_T_service[6]),
			'Controle_Actif' => htmlspecialchars($liste_T_service[3]),
			'ID_Modele_Service' => htmlspecialchars($liste_T_service[5]),
			'Parametres' => htmlspecialchars($liste_T_service[11]),
			'Consigne' => htmlspecialchars($liste_T_service[7]),
			'Detail_Consigne' => htmlspecialchars($liste_T_service[8]),
			'Type_Action' => htmlspecialchars($liste_T_service[9]),
			'Commentaire' => htmlspecialchars($liste_T_service[10]),
			'id_hote_centreon' => htmlspecialchars($liste_T_service[4]),
			'nom_service2' => htmlspecialchars($liste_T_service[0]),
			'id_demande2' => $ID_Demande,
			'id_hote2' => htmlspecialchars($liste_T_service[1]),
			'nom_periode2' => htmlspecialchars($liste_T_service[2]),
			'frequence2' => htmlspecialchars($liste_T_service[6]),
			'controle_actif2' => htmlspecialchars($liste_T_service[3]),
			'id_modele_service2' => htmlspecialchars($liste_T_service[5]),
			'parametres2' => htmlspecialchars($liste_T_service[11]),
			'consigne2' => htmlspecialchars($liste_T_service[7]),
			'detail_consigne2' => htmlspecialchars($liste_T_service[8]),
			'type_action2' => htmlspecialchars($liste_T_service[9]),
			'commentaire2' => htmlspecialchars($liste_T_service[10]),
			'id_hote_centreon2' => htmlspecialchars($liste_T_service[4])
		)) or die(print_r($MAJ_service->errorInfo()));
*/
		/**
		 * Procedure Try UPDATE catch INSERT
		 * 
 		try {
			$MAJ_service = $bdd_supervision->prepare('UPDATE service
			SET Nom_Periode= :nom_periode2,
					 Frequence= :frequence2,
					 Controle_Actif= :controle_actif2,
					 ID_Modele_Service= :id_modele_service2,
					 Parametres= :parametres2,
					 Consigne= :consigne2,
					 Detail_Consigne= :detail_consigne2,
					 Type_Action= :type_action2,
					 Commentaire= :commentaire2
				 WHERE Nom_Service= :nom_service2 AND ID_Demande= :id_demande2 AND ID_Hote= :id_hote2;');
			$MAJ_service->execute(array(
					'nom_periode2' => htmlspecialchars($liste_T_service[2]),
					'frequence2' => htmlspecialchars($liste_T_service[6]),
					'controle_actif2' => htmlspecialchars($liste_T_service[3]),
					'id_modele_service2' => htmlspecialchars($liste_T_service[5]),
					'parametres2' => htmlspecialchars($liste_T_service[11]),
					'consigne2' => htmlspecialchars($liste_T_service[7]),
					'detail_consigne2' => htmlspecialchars($liste_T_service[8]),
					'type_action2' => htmlspecialchars($liste_T_service[9]),
					'commentaire2' => htmlspecialchars($liste_T_service[10]),
					'nom_service2' => htmlspecialchars($liste_T_service[0]),
					'id_demande2' => $ID_Demande,
					'id_hote2' => htmlspecialchars($liste_T_service[1])
			)) or die(print_r($MAJ_service->errorInfo()));
		} catch (Exception $e) {
			$MAJ_service = $bdd_supervision->prepare('INSERT INTO service 
				(Nom_Service, ID_Demande, ID_Hote, Nom_Periode, Frequence, Controle_Actif, ID_Modele_Service, Parametres, Consigne, Detail_Consigne, Type_Action, Commentaire, ID_Hote_Centreon)
				VALUES (:Nom_Service, :ID_Demande, :ID_Hote, :Nom_Periode, :Frequence, :Controle_Actif, :ID_Modele_Service, :Parametres, :Consigne, :Detail_Consigne, :Type_Action, :Commentaire, :id_hote_centreon)');
			$MAJ_service->execute(array(
				'Nom_Service' => htmlspecialchars($liste_T_service[0]),
				'ID_Demande' => $ID_Demande,
				'ID_Hote' => htmlspecialchars($liste_T_service[1]),
				'Nom_Periode' => htmlspecialchars($liste_T_service[2]),
				'Frequence' => htmlspecialchars($liste_T_service[6]),
				'Controle_Actif' => htmlspecialchars($liste_T_service[3]),
				'ID_Modele_Service' => htmlspecialchars($liste_T_service[5]),
				'Parametres' => htmlspecialchars($liste_T_service[11]),
				'Consigne' => htmlspecialchars($liste_T_service[7]),
				'Detail_Consigne' => htmlspecialchars($liste_T_service[8]),
				'Type_Action' => htmlspecialchars($liste_T_service[9]),
				'Commentaire' => htmlspecialchars($liste_T_service[10]),
				'id_hote_centreon' => htmlspecialchars($liste_T_service[4])
			)) or die(print_r($MAJ_service->errorInfo()));
		};
 */

		/**
		 * Procedure basique SELECT Compare si trouvé update sinon insert
		 */
		$Select_service = $bdd_supervision->prepare('SELECT Concat(Nom_Service,"-", ID_Demande,"-", Id_Hote) FROM service WHERE ID_Demande = :ID_Demande;');
		$Select_service->execute(array(
				'ID_Demande' => $ID_Demande
		)) or die(print_r($Select_service->errorInfo()));
		
		$UPDATE_service = False;
		$res_Select_Service = $Select_service->fetchAll();
		$MonService=$liste_T_service[0].'-'.$ID_Demande.'-'.$liste_T_service[1];
		foreach($res_Select_Service as $Service)
		{
			if (md5($Service[0]) == md5($MonService) )
			{ // Si la clé Nom_Service correspond
				addlog("MonService=".$MonService);
				addlog("Service=".$Service[0]);
				addlog("md5_MonService=".md5($MonService));
				addlog("md5_Service=".md5($Service[0]));
				$UPDATE_service = True;
			};
		};
		if ($UPDATE_service==True)
		{
			addlog("MAJ Table Service");
			addlog("MonService=".$MonService);
			addlog("Service=".$Service[0]);
			addlog("md5_MonService=".md5($MonService));
			addlog("md5_Service=".md5($Service[0]));					
			$MAJ_service = $bdd_supervision->prepare('UPDATE service
				SET Nom_Periode= :nom_periode2,
					 Frequence= :frequence2,
					 Controle_Actif= :controle_actif2,
					 ID_Modele_Service= :id_modele_service2,
					 Parametres= :parametres2,
					 Consigne= :consigne2,
					 Detail_Consigne= :detail_consigne2,
					 Type_Action= :type_action2,
					 Commentaire= :commentaire2
				 WHERE Nom_Service= :nom_service2 AND ID_Demande= :id_demande2 AND ID_Hote= :id_hote2;');
			$MAJ_service->execute(array(
					'nom_periode2' => htmlspecialchars($liste_T_service[2]),
					'frequence2' => htmlspecialchars($liste_T_service[6]),
					'controle_actif2' => htmlspecialchars($liste_T_service[3]),
					'id_modele_service2' => htmlspecialchars($liste_T_service[5]),
					'parametres2' => htmlspecialchars($liste_T_service[11]),
					'consigne2' => htmlspecialchars($liste_T_service[7]),
					'detail_consigne2' => htmlspecialchars($liste_T_service[8]),
					'type_action2' => htmlspecialchars($liste_T_service[9]),
					'commentaire2' => htmlspecialchars($liste_T_service[10]),
					'nom_service2' => htmlspecialchars($liste_T_service[0]),
					'id_demande2' => $ID_Demande,
					'id_hote2' => htmlspecialchars($liste_T_service[1])
			)) or die(print_r($MAJ_service->errorInfo()));
		} else
		{
			addlog("INSERT Table Service");
			addlog("MonService=".$MonService);
			addlog("Service=".$Service[0]);
			addlog("md5_MonService=".md5($MonService));
			addlog("md5_Service=".md5($Service[0]));					
			$MAJ_service = $bdd_supervision->prepare('INSERT INTO service 
				(Nom_Service, ID_Demande, ID_Hote, Nom_Periode, Frequence, Controle_Actif, ID_Modele_Service, Parametres, Consigne, Detail_Consigne, Type_Action, Commentaire, ID_Hote_Centreon)
				VALUES (:Nom_Service, :ID_Demande, :ID_Hote, :Nom_Periode, :Frequence, :Controle_Actif, :ID_Modele_Service, :Parametres, :Consigne, :Detail_Consigne, :Type_Action, :Commentaire, :id_hote_centreon)');
			$MAJ_service->execute(array(
				'Nom_Service' => htmlspecialchars($liste_T_service[0]),
				'ID_Demande' => $ID_Demande,
				'ID_Hote' => htmlspecialchars($liste_T_service[1]),
				'Nom_Periode' => htmlspecialchars($liste_T_service[2]),
				'Frequence' => htmlspecialchars($liste_T_service[6]),
				'Controle_Actif' => htmlspecialchars($liste_T_service[3]),
				'ID_Modele_Service' => htmlspecialchars($liste_T_service[5]),
				'Parametres' => htmlspecialchars($liste_T_service[11]),
				'Consigne' => htmlspecialchars($liste_T_service[7]),
				'Detail_Consigne' => htmlspecialchars($liste_T_service[8]),
				'Type_Action' => htmlspecialchars($liste_T_service[9]),
				'Commentaire' => htmlspecialchars($liste_T_service[10]),
				'id_hote_centreon' => htmlspecialchars($liste_T_service[4])
			)) or die(print_r($MAJ_service->errorInfo()));
		};
	};
};


// Mise à jour Nom_Hote suite à insertion
//	$UPD_Service = $bdd_supervision->prepare('UPDATE service AS S, hote AS H SET S.Nom_Hote=H.Nom_Hote WHERE S.ID_Hote=H.ID_Hote AND S.ID_Demande= :ID_Demande');
	$UPD_Service = $bdd_supervision->prepare('UPDATE service AS S, hote AS H SET S.Nom_Hote=H.Nom_Hote, S.ID_Hote_Centreon=H.ID_Hote_Centreon WHERE S.ID_Hote=H.ID_Hote AND S.ID_Demande= :ID_Demande');
	$UPD_Service->execute(array(
			'ID_Demande' => $ID_Demande
	)) or die(print_r($UPD_Service->errorInfo()));
	addlog("Mise a jour Nom hote");
	addlog("UPDATE service AS S, hote AS H SET S.Nom_Hote=H.Nom_Hote WHERE S.ID_Hote=H.ID_Hote AND S.ID_Demande= " . $ID_Demande . ";");

// Mise à jour Type_action=Creer suite à duplication ou ajout
	$UPD_Service = $bdd_supervision->prepare('UPDATE service SET Type_Action="Creer" WHERE selection IS NULL AND ID_Demande= :ID_Demande');
	$UPD_Service->execute(array(
			'ID_Demande' => $ID_Demande
	)) or die(print_r($UPD_Service->errorInfo()));
	
	addlog("Mise a jour Type_Action=Creer pour selection=NULL");
	addlog("UPDATE service SET Type_Action='Creer' WHERE selection IS NULL AND ID_Demande= " . $ID_Demande . ";");
	
/**
 * Désactivé le 23/12/14 puisque géré dans la suppr du fieldset => génère des suppressions à tord! => cas d'un ID_Hote non récupéré
 * Réactivé le 13/10/15 suite à changment méthode d'insertion en base ON DUPLICATE KEY UPDATE posant problème avec les services!!!
*/
// epuration de la demande des services retirés et des doublons
// vérifier si toujours utile puisque suppression gérée lors de la suppression du fieldset
//	$Lst_Service_Base = $bdd_supervision->prepare('SELECT Nom_Service, Nom_Hote FROM service WHERE ID_Demande= :ID_Demande;');
    $Lst_Service_Base = $bdd_supervision->prepare('SELECT Nom_Service, ID_Hote FROM service WHERE ID_Demande= :ID_Demande;');
	$Lst_Service_Base->execute(array(
		'ID_Demande' => $ID_Demande
	)) or die(print_r($Lst_Service_Base->errorInfo()));

	while ($Res_Service_Base = $Lst_Service_Base->fetch())
	{
		addlog("boucle principale");
		addlog("Service_Base=". $Res_Service_Base['Nom_Service']);
		$Trouve = False;
		for ($i = 0;$i<$NbService;$i++)
		{
			addlog("debug liste_service".$i."=". $liste_service[$i]);
			$Lst_Service_Dem = explode("|",$liste_service[$i]);

			addlog("Service_Dem=". $Lst_Service_Dem[0].$Lst_Service_Dem[1]);
//                        if ($Lst_Service_Dem[0].$Lst_Service_Dem[1] == $Res_Service_Base['Nom_Service'].$Res_Service_Base['Nom_Hote'])
			if ($Lst_Service_Dem[0].$Lst_Service_Dem[1] == $Res_Service_Base['Nom_Service'].$Res_Service_Base['ID_Hote'])
			{
				$Trouve = True;
				addlog("service trouvé, on ne purge pas.");
				break;
			};
		};
		if ($Trouve == False)
		{
			addlog("service non trouvé, purge=".$Res_Service_Base['Nom_Service']);
			$DEL_Service = $bdd_supervision->prepare('DELETE FROM service 
				WHERE Nom_Service= :Nom_Service AND ID_Hote = :ID_Hote AND ID_Demande= :ID_Demande;');
			$DEL_Service->execute(array(
				'Nom_Service' => $Res_Service_Base['Nom_Service'],
				'ID_Hote' => $Res_Service_Base['ID_Hote'],
				'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Service->errorInfo()));
		};
	};
	// déprécié le 28/11/14
	//include('gestion_doublon_service.php'); // A vérifier si toujours utile
//};


if ($liste_plage[0] != "")  // S'il y a au moins une plage
{
	// Calcul du nombre de plage à mettre à jour
	$NbPlage = count($liste_plage);
	addlog("NbPlage=".$NbPlage);

	for ($i = 0;$i<$NbPlage;$i++)
	{
		addlog("liste_plage=".($liste_plage[$i]));
		$liste_T_plage = explode("|",$liste_plage[$i]);
		addlog("Nom_plage=".($liste_T_plage[0]));
		$Select_plage = $bdd_supervision->prepare('SELECT ID_Periode_Temporelle, Nom_Periode FROM periode_temporelle WHERE Nom_Periode = :Nom_Periode AND ID_Demande = :ID_Demande;');
		$Select_plage->execute(array(
			'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
			'ID_Demande' => $ID_Demande
		)) or die(print_r($Select_plage->errorInfo()));
		
		$UPDATE_plage = False;
		$res_Select_Plage = $Select_plage->fetchAll();
		foreach($res_Select_Plage as $Plage)
		{
			if ($Plage[1] == $liste_T_plage[0]) // Si la plage correspond
			addlog("Plage=".$Plage[1]);
			$UPDATE_plage = True;
		};

		if ($UPDATE_plage==True)
		{
			addlog("MAJ Table Plage");
					
			$MAJ_plage = $bdd_supervision->prepare('UPDATE periode_temporelle 
				SET Lundi= :Lundi,
					Mardi= :Mardi,
					Mercredi= :Mercredi,
					Jeudi= :Jeudi,
					Vendredi= :Vendredi,
					Samedi= :Samedi,
					Dimanche= :Dimanche,
					Commentaire= :Commentaire,
					Type_Action= :Type_Action
				WHERE Nom_Periode= :Nom_Periode AND ID_Demande = :ID_Demande;');
			$MAJ_plage->execute(array(
				'Lundi' => htmlspecialchars($liste_T_plage[1]),
				'Mardi' => htmlspecialchars($liste_T_plage[2]),
				'Mercredi' => htmlspecialchars($liste_T_plage[3]),
				'Jeudi' => htmlspecialchars($liste_T_plage[4]),
				'Vendredi' => htmlspecialchars($liste_T_plage[5]),
				'Samedi' => htmlspecialchars($liste_T_plage[6]),
				'Dimanche' => htmlspecialchars($liste_T_plage[7]),
				'Commentaire' => htmlspecialchars($liste_T_plage[8]),
				'Type_Action' => htmlspecialchars($liste_T_plage[9]),
				'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
				'ID_Demande' => $ID_Demande
				)) or die(print_r($MAJ_plage->errorInfo()));
				
		} else
		{
			addlog("INSERT Table Plage");
					
			$MAJ_plage = $bdd_supervision->prepare('INSERT INTO periode_temporelle 
				(ID_Demande, Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche, Commentaire, Type_Action)
				VALUES (:ID_Demande, :Nom_Periode, :Lundi, :Mardi, :Mercredi, :Jeudi, :Vendredi, :Samedi, :Dimanche, :Commentaire, :Type_Action)');
			$MAJ_plage->execute(array(
				'ID_Demande' => $ID_Demande,
				'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
				'Lundi' => htmlspecialchars($liste_T_plage[1]),
				'Mardi' => htmlspecialchars($liste_T_plage[2]),
				'Mercredi' => htmlspecialchars($liste_T_plage[3]),
				'Jeudi' => htmlspecialchars($liste_T_plage[4]),
				'Vendredi' => htmlspecialchars($liste_T_plage[5]),
				'Samedi' => htmlspecialchars($liste_T_plage[6]),
				'Dimanche' => htmlspecialchars($liste_T_plage[7]),
				'Commentaire' => htmlspecialchars($liste_T_plage[8]),
				'Type_Action' => htmlspecialchars($liste_T_plage[9])
			)) or die(print_r($MAJ_plage->errorInfo()));
		};
	};

// epuration de la demande des plages retirés (passage au Type_Action "OK")
// vérifier si toujours utile puisque suppression gérée lors de la suppression du fieldset
	$Lst_Plage_Base = $bdd_supervision->prepare('SELECT Nom_Periode FROM periode_temporelle WHERE ID_Demande= :ID_Demande;');
	$Lst_Plage_Base->execute(array(
		'ID_Demande' => $ID_Demande
	)) or die(print_r($Lst_Plage_Base->errorInfo()));

	while ($Res_Plage_Base = $Lst_Plage_Base->fetch())
	{
		addlog("boucle principale");
		addlog("Plage_Base=". $Res_Plage_Base['Nom_Periode']);
		$Trouve = False;
		for ($i = 0;$i<$NbPlage;$i++)
		{
			addlog("debug liste_plage=". $liste_plage[$i]);
			$Lst_Plage_Dem = explode("|",$liste_plage[$i]);

			addlog("Plage_Dem=". $Lst_Plage_Dem[0]);
			if ($Lst_Plage_Dem[0] == $Res_Plage_Base['Nom_Periode'])
			{
				$Trouve = True;
				addlog("trouve");
				break;
			};
		};
		if ($Trouve == False)
		{
			addlog("purge periode=".$Res_Plage_Base['Nom_Periode']); // on purge pas vraiment pour conserver la dispo de la plage sur les autres services
			$UPD_Plage = $bdd_supervision->prepare('UPDATE periode_temporelle 
				SET Type_Action= :Type_Action
				WHERE Nom_Periode= :Nom_Periode AND ID_Demande= :ID_Demande;');
			$UPD_Plage->execute(array(
				'Type_Action' => "NC",
				'Nom_Periode' => $Res_Plage_Base['Nom_Periode'],
				'ID_Demande' => $ID_Demande
			)) or die(print_r($UPD_Plage->errorInfo()));
		};
	};
};
