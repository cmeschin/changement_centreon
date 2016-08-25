<?php
if (session_id()=='')
{
	session_start();
};
include('log.php'); // chargement de la fonction de log
include_once('connexion_sql_centreon.php');
 
$sID_Hote = (isset($_POST["sID_Hote"])) ? $_POST["sID_Hote"] : NULL;
$sNbService = (isset($_POST["sNbService"])) ? $_POST["sNbService"] : NULL;

if ($sID_Hote && $sNbService) 
{
	try 
	{
		$req_hote = $bdd_centreon->prepare('SELECT
			 Distinct(Nom_Hote),
			 Hote_Description,
			 Hote
			 FROM vInventaireServices
			 WHERE host_id= :sID_Hote
			 ORDER BY Nom_Hote, Sonde');
		$req_hote->execute(array(
			'sID_Hote' => htmlspecialchars($sID_Hote)
		)) or die(print_r($req_hote->errorInfo()));
	} catch (Exception $e) 
	{
		die('Erreur requete_liste_recherche_service: ' . $e->getMessage());
	};
	$i = $sNbService;
	$liste_service = '';
	while ($res_hote = $req_hote->fetch())
	{ 
		$j=1;
		try 
		{
			$req_service = $bdd_centreon->prepare('SELECT
				 Distinct(Nom_Hote),
				 Hote_Description,
				 IP_Hote,
				 Controle,
				 Hote,
				 Sonde,
				 Frequence,
				 Plage_Horaire,
				 host_id,
				 service_id
				 FROM vInventaireServices
				 WHERE host_id= :sID_Hote
				 ORDER BY Nom_Hote, Sonde');
			$req_service->execute(array(
				'sID_Hote' => htmlspecialchars($sID_Hote)
			)) or die(print_r($req_service->errorInfo()));
		} catch (Exception $e) 
		{
			die('Erreur requete_liste_recherche_service: ' . $e->getMessage());
		};
		
		while ($res_service = $req_service->fetch())
		{ 
			$liste_service .= '<input readonly="" type="checkbox" name="selection_service" id="s' . $i . '"/>' . '!';
			if ($j  == 1 || $j % 10 == 0)
			{
				$liste_service .= htmlspecialchars($res_service['Hote']) . '!'; // on transmets le champ Hote qui contient le nom complet de l'hote et l'IP 
			}else
			{
				$liste_service .= '' . '!';
			};
			$liste_service .= htmlspecialchars($res_service['Sonde']) . '!';
			$liste_service .= htmlspecialchars($res_service['Frequence']) . '!';
			$liste_service .= htmlspecialchars($res_service['Plage_Horaire']) . '!';
			$liste_service .= htmlspecialchars($res_service['Controle']) . '!';
			$liste_service .= 's' . htmlspecialchars($res_service['service_id']) . '!';
			$liste_service .= 'h' . htmlspecialchars($res_service['host_id']) . '|';
			$i++; // incrément du compteur d'id
			$j++; // incrément du compteur d'hôte
		};
	};
	echo rtrim($liste_service,'|');
} else 
{
    echo "ERREUR: ID_Hote=[" . $sID_Hote . "]; NbService=[" . $sNbService . "].";
};
