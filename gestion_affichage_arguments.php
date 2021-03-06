<?php
if (session_id()=='')
{
session_start();
};

if (!$_SESSION['Extraction']) // si la variable n'est pas initialisée on l'initialise à false
{
	$_SESSION['Extraction'] = false;
}
if (($_SESSION['PDF'] == false) OR (!$_SESSION['PDF']))
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
	if ($_SESSION['PDF'] == false)
	{
		echo '<label for="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '">' . htmlspecialchars($T_Libelle[$i]) . ':</label>';
	};
// 	var_dump($_SESSION['Extraction']);
// 	var_dump($_SESSION['PDF']);
// 	var_dump($_SESSION['Reprise']);
// 	var_dump($_SESSION['Nouveau']);
	if ($T_Argument[$i] == "")
	{
		if (($_SESSION['Extraction'] == false) AND ($_SESSION['PDF'] == false) AND (($_SESSION['Reprise'] == true) OR ($_SESSION['Nouveau'] == true)))
		{
			echo '<input type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="" Placeholder="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '" onblur="verifChamp(this)" class="Service_Argument' . $NbFieldset_Service . '"/>';
// 		} else if (($_SESSION['PDF'] == false) AND ($_SESSION['Extraction'] == true) AND ($_SESSION['Reprise'] == false) AND ($_SESSION['Nouveau'] == false))
// 		{
// 			echo '<input Readonly="Readonly" type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="" Placeholder="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
		} else if (($_SESSION['PDF'] == true) AND ($_SESSION['Extraction'] == true))// c'est une extraction PDF
		{
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="text-decoration: underline">' . htmlspecialchars($T_Libelle[$i]) . ':</span>';
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars(trim($Valeur_Champ)) . '</span>';
			echo '<br />';
		} else
		{ 
 			echo '<input Readonly="Readonly" type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="" Placeholder="' . htmlspecialchars(trim($Valeur_Champ)) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
// 			echo '<p> Oups il y a un trou dans le code... :)</p>';
		};
	} else
	{
		if (($_SESSION['Extraction'] == false) AND ($_SESSION['PDF'] == false) AND (($_SESSION['Reprise'] == true) OR ($_SESSION['Nouveau'] == true))) 
		{ // Si ce n'est pas la page extraction ou une reprise ou une nouvelle demande
			echo '<input type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" placeholder="' . htmlspecialchars(trim($T_Argument_Mod[$i])) . '" size="'. $LongueurArg . '" onblur="verifChamp(this)" class="Service_Argument' . $NbFieldset_Service . '"/>';
// 		} else if (($_SESSION['PDF'] == false) AND ($_SESSION['Extraction'] == true) AND ($_SESSION['Reprise'] == false) AND ($_SESSION['Nouveau'] == false))
// 		{ // c'est une extraction simple ou un listage des demandes
// 			echo '<input Readonly="Readonly" type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" placeholder="' . htmlspecialchars(trim($T_Argument_Mod[$i])) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
		} else if (($_SESSION['PDF'] == true) AND ($_SESSION['Extraction'] == true))// c'est une extraction PDF
		{
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="text-decoration: underline">' . htmlspecialchars($T_Libelle[$i]) . ':</span>';
			echo '<span id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" class="Service_Argument' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars(trim($Valeur_Champ)) . '</span>';
			echo '<br />';
		} else
		{ 
			echo '<input Readonly="Readonly" type="text" id="Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '" name="Service_' . $NbFieldset_Service . '_Argument_' . $Num_Argument . '" value="' . htmlspecialchars(trim($Valeur_Champ)) . '" placeholder="' . htmlspecialchars(trim($T_Argument_Mod[$i])) . '" size="'. $LongueurArg . '" class="Service_Argument' . $NbFieldset_Service . '"/>';
//			echo '<p> Oups il y a un trou dans le code... :)</p>';
		};
	};
	if (($_SESSION['Extraction'] == false) AND ($_SESSION['PDF'] == false) AND (($_SESSION['Reprise'] == true) OR ($_SESSION['Nouveau'] == true))) // Si ce n'est pas la page extraction
	{
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Argument' . $NbFieldset_Service . '_' . $Num_Argument . '"/>';
	};
	if ($_SESSION['PDF'] == false) // Si ce n'est pas la page extraction PDF
	{
		echo '<br />';
	};
	$Num_Argument ++; // incrément Num Argument
};
