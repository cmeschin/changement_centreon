<?php
//session_start();
if (session_id()=='')
{
session_start();
};

include_once('log.php');
if ($monclient)
{
	// récupérer la liste des hôtes sélectionnés dans la demande en cours avec l'ID demande et dont l'ID_Hote_Centreon n'est pas 0
	// 30/60/15 suppression de la restriction ID_Hote_Centreon<>0 puisque champ selection disponible
	include_once('connexion_sql_supervision.php');
	try {
// 		$req_hote_Dem = $bdd_supervision-> prepare('SELECT
// 				 ID_Hote,
// 				 ID_Demande,
// 				 ID_Hote_Centreon,
// 				 Nom_Hote,
// 				 Description,
// 				 IP_Hote,
// 				 Type_Hote,
// 				 ID_Localisation,
// 				 OS,
// 				 Architecture,
// 				 Langue,
// 				 Fonction,
// 				 Controle_Actif,
// 				 Commentaire,
// 				 Consigne,
// 				 Detail_Consigne,
// 				 Type_Action,
// 				 Etat_Parametrage,
// 				 selection
// 			FROM hote WHERE ID_Hote_Centreon <> 0 AND ID_Demande = :ID_Demande;');
		$req_hote_Dem = $bdd_supervision-> prepare('SELECT
				 ID_Hote,
				 ID_Demande,
				 ID_Hote_Centreon,
				 Nom_Hote,
				 Description,
				 IP_Hote,
				 Type_Hote,
				 ID_Localisation,
				 OS,
				 Architecture,
				 Langue,
				 Fonction,
				 Controle_Actif,
				 Commentaire,
				 Consigne,
				 Detail_Consigne,
				 Type_Action,
				 Etat_Parametrage,
				 selection
			FROM hote WHERE ID_Demande = :ID_Demande;');
		$req_hote_Dem->execute(array(
				'ID_Demande' => $_SESSION['ID_dem']
		)) or die(print_r($req_hote_Dem->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete liste hote reprise: ' . $e->getMessage());
	};
	
	$res_hote_Dem = $req_hote_Dem->fetchAll();
	
	/**
	 * Construction de la liste des id_hote
	 */
	$_SESSION['lst_id_hote'] = "";
	addlog("Liste id_hote" . $_SESSION['lst_id_hote']);
	foreach ( $res_hote_Dem as $val_hote )
	{
		$_SESSION['lst_id_hote'] .= "," .$val_hote['ID_Hote_Centreon'];
	};
	$_SESSION['lst_id_hote'] = substr($_SESSION['lst_id_hote'],1); // chaine construite sans le premier caractère.
	if ($_SESSION['lst_id_hote']=="")
	{
		$_SESSION['lst_id_hote']=0;
	};
	addlog("Liste id_hote" . $_SESSION['lst_id_hote']);

	echo '<p>Si un hôte n\'apparait pas dans la liste ci-dessous, c\'est qu\'il n\'est pas identifié pour la prestation actuelle.</p>';
	echo '<p>Utilisez la fonction de recherche ci-dessus pour vérifier son existence dans Centreon.</p>';
	echo '<p class="attention">Sélectionnez uniquement les hôtes dont vous souhaitez modifier les caractéristiques (Adresse IP, fonction, etc...) ou l\'état de la supervision (activation, désactivation, suppression).<br/>
			La désactivation ou la suppression d\'un hôte dans centreon implique la désactivation ou la suppression automatique des services qui lui sont associés.<br/>
			Inversement, l\'activation d\'un hôte réactivera automatiquement ses services à l\'exception des services désactivés unitairement. Il conviendra d\'être vigilant sur ce point.</p>';
	echo '<table id="T_Liste_Hote">';
	echo '<tr>';
	echo '<th>Sélection</th>';
	echo '<th>Localisation</th>';
	echo '<th>Type</th>';
	echo '<th>Hôte</th>';
	echo '<th>Description</th>';
	echo '<th>Adresse IP</th>';
	echo '<th>Controle</th>';
	echo '<th hidden="hidden">host_id</th>';
	echo '</tr>';
	$i = 1;
		foreach($res_hote_Dem as $lst_hote) // on boucle sur les hôtes de la demande => le While ne permet de faire qu'une seule fois la boucle
		{
			if (htmlspecialchars($lst_hote['Controle_Actif']) == "inactif")
			{
				echo '<tr class="inactif">';
			} else
			{
				echo '<tr>';
			};
//			if ((htmlspecialchars($lst_hote['Nom_Hote']).htmlspecialchars($lst_hote['IP_Hote']) == $nom_hote.$ip_hote) && (htmlspecialchars($lst_hote['selection']) == true))// si l'hote+IP fait parti de la demande en cours on le désactive et on le coche
			if (htmlspecialchars($lst_hote['selection']) == "true")// si l'hote fait parti de la demande en cours on le désactive
			{
//						echo '<td><input disabled="disabled" type="checkbox" name="selection_hote" id="' . $i . '"/></td>';
// on le le coche pas pour éviter un rajout en double
//						echo '<td><input checked="checked" disabled="disabled" type="checkbox" name="selection_hote" id="' . $i . '"/></td>';
				echo '<td><input disabled="disabled" type="checkbox" name="selection_hote" id="' . $i . '"/>OK</td>';
						//$ajout_OK = True;
//					} else if (htmlspecialchars($lst_hote['Nom_Hote']).htmlspecialchars($lst_hote['IP_Hote']) == $nom_hote.$ip_hote)//sinon on ajoute simplement la case 
			} else //sinon on ajoute simplement la case 
			{
				echo '<td><input type="checkbox" name="selection_hote" id="' . $i . '"/></td>';
			};
		//};
			echo '<td>' . htmlspecialchars($lst_hote['ID_Localisation']) . '</td>';
			echo '<td>' . htmlspecialchars($lst_hote['Type_Hote']) . '</td>';
			echo '<td>' . htmlspecialchars($lst_hote['Nom_Hote']) . '</td>';
			echo '<td>' . htmlspecialchars($lst_hote['Description']) . '</td>';
			echo '<td>' . htmlspecialchars($lst_hote['IP_Hote']) . '</td>';
			echo '<td>' . htmlspecialchars($lst_hote['Controle_Actif']) . '</td>';
			echo '<td hidden>h' . htmlspecialchars($lst_hote['ID_Hote_Centreon']) . '</td>';
			echo '</tr>';
			$i ++;
		};
	echo '</table>';
	echo '<p>Inutile de sélectionner un hôte pour lequel vous souhaitez modifier un service; sélectionnez simplement le service correspondant dans l\'onglet "Liste des services" ci-dessous.</p>';
} else 
{
    echo "ERREUR: Code_Client=[" . $monclient . "].";
};
