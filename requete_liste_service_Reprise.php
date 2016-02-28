<?php
if (session_id()=='')
{
session_start();
};
//include('log.php');

if ($monclient ) 
{
	// récupérer la liste des services sélectionnés dans la demande en cours avec l'ID demande
	include('connexion_sql_supervision.php');
	try {
/*		$req_service_Dem = $bdd_supervision-> prepare('SELECT
			 S.Nom_Service as Nom_Service,
			 H.Nom_Hote as Nom_Hote,
			 H.IP_Hote as IP_Hote,
			 S.selection as selection
		FROM service as S INNER JOIN hote as H ON S.ID_Demande=H.ID_Demande WHERE S.selection= "true" AND S.ID_Demande = :ID_Demande;'); */
		$req_service_Dem = $bdd_supervision-> prepare('SELECT
			 S.Nom_Service as Nom_Service,
			 S.Nom_Hote as Nom_Hote,
			 H.IP_Hote as IP_Hote,
			 S.selection
		FROM service as S INNER JOIN hote as H ON S.ID_Demande=H.ID_Demande WHERE S.selection= "true" AND S.ID_Demande = :ID_Demande;');
		
		$req_service_Dem->execute(array(
				'ID_Demande' => $_SESSION['ID_dem']
		)) or die(print_r($req_service_Dem->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete liste service reprise: ' . $e->getMessage());
	};
	
	$res_service_Dem = $req_service_Dem->fetchAll();

	// récupérer la liste des services de centreon et générer le tableau
	include_once('connexion_sql_centreon.php');
try {
/*		$req_service = $bdd_centreon->prepare('SELECT
			 Distinct(host_name),
			 host_address,
			 Controle,
			 Sonde,
			 Frequence,
			 Plage_Horaire,
			 H.host_id,
			 service_id
		FROM vInventaireServices AS vIS
			INNER JOIN host AS H ON vIS.host_id=H.host_id
		WHERE Code_Client= :prestation
		ORDER BY H.host_name,Sonde');
*/
//////////////////////////////////////////////
// 11/01/15 Clause WHERE à reporter dans la requête ci-dessous lorsque la modif pour les presta INFRA sera active
// 		WHERE (Code_Client= :prestation OR Code_Client LIKE "%INFRA%") AND host_id IN (' . htmlspecialchars($_SESSION['lst_id_hote']) . ')
//	Clause Where avant modif:
//		WHERE Code_Client= :prestation
//////////////////////////////////////////////
	addlog('SELECT
			 Distinct(Nom_Hote),
			 IP_Hote,
			 Controle,
			 Sonde,
			 Frequence,
			 Plage_Horaire,
			 host_id,
			 service_id
		FROM vInventaireServices
		WHERE (Code_Client= "' . htmlspecialchars($monclient) . '" OR Code_Client LIKE "%INFRA%") AND host_id IN (' . htmlspecialchars($_SESSION['lst_id_hote']) . ')
		ORDER BY Nom_Hote,Sonde');
	
		$req_service = $bdd_centreon->prepare('SELECT
			 Distinct(Nom_Hote),
			 IP_Hote,
			 Controle,
			 Sonde,
			 Frequence,
			 Plage_Horaire,
			 host_id,
			 service_id
		FROM vInventaireServices
		WHERE (Code_Client= :prestation OR Code_Client LIKE "%INFRA%") AND host_id IN (' . htmlspecialchars($_SESSION['lst_id_hote']) . ')
		ORDER BY Nom_Hote,Sonde');
	$req_service->execute(array(
			'prestation' => htmlspecialchars($monclient)
	)) or die(print_r($req_service->errorInfo()));
} catch (Exception $e) {
	addlog( 'Erreur: '	 . $e->getMessage());
	die('Erreur requete liste service centreon: ' . $e->getMessage());
};
	$nom_hote = "";
	$ajout_OK = False;
	//echo '<!--<div> -->';
	echo '<table id="T_Liste_Service">';
		echo '<tr>';
		echo '<th>Selection</th>';
		echo '<th>Hôte</th>';
		echo '<th>Service</th>';
		echo '<th>Fréquence</th>';
		echo '<th>Plage Horaire</th>';
		echo '<th>Controle</th>';
		echo '<th hidden="hidden">service_id</th>';
		echo '<th hidden="hidden">host_id</th>';
		echo '</tr>';
		$i = 1;

		while ($res_service = $req_service->fetch())
		{
			$nom_hote_actuel = substr(stristr(substr(stristr($res_service['Nom_Hote'],'-'),1),'-'),1);
			if ($nom_hote != $nom_hote_actuel)  
			{
				/**
			 	* permet de savoir si on a changé d'hôte pour l'afficher dans le tableau
			 	*/
				$j = 1;
				$nom_hote = $nom_hote_actuel; // enlève la localisation et la fonction et les deux -
				$hote_localisation = stristr($res_service['Nom_Hote'],'-',1); // conserve la chaine avant le premier tiret
				// décomposé ça donne ça:
				// $nom_hote= stristr($res_hote['host_name'],'-'); // enlève la localisation
				// $nom_hote= substr($nom_hote,1); // enlève le premier -
				// $nom_hote= stristr($nom_hote,'-'); // enlève la fonction
				// $nom_hote= substr($nom_hote,1); // enlève le deuxième tiret restant
			};
			if ($res_service['Controle'] == "inactif") // mise en couleur pour les controles inactifs
			{
				echo '<tr class="inactif">';
			} else
			{
				echo '<tr>';
			}; 
			if (count($res_service_Dem) >0)
			{
				/**
				 * si au moins un service a été sélectionné
				 */
			//while ($res_service_Dem = $req_service_Dem->fetch()) // on boucle sur les services de la demande
				$ajout_OK = False;
				foreach($res_service_Dem as $lst_service) 
				{
					/**
					 * on boucle sur les hôtes de la demande => le While ne permet de faire qu'une seule fois la boucle
					 */
					//if ($ajout_OK == False)
					//{
						if (($lst_service['IP_Hote'].$lst_service['Nom_Hote'].$lst_service['Nom_Service'] == $res_service['IP_Hote'].$nom_hote.$res_service['Sonde']) && ($lst_service['selection'] == "true") && $ajout_OK == False) // si le couple nom_hote+nom_service fait parti de la demande en cours on le désactive
						//if ($lst_service['selection'] == "true")
						{
							
							echo '<td><input Disabled="Disabled" type="checkbox" name="selection_service" id="s' . $i . '"/>OK</td>';
							$ajout_OK = True;
						//} else if (htmlspecialchars($lst_service['IP_Hote'].$lst_service['Nom_Hote'].$lst_service['Nom_Service']) == htmlspecialchars($res_service['host_address'].$nom_hote.$res_service['Sonde'])
						//{
						//	echo '<td><input type="checkbox" name="selection_service" id="s' . $i . '"/></td>';
						//} else 
						//{
						//	echo '<td><input type="checkbox" name="selection_service" id="s' . $i . '"/></td>';
							//$ajout_OK = True;
						};
					//};
					//$ajout_OK = True;
				};

				if ($ajout_OK == False)
				{
					echo '<td><input type="checkbox" name="selection_service" id="s' . $i . '"/></td>';
					//$ajout_OK = True;
				} else
				{
					$ajout_OK = False;
				};

			} else
			{
				echo '<td><input type="checkbox" name="selection_service" id="s' . $i . '"/></td>';
			};
			if ($j  == 1 || $j % 10 == 0){
				echo '<td title="' . $res_service['IP_Hote'] . ' - ' . $hote_localisation . '">' . $nom_hote . '</td>';
			}else
			{
				echo '<td></td>';
			};
			echo '<td>' . $res_service['Sonde'] . '</td>';
			echo '<td>' . $res_service['Frequence'] . '</td>';
			echo '<td>' . $res_service['Plage_Horaire'] . '</td>';
			//if ($res_service['Controle'] == "inactif") // mise en couleur pour les controles inactifs
			//{
			//	echo '<td>inactif</td>';				
			//} else
			//{
			//	echo '<td>actif</td>';
			//};
			echo '<td>' . htmlspecialchars($res_service['Controle']) . '</td>';
			echo '<td hidden>s' . $res_service['service_id'] . '</td>';
			echo '<td hidden>h' . $res_service['host_id'] . '</td>';
			echo '</tr>';
			$i++;
			$j++;
		};
	echo '</table>';
	//echo '<!--</div> -->';
} else 
{
    echo "ERREUR: Code_Client=[" . $monclient . "].";
};
