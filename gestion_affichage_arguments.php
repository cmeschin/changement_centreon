<?php
if (session_id()=='')
{
session_start();
};
// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
//echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '">';

if (!$_SESSION['R_ID_Demande']) // si la variable n'est pas initialisée on l'initialise à NULL
{
	$_SESSION['R_ID_Demande']=NULL;
}
if (($_SESSION['PDF'] == "Non") OR (!$_SESSION['PDF']))
{
	echo '<legend>Arguments du service</legend>';	
	echo '<label for="Service_Argument_Description' . $NbFieldset_Service . '">Description :</label>';
	echo '<textarea Disabled="Disabled" id="Service_Argument_Description' . $NbFieldset_Service . '" name="Service_Argument_Description' . $NbFieldset_Service . '" rows="2" cols="50">' . htmlspecialchars($Description) . '</textarea> <br />';
} else
{
	echo '<h5 id="Lbl_Service_Argument' . $NbFieldset_Service . '" style="text-decoration: underline;text-align:center">Arguments du service</h5>';
	echo '<span id="Lbl_Service_Argument_Description' . $NbFieldset_Service . '" style="text-decoration: underline">Description:</span>';
	echo '<span id="Service_Argument_Description' . $NbFieldset_Service . '" style="font-style: italic"> ' . htmlspecialchars($Description) . '</span> <br />';
	echo '<br />';
	
};

$Num_Argument = 1; // initialise la valeur pour le premier argument affiché
for ( $i=0;$i<$nbLibelle;$i++)
{
	if (!isset($T_Argument[$i])) // Si la variable n'est pas déclarée (dernier argument vide dans le paramétrage centreon par ex, on la force à blanc
	{
		$T_Argument[$i] = "NC";
	}
	if ($T_Argument[$i] == "")
	{
		$Valeur_Champ = $T_Argument_Mod[$i];
	} else
	{
		$Valeur_Champ =  $T_Argument[$i];
	};
	include('gestion_caracteres_speciaux.php');
	
	$LongueurArg=  strlen(htmlspecialchars($Valeur_Champ)) + 5;
//	echo '<div id="Service_Argument_Actif' . $NbFieldset_Service . '>';
	if ($_SESSION['PDF'] == "Non")
	{
		echo '<label for="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '">' . htmlspecialchars($T_Libelle[$i]) . ':</label>';
	};
	if ($T_Argument[$i] == "")
	{
		if (($_SESSION['R_ID_Demande'] == NULL) AND ($_SESSION['PDF'] == "Non"))
		{
			if ($_SESSION['Reprise'] == true)
			{
				echo '<input type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="" Placeholder="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '" onblur="verifChamp(this)" class="Service_Argument' . $NbFieldset_Service . '"/>';
			} else 
			{
				echo '<input type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="" Placeholder="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
			};
		} else if ($_SESSION['PDF'] == "Non")
		{
				echo '<input Readonly="Readonly" type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="" Placeholder="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
		} else
		{
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="text-decoration: underline">' . htmlspecialchars($T_Libelle[$i]) . ':</span>';
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars(trim($Valeur_Champ)) . '</span>';
			echo '<br />';
		};
	} else
	{
		if (($_SESSION['R_ID_Demande'] == NULL) AND ($_SESSION['PDF'] == "Non"))
		{
			if ($_SESSION['Reprise'] == true)
			{
				echo '<input type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" placeholder="' . htmlspecialchars(trim($T_Argument_Mod[$i])) . '" size="'. $LongueurArg . '" onblur="verifChamp(this)" class="Service_Argument' . $NbFieldset_Service . '"/>';
			} else 
			{
				echo '<input type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" placeholder="' . htmlspecialchars(trim($T_Argument_Mod[$i])) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
			};
		} else if ($_SESSION['PDF'] == "Non")
		{
			echo '<input Readonly="Readonly" type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" placeholder="' . htmlspecialchars(trim($T_Argument_Mod[$i])) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
		} else
		{
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="text-decoration: underline">' . htmlspecialchars($T_Libelle[$i]) . ':</span>';
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars(trim($Valeur_Champ)) . '</span>';
			echo '<br />';
		};
	};
	if (($_SESSION['R_ID_Demande'] == NULL) AND ($_SESSION['PDF'] == "Non"))
	{
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '"/>';
	};
	if ($_SESSION['PDF'] == "Non")
	{
		echo '<br />';
	};
	//	echo '</div>';
	$Num_Argument ++; // incrément Num Argument
};
//echo '</fieldset> ';
/*
echo '<fieldset style="visibility:hidden" id="Inactif_Arg_Service_Modele' . $NbFieldset_Service . '">';
echo '<legend>Arguments du service initial</legend>';
echo '<label for="Inactif_Arg_Service_Argument_Description' . $NbFieldset_Service . '">Description :</label>';
echo '<textarea Disabled="Disabled" id="Inactif_Arg_Service_Argument_Description' . $NbFieldset_Service . '" name="Inactif_Arg_Service_Argument_Description' . $NbFieldset_Service . '" rows="3" cols="50">' . htmlspecialchars($Description) . '</textarea> <br />';
for ( $i=0;$i<$nbLibelle;$i++)
{
	$Valeur_Champ =  $T_Argument[$i];
	include('gestion_caracteres_speciaux.php');

	$LongueurArg=  strlen(htmlspecialchars($T_Argument[$i])) + 10;
	echo '<label for="Inactif_Arg_Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '">' . htmlspecialchars($T_Libelle[$i]) . ':</label>';
	echo '<input type="text" id="Inactif_Arg_Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '"/>';
//	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '"/> <br />';
	echo ' <br />';
	$Num_Argument ++; // incrément Num Argument
};
echo '</fieldset> ';
*/