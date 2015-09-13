<?php
if (session_id()=='')
{
session_start();
};
set_time_limit(600); //fixe un délai maximum d'exécution de 600 secondes soit 10 minutes.
//session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement insertion_selection.php");

include_once('connexion_sql_supervision.php');
try {
	
	$bdd_supervision->beginTransaction();
	$sinfo_gen = (isset($_POST["info_gen"])) ? $_POST["info_gen"] : NULL;
	$info_gen = explode("$",$sinfo_gen); // découpe la chaine en tableau avec comme séparateur le $
	/* déprécié le 03-11-2014
	$shote_selec = (isset($_POST["hote_selec"])) ? $_POST["hote_selec"] : NULL;
	$hote_selec = explode("$",$shote_selec); // découpe la chaine en tableau avec comme séparateur le $
	*/
	$shote_liste = (isset($_POST["hote_liste"])) ? $_POST["hote_liste"] : NULL;
	$hote_liste = explode("$",$shote_liste); // découpe la chaine en tableau avec comme séparateur le $
	//addlog("liste_hotes=".$shote_selec);
	addlog("liste_hotesliste=".$shote_liste);
	
	
	$sservice_selec = (isset($_POST["service_selec"])) ? $_POST["service_selec"] : NULL;
	$service_selec = explode("$",$sservice_selec); // découpe la chaine en tableau avec comme séparateur le $
	
	addlog("liste_services=".$sservice_selec);
	
/* déprécié le 02/12/14
	$splage_selec = (isset($_POST["plage_selec"])) ? $_POST["plage_selec"] : NULL;
	$plage_selec = explode("$",$splage_selec); // découpe la chaine en tableau avec comme séparateur le $
*/
	$splage_liste = (isset($_POST["plage_liste"])) ? $_POST["plage_liste"] : NULL;
	$plage_liste = explode("$",$splage_liste); // découpe la chaine en tableau avec comme séparateur le $
//	addlog("liste_plages=".$splage_selec);
	addlog("liste_plagesliste=".$splage_liste);
	
	//print_r($info_gen);
	$nbligne_info=count($info_gen);
	if ($_SESSION['ID_dem']==0) // si ID_dem=0 on enregistre les info générales et on déclare la demande en etat "Brouillon".
	{
		if ($nbligne_info==8)
		{
	
	/*
	//Ordre des champs: ATTENTION EN CAS DE CHANGEMENT DES CHAMPS DU FORMULAIRE INFO GENERALE
		0 => demandeur
		1=> Date_demande
		2=> ref_demande
		3=> Etat
		4=> Prestation // => inversion le 12/09/15
		5=> Date_supervision // => inversion le 12/09/15
		6=> email
		7=> commentaire
	*/
			$insert_info_gen = $bdd_supervision->prepare('INSERT INTO demande (Code_Client, Demandeur, Date_Demande, Ref_Demande, Date_Supervision_Demandee, Commentaire, Etat_Demande, email) VALUES(:Code_Client, :Demandeur, :Date_Demande, :Ref_Demande, :Date_Supervision_Demandee, :Commentaire, :Etat_Demande, :email)');
			$insert_info_gen->execute(array(
				'Demandeur' => htmlspecialchars($info_gen[0]),
				'Date_Demande' => htmlspecialchars($info_gen[1]),
				'Ref_Demande' => htmlspecialchars($info_gen[2]),
				'Code_Client' => htmlspecialchars($info_gen[4]),
				'Date_Supervision_Demandee' => htmlspecialchars($info_gen[5]),
				'Commentaire' => htmlspecialchars($info_gen[7]),
				'Etat_Demande' => htmlspecialchars("Brouillon"),
				'email' => htmlspecialchars(strtolower($info_gen[6])) // on force la chaine en minuscule pour garantir la compatibilité des adresses mails.
				)) or die(print_r($insert_info_gen->errorInfo()));
			
			// récupération de l'ID_Demande nouvellement créé
			$req_ID_dem = $bdd_supervision->prepare('SELECT ID_Demande FROM demande WHERE Ref_Demande= :Ref_Demande');
			$req_ID_dem->execute(array(
				'Ref_Demande' => htmlspecialchars($info_gen[2])
				)) or die(print_r($req_ID_dem->errorinfo()));
			$res_ID_dem = $req_ID_dem->fetch();
			$_SESSION['ID_dem']=$res_ID_dem[0]; // affectation de l'ID_Demande 
			$_SESSION['Code_Client']= htmlspecialchars($info_gen[5]);
			addlog("Les données ont été correctement enregistrées pour la prestation [" . $_SESSION['Code_Client'] . "]. ID=" . $_SESSION['ID_dem'] . ".");
	//		addlog(print_r($info_gen));
			addlog("données générales OK");
			addlog("email:".htmlspecialchars(strtolower($info_gen[6])));
		}
		else
		{
			//echo "echec insertion info générale!<br />";
			addlog("ECHEC insertion données générales");
			addlog("Nblignes=". $nbligne_info);
			addlog(print_r($info_gen));
		};
	};
	
	
	// récupération de la ref demande
	$ID_Demande= $_SESSION['ID_dem'];
	////////////////////////////////
	// insertion enregistrement hôte
	////////////////////////////////
	
	/* déprécié le 03-11-2014
	////////////////////////////////
	// Insertion hôtes sélectionnés
	////////////////////////////////
	
	$nbligne_hote=count($hote_selec);
		echo "nbligne_hote=" . $nbligne_hote . "<br />";
		echo '<pre>';
		print_r($hote_selec);
		echo '</pre>';
	if ($nbligne_hote>0 AND $hote_selec[0] != NULL) // le tableau contient au moins une ligne non null
	{
		for ( $i=0;$i<$nbligne_hote;$i++){
		//	echo "ligne " . $i . ":" . $hote_selec[$i] . "<br />";
			$t_hote_selec = explode(",",$hote_selec[$i]);// redécoupage de chaque ligne 
			$nbchamp_hote=count($t_hote_selec);
	//		print_r($t_hote_selec)."<br />";
	
			if ($nbchamp_hote==5)
			{
				$insert_hote_selec = $bdd_supervision->prepare('INSERT INTO hote (ID_Demande, ID_Hote_Centreon, Nom_Hote, Description, IP_Hote, Controle_Actif) VALUES(:ID_Demande, :ID_Hote_Centreon, :Nom_Hote, :Description, :IP_Hote, :Controle_Actif)');
				$insert_hote_selec->execute(array(
					'ID_Demande' => htmlspecialchars($ID_Demande),
					'ID_Hote_Centreon' => htmlspecialchars($t_hote_selec[4]),
					'Nom_Hote' => htmlspecialchars($t_hote_selec[0]),
					'Description' => htmlspecialchars($t_hote_selec[1]),
					'IP_Hote' => htmlspecialchars($t_hote_selec[2]),
					'Controle_Actif' => htmlspecialchars($t_hote_selec[3])
				)) or die(print_r($insert_hote_selec->errorInfo()));
			}
			else
			{
				echo "echec insertion hôte [" . $t_hote_selec[0] . "]!" . "<br />";
				addlog("ECHEC insertion hôte");
			};
		};
		echo "Les hôtes ont été correctement enregistrées.<br />";
				addlog("insertion hôtes OK");
	} else
	{
		echo "Aucun hôte à insérer!";
				addlog("aucun hôte à insérer");
	};
	*/
	
	////////////////////////////////
	// Insertion liste hôtes
	////////////////////////////////
	
	// déprécié le 03-11-2014 on utilise désormais uniquement la table hote
	////Purge de la table Hote_Temp pour les hôtes dont la demande est traitée ou annulée
	//include('Delete_hote_temp.php');
	
	$nbligne_hote_liste=count($hote_liste);
		addlog('nbligne_hote_liste=' . $nbligne_hote_liste) ;
/*		echo '<pre>';
		print_r($hote_liste);
		echo '</pre>';
*/
	if ($nbligne_hote_liste>0 AND $hote_liste[0] != NULL) // le tableau contient au moins une ligne non null
	{
		for ( $i=0;$i<$nbligne_hote_liste;$i++){
			addlog('ligne ' . $i . ':' . $hote_liste[$i]);
			$t_hote_liste = explode(",",$hote_liste[$i]);// redécoupage de chaque ligne 
			$nbchamp_hote=count($t_hote_liste);
//			print_r($t_hote_liste)."<br />";
	
			if ($nbchamp_hote==6)
			{
				addLog("INSERTION hôte:".htmlspecialchars($t_hote_liste[0]));
	//			$insert_hote_liste = $bdd_supervision->prepare('INSERT IGNORE INTO hote_temp (ID_Hote_Centreon, Nom_Hote, IP_Hote, ID_Demande) VALUES(:ID_Hote_Centreon, :Nom_Hote, :IP_Hote, :ID_Demande)');
	//			$insert_hote_liste = $bdd_supervision->prepare('INSERT IGNORE INTO hote (ID_Hote_Centreon, Nom_Hote, IP_Hote, selection, ID_Demande) VALUES(:ID_Hote_Centreon, :Nom_Hote, :IP_Hote, :selection, :ID_Demande)');
				$insert_hote_liste = $bdd_supervision->prepare('INSERT INTO hote
							 (Nom_Hote,
							 Description,
							 IP_Hote,
							 Controle_Actif,
							 ID_Hote_Centreon,
							 selection,
							 ID_Demande)
						 VALUES(:nom_hote,
						 	:description,
							:ip_hote,
							 :controle_actif,
							 :id_hote_centreon,
							 :selection,
							 :id_demande)
						ON DUPLICATE KEY UPDATE
							 Nom_Hote= :nom_hote2,
							 Description= :description2,
							 IP_Hote= :ip_hote2,
							 Controle_Actif= :controle_actif2,
							 ID_Hote_Centreon= :id_hote_centreon2,
							 selection= :selection2,
							 ID_Demande= :id_demande2');
				$insert_hote_liste->execute(array(
					// 0 Hote, 1 description, 2 IP, 3 Actif, 4 ID_Hote_centreon, 5 selection
					'nom_hote' => htmlspecialchars($t_hote_liste[0]),
					'description' => htmlspecialchars($t_hote_liste[1]),
					'ip_hote' => htmlspecialchars($t_hote_liste[2]),
					'controle_actif' => htmlspecialchars($t_hote_liste[3]),
					'id_hote_centreon' => htmlspecialchars($t_hote_liste[4]),
					'selection' => htmlspecialchars($t_hote_liste[5]),
					'id_demande' => htmlspecialchars($ID_Demande),
					'nom_hote2' => htmlspecialchars($t_hote_liste[0]),
					'description2' => htmlspecialchars($t_hote_liste[1]),
					'ip_hote2' => htmlspecialchars($t_hote_liste[2]),
					'controle_actif2' => htmlspecialchars($t_hote_liste[3]),
					'id_hote_centreon2' => htmlspecialchars($t_hote_liste[4]),
					'selection2' => htmlspecialchars($t_hote_liste[5]), // ne doit pas être mis à jour si déjà existant => modif le 02/12/14 =>réactivé le 18/03/15
					'id_demande2' => htmlspecialchars($ID_Demande)
				)) or die(print_r($insert_hote_liste->errorInfo()));
				addlog("insertion hôte: [" . $t_hote_liste[0] . "] [" . $t_hote_liste[1] . "] [" . $t_hote_liste[2] . "] [" . $t_hote_liste[3] . "] [" . $t_hote_liste[4] . "] [" . $t_hote_liste[5] . "]");
			} else
			{
				addlog("ECHEC insertion liste hôte");
				http_response_code(500);
				die( "echec insertion liste hôte [" . $t_hote_liste[0] . "]!" . "<br />");
			};
		};
		//include('gestion_doublon_hote_temp.php'); // devenue inutile avec l'index et la syntaxe INSERT IGNORE
		//echo "La liste des hôtes a été correctement enregistrée.<br />";
		addlog("INSERTION_SELECTION: Insertion liste hôtes OK.");
	} else
	{
		//echo "Aucune liste d'hôte à insérer!";
		addlog("aucune liste d'hôte à insérer");
	};
	
	////////////////////////////////
	// insertion enregistrement service
	////////////////////////////////
	$nbligne_service=count($service_selec);
		addlog('nbligne_service=' . $nbligne_service);
/*		echo '<pre>';
		print_r($service_selec);
		echo '</pre>';
*/
	if ($nbligne_service>0 AND $service_selec[0] != NULL) // le tableau contient au moins une ligne non null
	{
		for ( $i=0;$i<$nbligne_service;$i++)
		{
			addlog('ligne ' . $i . ':' . $service_selec[$i]);
			$t_service_selec = explode(",",$service_selec[$i]);// redécoupage de chaque ligne 
			$nbchamp_service=count($t_service_selec);
	//		print_r($t_service_selec)."<br />";
	
			if ($nbchamp_service==7)
			{
	//			$insert_service_selec = $bdd_supervision->prepare('INSERT IGNORE INTO service (ID_Demande, Nom_Service, Frequence, Nom_Periode, Controle_Actif, ID_Service_Centreon, ID_Hote_Centreon) VALUES(:ID_Demande, :Nom_Service, :Frequence, :Nom_Periode, :Controle_Actif, :ID_Service_Centreon, :ID_Hote_Centreon)');
				$insert_service_selec = $bdd_supervision->prepare('INSERT INTO service
						 (ID_Demande,
						 Nom_Service,
						 Frequence,
						 Nom_Periode,
						 Controle_Actif,
						 ID_Service_Centreon,
						 ID_Hote_Centreon,
						 Type_Action,
						 selection)
					 VALUES(:ID_Demande,
						 :Nom_Service,
						 :Frequence,
						 :Nom_Periode,
						 :Controle_Actif,
						 :ID_Service_Centreon,
						 :ID_Hote_Centreon,
						 :type_action,
						 :selection)
					ON DUPLICATE KEY UPDATE
						 ID_Demande= :id_demande2,
						 Nom_Service= :nom_service2,
						 Frequence= :frequence2,
						 Nom_Periode= :nom_periode2,
						 Controle_Actif= :controle_actif2,
						 ID_Service_Centreon= :id_service_centreon2,
						 Type_Action= :type_action2,
						 ID_Hote_Centreon= :id_hote_centreon2');
				$insert_service_selec->execute(array(
					'ID_Demande' => htmlspecialchars($ID_Demande),
					'Nom_Service' => htmlspecialchars($t_service_selec[0]),
					'Frequence' => htmlspecialchars($t_service_selec[1]),
					'Nom_Periode' => htmlspecialchars($t_service_selec[2]),
					'Controle_Actif' => htmlspecialchars($t_service_selec[3]),
					'ID_Service_Centreon' => htmlspecialchars($t_service_selec[4]),
					'ID_Hote_Centreon' => htmlspecialchars($t_service_selec[5]),
					'type_action' => "Modifier",
					'selection' => htmlspecialchars($t_service_selec[6]),
					'id_demande2' => htmlspecialchars($ID_Demande),
					'nom_service2' => htmlspecialchars($t_service_selec[0]),
					'frequence2' => htmlspecialchars($t_service_selec[1]),
					'nom_periode2' => htmlspecialchars($t_service_selec[2]),
					'controle_actif2' => htmlspecialchars($t_service_selec[3]),
					'id_service_centreon2' => htmlspecialchars($t_service_selec[4]),
					'type_action2' => "Modifier",
					'id_hote_centreon2' => htmlspecialchars($t_service_selec[5])
				)) or die(print_r($insert_service_selec->errorInfo()));
			}
			else
			{
				addlog("ECHEC insertion services");
				http_response_code(500);
				die("echec insertion service [" . $t_service_selec[0] . "]!");
			};
		};
		//echo "Les services ont été correctement enregistrés.";
		addlog("insertion service OK");
	} else
	{
		//echo "Aucun service à insérer!";
		addlog("aucun service à insérer");
	};
	
	////////////////////////////////
	// Insertion liste plages
	////////////////////////////////
	
	//Purge de la table Hote_Temp pour les hôtes dont la demande est traitée ou annulée
	//include('Delete_hote_temp.php');
	
	$nbligne_plage=count($plage_liste);
		addlog('nbligne_plage=' . $nbligne_plage);
/*		echo '<pre>';
		print_r($plage_liste);
		echo '</pre>';
*/
	if ($nbligne_plage>0 AND $plage_liste[0] != NULL) // le tableau contient au moins une ligne non null
	{
	        for ( $i=0;$i<$nbligne_plage;$i++){
					addlog('ligne ' . $i . ':' . $plage_liste[$i]);
	                $t_plage_liste = explode(";",$plage_liste[$i]);// redécoupage de chaque ligne
	                $nbchamp_plage=count($t_plage_liste);
	//              print_r($t_plage_liste)."<br />";
					addlog("nbchamp_plage=".$nbchamp_plage);
	                if ($nbchamp_plage==9)
	                {
						addlog("insertion liste plage [" . $t_plage_liste[0] . "] en cours...");
//						$insert_plage_liste = $bdd_supervision->prepare('INSERT IGNORE INTO periode_temporelle (ID_Demande, Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche, Type_Action) VALUES(:ID_Demande, :Nom_Periode, :Lundi, :Mardi, :Mercredi, :Jeudi, :Vendredi, :Samedi, :Dimanche, :Type_Action)');
						$insert_plage_liste = $bdd_supervision->prepare('INSERT INTO periode_temporelle
									 (ID_Demande,
									 Nom_Periode,
									 Lundi,
									 Mardi,
									 Mercredi,
									 Jeudi,
									 Vendredi,
									 Samedi,
									 Dimanche,
									 selection)
								VALUES(:ID_Demande,
									 :Nom_Periode,
									 :Lundi,
									 :Mardi,
									 :Mercredi,
									 :Jeudi,
									 :Vendredi,
									 :Samedi,
									 :Dimanche,
									 :selection)
								ON DUPLICATE KEY UPDATE
									ID_Demande= :id_demande2,
									 Nom_Periode= :nom_periode2,
									 Lundi= :lundi2,
									 Mardi= :mardi2,
									 Mercredi= :mercredi2,
									 Jeudi= :jeudi2,
									 Vendredi= :vendredi2,
									 Samedi= :samedi2,
									 Dimanche= :dimanche2');
						$insert_plage_liste->execute(array(
	                                'ID_Demande' => htmlspecialchars($ID_Demande),
	                                'Nom_Periode' => htmlspecialchars($t_plage_liste[0]),
	                                'Lundi' => htmlspecialchars($t_plage_liste[1]),
	                                'Mardi' => htmlspecialchars($t_plage_liste[2]),
	                                'Mercredi' => htmlspecialchars($t_plage_liste[3]),
	                                'Jeudi' => htmlspecialchars($t_plage_liste[4]),
	                                'Vendredi' => htmlspecialchars($t_plage_liste[5]),
	                                'Samedi' => htmlspecialchars($t_plage_liste[6]),
	                                'Dimanche' => htmlspecialchars($t_plage_liste[7]),
									'selection' => htmlspecialchars($t_plage_liste[8]),
									'id_demande2' => htmlspecialchars($ID_Demande),
									'nom_periode2' => htmlspecialchars($t_plage_liste[0]),
									'lundi2' => htmlspecialchars($t_plage_liste[1]),
									'mardi2' => htmlspecialchars($t_plage_liste[2]),
									'mercredi2' => htmlspecialchars($t_plage_liste[3]),
									'jeudi2' => htmlspecialchars($t_plage_liste[4]),
									'vendredi2' => htmlspecialchars($t_plage_liste[5]),
									'samedi2' => htmlspecialchars($t_plage_liste[6]),
									'dimanche2' => htmlspecialchars($t_plage_liste[7])
	                        )) or die(print_r($insert_plage_liste->errorInfo()));
			}
	                else
	                {
	                        addlog("ECHEC insertion liste plage [" . $t_plage_liste[0] . "]!");
	                        http_response_code(500);
	                        die("echec insertion liste plage [" . $t_plage_liste[0] . "]!" . "<br />");
	                };
	        };
	        //include('gestion_doublon_periode_temporelle.php'); // devenue inutile avec l'index et la syntaxe INSERT IGNORE
	        //echo "La liste des plages a été correctement enregistrée.<br />";
	                        addlog("insertion liste plages OK");
	} else
	{
	        //echo "Aucune liste de plage à insérer!";
	                        addlog("aucune liste de plage à insérer");
	};
/*
 * Déprécié le 02/12/14
	////////////////////////////////
	// MAJ enregistrement plage
	////////////////////////////////
	$nbligne_plage=count($plage_selec);
	//echo "nbligne_plage=" . $nbligne_plage . "<br />";
	//	print_r($plage_selec) . "<br />";
	if ($nbligne_plage>0 AND $plage_selec[0] != NULL) // le tableau contient au moins une ligne non null
	{
		for ( $i=0;$i<$nbligne_plage;$i++)
		{
	//		echo "ligne " . $i . ":" . $plage_selec[$i] . "<br />";
			$t_plage_selec = explode(",",$plage_selec[$i]);// redécoupage de chaque ligne 
			$nbchamp_plage=count($t_plage_selec);
	//		print_r($t_plage_selec)."<br />";
	
			if ($nbchamp_plage==8)
			{
				$update_plage_selec = $bdd_supervision->prepare('UPDATE periode_temporelle SET Type_Action = "Modifier" WHERE ID_Demande = :ID_Demande AND Nom_Periode = :Nom_Periode');
				$update_plage_selec->execute(array(
					'ID_Demande' => htmlspecialchars($ID_Demande),
					'Nom_Periode' => htmlspecialchars($t_plage_selec[0]),
	//				'Lundi' => htmlspecialchars($t_plage_selec[1]),
	//				'Mardi' => htmlspecialchars($t_plage_selec[2]),
	//				'Mercredi' => htmlspecialchars($t_plage_selec[3]),
	//				'Jeudi' => htmlspecialchars($t_plage_selec[4]),
	//				'Vendredi' => htmlspecialchars($t_plage_selec[5]),
	//				'Samedi' => htmlspecialchars($t_plage_selec[6]),
	//				'Dimanche' => htmlspecialchars($t_plage_selec[7])
				)) or die(print_r($update_plage_selec->errorInfo()));
			}
			else
			{
				echo "echec MAJ plage [" . $t_plage_selec[0] . "]!";
				addlog("ECHEC MAJ plage");
			};
		};
		echo "Les plages ont été correctement mises à jour.";
				addlog("MAJ plage OK");
	} else
	{
		echo "Aucune plage à modifier!";
				addlog("aucune plage à modifier");
	};
*/
	
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur insertion Selection: '. $e->getMessage());
	
};
// Mise à jour des tables dans Supervision
addlog("appel page complete_table.php");
include_once('complete_table.php');
