<?php
if (session_id()=='')
{
session_start();
};
// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];

include_once('connexion_sql_supervision.php');
include_once('connexion_sql_centreon.php');


// Selection de tous les hôtes de la demande dont type_action <> Traite
try {
	include_once('requete_liste_hote_demande.php');
} catch (Exception $e) {
	die('Erreur requete_liste_hote_demande: ' . $e->getMessage());
};

$NbFieldset = 1;
while ($res_liste_hote = $req_liste_hote->fetch())
{ 
	echo '<fieldset id="Hote' . $NbFieldset . '" class="hote">';
	echo '<legend>Hôte n°' . $NbFieldset . '</legend>';
	echo '';
		echo '<div id="model_param_hote">';
			echo '<!-- Hote -->';
			echo '<label for="Nom_Hote' . $NbFieldset . '" class="hote_Nom_IP" onclick="alert(\'Saisir le nom de l\\\'hôte tel que définit dans les propriétés systèmes.\')">Nom de l\'hôte <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
			echo '<input Readonly type="text" id="Nom_Hote' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Nom" value="' . htmlspecialchars($res_liste_hote['Nom_Hote']) . '" size="20" length="20" class="hote' . $NbFieldset . '"/>';
			echo '<img src="images/img_ver.png" class="verif" alt="correct" id="img_Nom_Hote' . $NbFieldset . '" /> <br />';
			echo '';
			echo '<!-- Adresse IP -->';
			echo '<label for="IP_Hote' . $NbFieldset . '" class="hote_Nom_IP">Adresse IP :</label>';
			echo '<input Disabled="Disabled" type="text" id="IP_Hote' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_IP" onblur="verifChampIP(this)" value="' . htmlspecialchars($res_liste_hote['IP_Hote']) . '" class="hote' . $NbFieldset . '"/>';
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_IP_Hote' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> ';
		echo '</div>';
		echo '';
		echo '<!-- Description -->';
		echo '<div id="model_param_hote">';
			echo '<label for="Hote_Description' . $NbFieldset . '" onclick="alert(\'Saisir ici une description succinte de l\\\'hôte.\')">Description <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br/>';
			echo '<textarea id="Hote_Description' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Description" rows="2" cols="40" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">' . htmlspecialchars($res_liste_hote['Description']) . '</textarea>';
			if (htmlspecialchars($res_liste_hote['Description']) != "")
			{
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Hote_Description' . $NbFieldset . '" />';
			}else 
			{
				echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Hote_Description' . $NbFieldset . '" />';
			};
		echo '</div> <br />';
		echo '';
		echo '<!-- Localisation -->';
		echo '<label for="Localisation' . $NbFieldset . '" onclick="alert(\'Indiquez la localisation géographique de l\\\'hôte, si elle n\\\'apparait pas dans la liste, sélectionnez <Autre> et indiquez le nouveau site.\')">Localisation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		try {
			include('requete_liste_Hote_Site.php');
		} catch (Exception $e) {
			http_response_code(500);
			die('Erreur requete_liste_hote_site' . $e->getMessage());
		};
		if ($res_liste_hote['ID_Localisation'] == "")
		{
			echo '<select name="Hote_' . $NbFieldset . '_Localisation" id="Localisation' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">  <!-- Liste Localisation -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
				
			$Trouve_Loc = true; // on force à true pour le champ masqué
			echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
			while ($res_Loc = $req_Localisation->fetch())
			{
				echo '<option value="' . htmlspecialchars($res_Loc['ID_Localisation']) . '">' . htmlspecialchars($res_Loc['Lieux']) . ' [' . htmlspecialchars($res_Loc['ID_Localisation']) . ']</option>';
			};
			echo '</select>';
//			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Localisation' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Localisation' . $NbFieldset . '"/>';
		} else
		{
			echo '<select Disabled="Disabled" name="Hote_' . $NbFieldset . '_Localisation" id="Localisation' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">  <!-- Liste Localisation -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			$champ = $req_Localisation->fetchAll();
			$Trouve_Loc = false;
			foreach($champ as $res_Loc)
			{
				if ($res_liste_hote['ID_Localisation'] == $res_Loc['ID_Localisation'])
				{
					$Trouve_Loc = true;
				};
			};
			if ($Trouve_Loc == true)
			{
				echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
				foreach($champ as $res_Loc)
				{
					if ($res_liste_hote['ID_Localisation'] == $res_Loc['ID_Localisation']) 
					{
						//echo '<option Selected="Selected" value="' . htmlspecialchars($res_Loc['ID_Localisation']) . '">' . htmlspecialchars($res_Loc['Lieux']) . ' [' . htmlspecialchars($res_Loc['ID_Localisation']) . ']</option>';
						include('option_localisation_selected.php');
					} else
					{
						//echo '<option value="' . htmlspecialchars($res_Loc['ID_Localisation']) . '">' . htmlspecialchars($res_Loc['Lieux']) . ' [' . htmlspecialchars($res_Loc['ID_Localisation']) . ']</option>';
						include('option_localisation.php');
					};
				};
			} else
			{
				echo '<option Selected="Selected" value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
				foreach($champ as $res_Loc)
				{
					//echo '<option value="' . htmlspecialchars($res_Loc['ID_Localisation']) . '">' . htmlspecialchars($res_Loc['Lieux']) . ' [' . htmlspecialchars($res_Loc['ID_Localisation']) . ']</option>';
					include('option_localisation.php');
				};
			};
			echo '</select>';
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Localisation' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		};
		if ($Trouve_Loc == false) // si autre initialisé on l'affiche
		{
			echo '<span id="Localisation' . $NbFieldset . '_new">';
				echo '<input onblur="verifChamp(this)" type="text" name="Hote_' . $NbFieldset . '_Localisation_new" id="Localisation' . $NbFieldset . '_new" value="' . htmlspecialchars($res_liste_hote['ID_Localisation']) . '" placeholder="saisir le nouveau site..." size="20" class="hote' . $NbFieldset . '" title="Saisir le nouveau site... Le nom final pourra être modifié afin de correspondre à la règle de nommage."/>';
				if (htmlspecialchars($res_liste_hote['ID_Localisation']) != "")
				{
					echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Localisation' . $NbFieldset . '_new"/>';
				} else 
				{
					echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Localisation' . $NbFieldset . '_new"/>';
				}
			echo '</span> <br />';
		} else // autre non initialisé on charge par défaut à vide et on masque
		{
			echo '<span id="Localisation' . $NbFieldset . '_new" style="visibility: hidden;">';
				echo '<input onblur="verifChamp(this)" type="text" name="Hote_' . $NbFieldset . '_Localisation_new" id="Localisation' . $NbFieldset . '_new" value="Vide" placeholder="saisir le nouveau site..." size="20" class="hote' . $NbFieldset . '" title="Saisir le nouveau site... Le nom final pourra être modifié afin de correspondre à la règle de nommage.."/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Localisation' . $NbFieldset . '_new"/>';
			echo '</span> <br />';
		};
		echo '';
		echo '<!-- Type Hote -->';
		echo '<label for="Type_Hote' . $NbFieldset . '" onclick="alert(\'Sélectionnez le type d\\\'hôte dans la liste.\\nCette information fait partie de la règle de nommage des hôtes dans Centreon.\\nVous trouverez plus d\\\'explication dans la documentation en ligne.\')">Type <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		try {
			include('requete_liste_Hote_Type.php');
		} catch (Exception $e)
		{
			http_response_code(500);
			die('Erreur requete_liste_hote_type' . $e->getMessage());
		};
/**
 * Désactivation possibilité création nouveau type d'hôte
			echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
			include('requete_liste_Hote_Type.php'); 
			while ($res_type = $req_type->fetch())
			{ 
				if ($res_liste_hote[3] == $res_type['Type_Hote']){
					echo '<option Selected="Selected" value="' . htmlspecialchars($res_type['Type_Hote']) . '">' . htmlspecialchars($res_type['Type_Hote']) . ' / ' . htmlspecialchars($res_type['Type_Description']) . '</option>';
				} else {
					echo '<option value="' . htmlspecialchars($res_type['Type_Hote']) . '">' . htmlspecialchars($res_type['Type_Hote']) . ' / ' . htmlspecialchars($res_type['Type_Description']) . '</option>';
				};
			};
*/
		if ($res_liste_hote['Type_Hote'] == "")
		{
			echo '<select name="Hote_' . $NbFieldset . '_Type" id="Type_Hote' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">  <!-- Liste Type_Hote -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';

			$Trouve_Type = true; // on force à true pour le champ masqué
			echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
			while ($res_type = $req_type->fetch())
			{ 
				include('option_type.php');
			};
			echo '</select>';
//			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_Hote' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_Hote' . $NbFieldset . '"/>';
		} else
		{
			echo '<select Disabled="Disabled" name="Hote_' . $NbFieldset . '_Type" id="Type_Hote' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">  <!-- Liste Type_Hote -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';

			$champ = $req_type->fetchAll();
			$Trouve_Type = false;
			foreach($champ as $res_type)
			{
				if ($res_liste_hote['Type_Hote'] == $res_type['Type_Hote'])
				{
					$Trouve_Type = true;
				};
			};
			if ($Trouve_Type == true)
			{
				echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
				foreach($champ as $res_type)
				{
					if ($res_liste_hote['Type_Hote'] == $res_type['Type_Hote'])
					{
						include('option_type_selected.php');
					} else
					{
						include('option_type.php');
					};
				};
			} else
			{
				echo '<option Selected="Selected" value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
				foreach($champ as $res_type)
				{
					include('option_type.php');
				};
			};
			echo '</select>';
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_Hote' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		};
		if ($Trouve_Type == false) // si autre initialisé on l'affiche
		{
			echo '<span id="Type_Hote' . $NbFieldset . '_new">';
			echo '<input onblur="verifChamp(this)" type="text" name="Hote_' . $NbFieldset . '_Type_new" id="Type_Hote' . $NbFieldset . '_new" value="' . htmlspecialchars($res_liste_hote['Type_Hote']) . '" placeholder="saisir le nouveau type d\'hôte..." size="30" class="hote' . $NbFieldset . '" title="Saisissez le nouveau type d\'hôte... Le nom final pourra être modifié afin de correspondre à la règle de nommage."/>';
			if (htmlspecialchars($res_liste_hote['Type_Hote']) != "")
			{
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_Hote' . $NbFieldset . '_new"/>';
			} else 
			{
				echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_Hote' . $NbFieldset . '_new"/>';
			}
			echo '</span> <br />';
		} else
		{
			echo '<span id="Type_Hote' . $NbFieldset . '_new" style="visibility: hidden;">';
				echo '<input onblur="verifChamp(this)" type="text" name="Hote_' . $NbFieldset . '_Type_new" id="Type_Hote' . $NbFieldset . '_new" value="Vide" placeholder="saisir le nouveau type d\'hôte..." size="30" class="hote' . $NbFieldset . '" title="Saisissez le nouveau type d\'hôte... Le nom final pourra être modifié afin de correspondre à la règle de nommage."/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_Hote' . $NbFieldset . '_new"/>';
			echo '</span> <br />';
		};
		echo '';
		echo '<!-- OS -->';
		echo '<label for="Type_OS' . $NbFieldset . '" onclick="alert(\'Sélectionnez le système d\\\'exploitation installé sur l\\\'hôte.\\nPour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez <Non concerné>.\')">Système d\'exploitation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		try {
			include('requete_liste_Hote_OS.php');				
		} catch (Exception $e) {
			http_response_code(500);
			die('Erreur requete_liste_hote_OS'. $e->getMessage());
		};
			 
		if ($res_liste_hote['OS'] == "")
		{
			echo '<select name="Hote_' . $NbFieldset . '_OS" id="Type_OS' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">  <!-- Liste Type_OS -->';			$Trouve_OS = true; // on force à true pour le champ masqué
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			echo '<option value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
			echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
			while ($res_OS = $req_OS->fetch())
			{ 
				echo '<option value="' . htmlspecialchars($res_OS['Type_OS']) . '">' . htmlspecialchars($res_OS['Type_OS_Desc']) . '</option>';
			};
			echo '</select>';
//			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_OS' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_OS' . $NbFieldset . '"/>';
		} else
		{
			echo '<select Disabled="Disabled" name="Hote_' . $NbFieldset . '_OS" id="Type_OS' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">  <!-- Liste Type_OS -->';			$Trouve_OS = true; // on force à true pour le champ masqué
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			echo '<option value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
			$res_OS = $req_OS->fetchAll();
			$Trouve_OS = false;
			foreach($res_OS as $champ)
			{
				if ($res_liste_hote['OS'] == $champ['Type_OS'])
				{
					$Trouve_OS = true;
				};
			};
			if ($Trouve_OS == true)
			{
				echo '<option value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
				foreach($res_OS as $champ)
				{
					if ($res_liste_hote['OS'] == $champ['Type_OS'])
					{
						echo '<option Selected="Selected" value="' . htmlspecialchars($champ['Type_OS']) . '">' . htmlspecialchars($champ['Type_OS_Desc']) . '</option>';
					} else
					{
						echo '<option value="' . htmlspecialchars($champ['Type_OS']) . '">' . htmlspecialchars($champ['Type_OS_Desc']) . '</option>';
					};
				};
			} else
			{
				echo '<option Selected="Selected" value="Autre">Autre</option> <!-- Valeur à sélectionner pour en créer un -->';
				foreach($res_OS as $champ)
				{
					echo '<option value="' . htmlspecialchars($champ['Type_OS']) . '">' . htmlspecialchars($champ['Type_OS_Desc']) . '</option>';
				};
			};
			echo '</select>';
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_OS' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		};
		if ($Trouve_OS == false) // si autre initialisé on l'affiche
		{
			echo '<span id="Type_OS' . $NbFieldset . '_new">';
				echo '<input onblur="verifChamp(this)" type="text" name="Hote_' . $NbFieldset . '_OS_new" id="Type_OS' . $NbFieldset . '_new" value="' . htmlspecialchars($res_liste_hote['OS']) . '" placeholder="saisir le nouveau type d\'OS..." size="30" class="hote' . $NbFieldset . '" title="Indiquez le système d\'exploitation installé sur l\'hôte."/>';
				if (htmlspecialchars($res_liste_hote['OS']) != "")
				{
					echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_OS' . $NbFieldset . '_new"/>';
				} else 
				{
					echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_OS' . $NbFieldset . '_new"/>';
				}
			echo '</span> <br />';
		} else
		{
			echo '<span id="Type_OS' . $NbFieldset . '_new" style="visibility: hidden;">';
				echo '<input onblur="verifChamp(this)" type="text" name="Hote_' . $NbFieldset . '_OS_new" id="Type_OS' . $NbFieldset . '_new" value="Vide" placeholder="saisir le nouveau type d\'OS..." size="30" class="hote' . $NbFieldset . '" title="Indiquez le système d\'exploitation installé sur l\'hôte."/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_OS' . $NbFieldset . '_new"/>';
			echo '</span> <br />';
		};
		echo '';
		echo '<!-- Architecture -->';
		echo '<label for="Architecture' . $NbFieldset . '" onclick="alert(\'Indiquez s\\\'il s\\\'agit d\\\'un système 32 ou 64 bits. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez <Non concerné>.\')">Architecture <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		if ($res_liste_hote['Architecture'] == "")
		{
			echo '<select name="Hote_' . $NbFieldset . '_Architecture" id="Architecture' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '"> <!-- Liste Architecture -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			echo '<option value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
			echo '<option value="32_bits">32_bits</option> ';
			echo '<option value="64_bits">64_bits</option> ';
			echo '</select>';
//			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Architecture' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Architecture' . $NbFieldset . '"/>';
		} else
		{
			echo '<select Disabled="Disabled" name="Hote_' . $NbFieldset . '_Architecture" id="Architecture' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '"> <!-- Liste Architecture -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			if ($res_liste_hote['Architecture'] == "NC")
			{
				echo '<option Selected="Selected" value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
				echo '<option value="32_bits">32_bits</option> ';
				echo '<option value="64_bits">64_bits</option> ';
			} elseif ($res_liste_hote['Architecture'] == "32_bits")
			{
				echo '<option value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
				echo '<option Selected="Selected" value="32_bits">32_bits</option> ';
				echo '<option value="64_bits">64_bits</option> ';
			} elseif ($res_liste_hote['Architecture'] == "64_bits")
			{
				echo '<option value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
				echo '<option value="32_bits">32_bits</option> ';
				echo '<option Selected="Selected" value="64_bits">64_bits</option> ';
			};
			echo '</select>';
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Architecture' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		};
		echo '';
		echo '<!-- Langue -->';
		echo '<label for="Langue' . $NbFieldset . '" onclick="alert(\'Sélectionnez la langue du système d\\\'exploitation installé. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez <Non concerné>.\')">Langue <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		if ($res_liste_hote['Langue'] == "")
		{
			echo '<select name="Hote_' . $NbFieldset . '_Langue" id="Langue' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '"> <!-- Liste Langue -->';
			echo '<option Selected="Selected" value="" >...</option> <!-- Valeur par défaut -->';
			echo '<option value="NC" >Non Concerné</option> <!-- Valeur si Non Concerné -->';
			echo '<option value="Francais">Francais</option> ';
			echo '<option value="Anglais">Anglais</option> ';
			echo '</select>';
//			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Langue' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Langue' . $NbFieldset . '"/>';
		} else
		{
			echo '<select Disabled="Disabled" name="Hote_' . $NbFieldset . '_Langue" id="Langue' . $NbFieldset . '" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '"> <!-- Liste Langue -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			if ($res_liste_hote['Langue'] == "NC")
			{
				echo '<option Selected="Selected" value="NC">Non Concerné</option> ';
				echo '<option value="Francais">Francais</option> ';
				echo '<option value="Anglais">Anglais</option> ';
			} elseif ($res_liste_hote['Langue'] == "Francais")
			{
				echo '<option value="NC">Non Concerné</option> ';
				echo '<option Selected="Selected" value="Francais">Francais</option> ';
				echo '<option value="Anglais">Anglais</option> ';
			} elseif ($res_liste_hote['Langue'] == "Anglais")
			{
				echo '<option value="NC">Non Concerné</option> ';
				echo '<option value="Francais">Francais</option> ';
				echo '<option Selected="Selected" value="Anglais">Anglais</option> ';
			};
			echo '</select>';
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Langue' . $NbFieldset . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		};
		echo '';
		echo '<!-- Fonction -->';
		echo '<label for="Fonction' . $NbFieldset . '" onclick="alert(\'Indiquez la ou les fonctions principales de l\\\'hôte. Cette information permettra de catégoriser l\\\'équipement. Les fonctions principales sont les suivantes: BosManager, BosDocument, Marcel, Tri, Videocodage, ICR, CFT, IBML, Fax, etc...\')">Fonction(s) <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Fonction' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Fonction" value="' . htmlspecialchars($res_liste_hote['Fonction']) . '" size="30" maxlength="50" class="hote' . $NbFieldset . '"/> </br>';
		echo '';
		echo '<!-- Consigne -->';
/**
 * Modification consigne obligatoire
 */
//		echo '<label for="Consigne_Hote' . $NbFieldset . '" onclick="alert(\'Indiquez ici le lien vers une consigne du wiki. Les consignes ont pour but de fournir les indications quant aux actions à réaliser par les équipes EPI et/ou CDS si un évènement se produit sur l\\\'équipement (relance d\\\'un process, envoi de mail, etc...)\')">Lien vers consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
//		echo '<input style="visibility: hidden;" type="text" id="Consigne_Hote' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Lien_Consigne" value="' . htmlspecialchars($res_liste_hote['Consigne']) . '" size="90" maxlength="255" class="hote' . $NbFieldset . '"/> <br />';
		echo '<span id="Consigne_Hote' . $NbFieldset . '" class="hote' . $NbFieldset . '">Lien vers la consigne :<a href="' . htmlspecialchars($res_liste_hote['Consigne']) . '" target="_blank">' . htmlspecialchars($res_liste_hote['Consigne']) . '</a></span> <br />';
		echo '';
		echo '<!-- Detail consigne -->';
		echo '<label for="Consigne_Hote_Detail' . $NbFieldset . '" onclick="alert(\'Décrivez ici les opérations à effectuer par les équipes EPI et/ou CDS si un évènement se produit sur l\\\'équipement (relancer un process, envoyer un mail, etc...).\\nLes consignes doivent être claires et précises afin qu\\\'elles puissent être appliquées rapidement et sans ambiguïté par les équipes de support.\\nLes adresses mails doivent être indiquées en toute lettre soit par ex: envoyer un mail à support_bmd@tessi.fr et pas simplement envoyer un mail support bmd.\\nCette consigne sera ensuite retranscrite dans le wiki tessi-techno et un lien sera rattaché à l\\\'hôte; le lien apparaitra par la suite dans le champ ci-dessus.\')">Description consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<textarea id="Consigne_Hote_Detail' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Description_Consigne" onblur="verifChamp(this)" rows="3" cols="50" class="hote' . $NbFieldset . '">' . htmlspecialchars($res_liste_hote['Detail_Consigne']) . '</textarea>';
		if ($res_liste_hote['Consigne'] == "")
		{
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Consigne_Hote_Detail' . $NbFieldset . '" <br />';
		} else 
		{
			echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Consigne_Hote_Detail' . $NbFieldset . '" ondblclick="deverouille_liste(this)" <br />';
		}
		echo '';
		echo '<!-- Controle_actif -->';
		echo '<label for="Controle_Actif_Hote' . $NbFieldset . '">Controle :</label>';
		echo '<input Disabled="Disabled" type="text" id="Controle_Actif_Hote' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Controle" readonly value="' . htmlspecialchars($res_liste_hote['Controle_Actif']) . '" size="5" class="hote' . $NbFieldset . '"/> <br />';
		echo '';
		echo '<!-- Action à effectuer -->';
		echo '<fieldset id="Action_Hote' . $NbFieldset . '" class="hote_action">';
		echo '<legend onclick="alert(\'Sélectionnez l\\\'action à réaliser sur l\\\'équipement; selon les cas plusieurs choix sont disponibles: Créer, Modifier, Activer, Désactiver, Supprimer.\\nVous trouverez plus d\\\'information sur l\\\'aide en ligne.\')">Actions à effectuer <img alt="point_interrogation" src="images/point-interrogation-16.png"></legend>';
		echo '<select name="Hote_' . $NbFieldset . '_Action" id="Hote_action' . $NbFieldset . '" class="hote' . $NbFieldset . '" onChange="change_statut(this)">';
// 			if (htmlspecialchars($res_liste_hote['Type_Action']) == "Modifier")
// 			{
// 				echo '<option Selected="Selected" value="Modifier">A Modifier</option>';
// 			} else
// 			{
// 				echo '<option value="Modifier">A Modifier</option>';
// 			};
			
// 			if (htmlspecialchars($res_liste_hote['Controle_Actif']) == "actif")
// 			{
// 				if (htmlspecialchars($res_liste_hote['Type_Action']) == "Desactiver")
// 				{	
// 					echo '<option Selected="Selected" value="Desactiver">A Désactiver</option>';
// 				} else
// 				{
// 					echo '<option value="Desactiver">A Désactiver</option>';
// 				};
// 			} else
// 			{
// 				if (htmlspecialchars($res_liste_hote['Type_Action']) == "Activer")
// 				{	
// 					echo '<option Selected="Selected" value="Activer">A Activer</option>';
// 				} else
// 				{
// 					echo '<option value="Activer">A Activer</option>';
// 				};
// 			};
// 			if (htmlspecialchars($res_liste_hote['Type_Action']) == "Supprimer")
// 			{	
// 				echo '<option Selected="Selected" value="Supprimer">A Supprimer</option>';
// 			} else
// 			{
// 				echo '<option value="Supprimer">A Supprimer</option>';
// 			};
			if (htmlspecialchars($res_liste_hote['Controle_Actif']) == "actif"){
				if (htmlspecialchars($res_liste_hote['Type_Action']) == "Creer"){
					echo '<option Selected="Selected" value="Creer">A Créer</option>';
				} else if (htmlspecialchars($res_liste_hote['Type_Action']) == "Modifier"){
					echo '<option Selected="Selected" value="Modifier">A Modifier</option>';
					echo '<option value="Desactiver">A Désactiver</option>';
					echo '<option value="Supprimer">A Supprimer</option>';
				} else if (htmlspecialchars($res_liste_hote['Type_Action']) == "Desactiver"){
					echo '<option value="Modifier">A Modifier</option>';
					echo '<option Selected="Selected" value="Desactiver">A Désactiver</option>';
					echo '<option value="Supprimer">A Supprimer</option>';
				} else if  (htmlspecialchars($res_liste_hote['Type_Action']) == "Supprimer"){
					echo '<option value="Modifier">A Modifier</option>';
					echo '<option value="Desactiver">A Désactiver</option>';
					echo '<option Selected="Selected" value="Supprimer">A Supprimer</option>';
				};
			} else
			{
				if (htmlspecialchars($res_liste_hote['Type_Action']) == "Modifier"){
					echo '<option Selected="Selected" value="Modifier">A Modifier (et activer)</option>';
					echo '<option value="Activer">A Activer</option>';
					echo '<option value="Supprimer">A Supprimer</option>';
				} else if (htmlspecialchars($res_liste_hote['Type_Action']) == "Activer"){
					echo '<option value="Modifier">A Modifier (et activer)</option>';
					echo '<option Selected="Selected" value="Activer">A Activer</option>';
					echo '<option value="Supprimer">A Supprimer</option>';
				} else if  (htmlspecialchars($res_liste_hote['Type_Action']) == "Supprimer"){
					echo '<option value="Modifier">A Modifier (et activer)</option>';
					echo '<option value="Activer">A Activer</option>';
					echo '<option Selected="Selected" value="Supprimer">A Supprimer</option>';
				};
			};
		echo '</select> <br />';
		echo '<!-- Commentaire -->';
		echo '<label for="Hote_Commentaire' . $NbFieldset . '" onclick="alert(\'Indiquez ici tout complément d\\\'information pouvant être utile à la mise en surveillance.\')">Commentaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<textarea id="Hote_Commentaire' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Commentaire" rows="2" cols="50" class="hote' . $NbFieldset . '">' . htmlspecialchars($res_liste_hote['Commentaire']) . '</textarea> <br />';
		echo '';
		echo '</fieldset>';
	echo '';
	echo '<span id="bouton_Hote' . $NbFieldset . '">';
		echo '<button id="Valider_Hote' . $NbFieldset . '" onclick="valider_fieldset_hote(this)" hidden>Valider</button>'; // => doit ajouter automatiquement les services par défaut lié au modèle hote
		echo '<button id="Cloner_Hote' . $NbFieldset . '" onclick="clone_fieldset_hote(this)">Dupliquer</button>';
		echo '<button id="Effacer_Hote' . $NbFieldset . '" onclick="efface_fieldset_hote(this)" hidden>Effacer</button>';
		echo '<button id="Supprimer_Hote' . $NbFieldset . '" onclick="supprime_fieldset_hote(this)">Retirer de la demande</button>';  // => ??? doit supprimer automatiquement les services liés à l'hote
// désactivé le 29/10/14		echo '<button id="PreEnregistrer_Hote' . $NbFieldset . '" onclick="PreEnregistrer_fieldset_hote(this)">Pré-Enregistrer cet hôte</button>';  // => permet d'enregistrer le nom de l'hôte dans la table Hote_Temp pour l'avoir dispo dans les service
	echo '</span>';
	echo '';
	if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Hote.php');
	};
		echo '';
	echo '</fieldset>';
	$NbFieldset++;
};
$Statut_Hote=true;
