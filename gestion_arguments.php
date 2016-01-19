<?php
if (session_id()=='')
{
session_start();
};
	
// découpage des libelles et Arguments
	$T_Libelle = explode('!',$res_liste_service['MS_Libelles']);
	$T_Argument_Mod = explode('!',$res_liste_service['MS_Arguments']);
	
	// Suppression des \ dans la liste des arguments
	$res_liste_service['Parametres'] = str_replace("\\","",$res_liste_service['Parametres']); // supprime les \ ajoutés automatiquement par centreon

	if ((strstr($res_liste_service['Parametres'], ' ')) && (!strstr($res_liste_service['Parametres'], '!'))) // si la chaine contient des espaces et pas de !
	{
		//alert("découpage des espaces");
		// on supprime les doubles espaces
		$New_Liste_Arguments = str_replace("  ", " ",$res_liste_service['Parametres']);
		
		//$T_Argument = explode(' ',$res_liste_service['Parametres']); // création du tableau principal avec comme séparateur un espace; permet de gérer les arguments non détaillés dans les modèles de service
		$T_Argument = explode(' ',$New_Liste_Arguments); // création du tableau principal avec comme séparateur un espace; permet de gérer les arguments non détaillés dans les modèles de service
	} else
	{
		//alert("découpage des !");
		$T_Argument = explode('!',$res_liste_service['Parametres']); // création du tableau principal avec comme séparateur un !, tout autre caractère de séparation n'est pas pris en compte
	};

	// Gestion des différents modèles
	if (($T_Argument[0] == "CPULOAD") && ($res_liste_service['MS_EST_MACRO'] != true)) // Sinon si le modèle est un CPU
	{
			$T_CPU=explode(',',$T_Argument[1]); // découpage de l'argument avec les virgules
			// reconstruction du tableau T_Argument
			$T_Argument = array();
			$T_Argument[0]= $T_CPU[1] . "," . $T_CPU[4] . "," . $T_CPU[7];
			$T_Argument[1]= $T_CPU[2] . "," . $T_CPU[5] . "," . $T_CPU[8];
	} else if (($T_Argument[0] == "MEMUSE") && ($res_liste_service['MS_EST_MACRO'] != true)) // Sinon si le modèle est une mémoire
	{
	// gestion du modèle MEMOIRE
		$T_MEMOIRE = $T_Argument; 
		// reconstruction du tableau T_Argument
		$T_Argument = array();
		$T_Argument[0] = $T_MEMOIRE[2];
		$T_Argument[1] = $T_MEMOIRE[3];
	} else if ((substr($res_liste_service['MS_Modele_Service'],10,6) == 'Disque') && ($res_liste_service['MS_EST_MACRO'] != true)) // Sinon si le modèle est un disque
	{
		$linux= False;
		if (substr($T_Argument[2],0,1) == "/") // si l'argument 3 commence par un "/" c'est un disque Linux via NRPE issu de la base Centreon
		{ // inversion des arguments et affectation de la variable linux à vrai
			$T_Disque = $T_Argument; 
			$T_Argument = array();
			$T_Argument[0] = $T_Disque[2];// Disque
			$T_Argument[2] = $T_Disque[1];// Critique
			$T_Argument[1] = $T_Disque[0];// Degrade
			$linux = True;
		} else if (substr($T_Argument[0],0,1) == "/") // si l'argument 3 commence par un "/" c'est un disque Linux via NRPE issu de la base supervision;
		{
			// on conserve l'ordre des arguments
			$linux = True;
		}; // sinon on ne change rien car c'est un disque windows
		if ($linux == True)// si c'est un disque linux
		{
			for ($i=1;$i<=2;$i++)
			{
				if (strtoupper(substr($T_Argument[$i],-2)) == "MO") // si la chaine est en Mo on la transmforme en MB
				{
					$T_Argument[$i] = substr($T_Argument[$i],0,strlen($T_Argument[$i])-2) . "MB";
				};
				if (strtoupper(substr($T_Argument[$i],-2)) == "GO") // si la chaine est en Go on la transmforme en GB
				{
					$T_Argument[$i] = substr($T_Argument[$i],0,strlen($T_Argument[$i])-2) . "GB";
				};
				
				if (substr($T_Argument[$i],-2) == "MB") // taille en MB
				{
					if (strlen(substr($T_Argument[$i],0,strlen($T_Argument[$i])-2)) >= 4)
					{
						$T_Argument[$i]=substr($T_Argument[$i],0,strlen($T_Argument[$i])-2)/1000 . "GB";
					};
	            } else if (substr($T_Argument[$i],-2) == "GB") // Taille en GB, on ne change rien
				{
					$T_Argument[$i] = $T_Argument[$i];
				} else if ((substr($T_Argument[$i],-3) == "%25") || (substr($T_Argument[$i],-1) == "%")) // Taille en pourcentage encodés (% => %25), on ne change rien
				{
					$T_Argument[$i] = $T_Argument[$i];
				} else // Taille sans unité donc en MB, on converti si dépasse le GB et on affiche l'unité
				{
					if (strlen($T_Argument[$i]) >= 4) // la taille dépasse le GB
					{
						$T_Argument[$i]=$T_Argument[$i]/1000 . "GB";
					}else
					{
						$T_Argument[$i]=$T_Argument[$i] . "MB";
					};
				};
			};
		} else // ce n'est pas un disque linux
		{
			for ($i=1;$i<=2;$i++)
			{
				if (strtoupper(substr($T_Argument[$i],-2)) == "MO") // si la chaine est en Mo on la transmforme en MB
				{
					$T_Argument[$i] = substr($T_Argument[$i],0,strlen($T_Argument[$i])-2) . "MB";
				};
				if (strtoupper(substr($T_Argument[$i],-2)) == "GO") // si la chaine est en Go on la transmforme en GB
				{
					$T_Argument[$i] = substr($T_Argument[$i],0,strlen($T_Argument[$i])-2) . "GB";
				};

				if (substr($T_Argument[$i],-2) == "MB") // Taille en MB, on converti si on dépasse le GB
	                        {
	                                if (strlen(substr($T_Argument[$i],0,strlen($T_Argument[$i])-2)) >= 4)
	                                {
	                                        $T_Argument[$i]=substr($T_Argument[$i],0,strlen($T_Argument[$i])-2)/1000 . "GB";
	                                };
				} else if (substr($T_Argument[$i],-2) == "GB") // taille en GB, on ne change rien
				{
					$T_Argument[$i]=$T_Argument[$i];
				} else if ((substr($T_Argument[$i],-3) == "%25") || (substr($T_Argument[$i],-1) == "%")) // Taille en pourcentage encodés (% => %25), conversion en espace restant
				{
					$T_Argument[$i] = 100 - substr($T_Argument[$i],0,strlen($T_Argument[$i])-1) . "%"; // affichage en taille restante
				} else // taille sans unité donc en %
                {
//					$T_Argument[$i] = 100 - substr($T_Argument[$i],0,strlen($T_Argument[$i])-1) . "%";; // affichage en taille restante
					$T_Argument[$i] = 100 - $T_Argument[$i] . "%";; // affichage en taille restante
                };
			};
		};
	} else if (($res_liste_service['MS_Modele_Service'] == 'Web: Controle URL simple') && ($res_liste_service['MS_EST_MACRO'] != true)) // Sinon si le modèle est un site web
	{
	// si un seul argument
		if (count($T_Argument) != 1)
		{
			$NbArgument = count($T_Argument);
			$T_URL = $T_Argument;
			for ( $i=0;$i<$NbArgument;$i++)
			{
				if (substr($T_URL[$i],0,4) == "http")
				{
					$T_Argument = array();
					$T_Argument[0] = $T_URL[$i];
				};
			};
		};
/* 
// section devenue inutile car gérée par la dernière condition, les interfaces réseau étant toutes en mode Macro
	} else if ($res_liste_service['MS_Modele_Service'] == 'Interface réseau')
	{
		echo 'EST_MACRO'.$res_liste_service['MS_EST_MACRO'];
		$Liste_Argument = explode('!',$res_liste_service['Parametres']);
		$Liste_Macro = explode('!',$res_liste_service['MS_Macro']);

//		echo '<pre>';
//		print_r($Liste_Macro);
//		echo '</pre>';

		// pour chaque Valeur de MS_Macro, faire correspondre l'argument de la liste 
		$NbMacro = count($Liste_Macro);
		$i=0;
		foreach ($Liste_Macro AS $Macro_Name)
		{
//			echo "Macro_Name=".$Macro_Name;
			for ($j=0;$j<$NbMacro;$j++)
			{
//				echo "Argument_Name=".stristr($Liste_Argument[$j],':',True);
				if ($Macro_Name == stristr($Liste_Argument[$j],':',True))
				{
//					echo "valeur".$j."=".substr(stristr($Liste_Argument[$j],':'),1);
					$T_Argument[$i] = substr(stristr($Liste_Argument[$j],':'),1);
				};
			};
//			echo "\n";
			$i++;
		};
		//};
*/
	} else if (($res_liste_service['MS_Modele_Service'] == 'Windows: Service') && ($res_liste_service['MS_EST_MACRO'] != true)) // Sinon si le modèle est un service windows
	{
		//$T_Argument = explode('-',$res_liste_service['MS_Modele_Service']);
		if (!isset($T_Argument[3]))
		{
			$T_Argument[3] = "NC";
		};
		if (!isset($T_Argument[4]))
		{
			$T_Argument[4] = "NC";
		};
	} else if ((substr($res_liste_service['MS_Modele_Service'],0,3) == 'Rep') && ($res_liste_service['MS_EST_MACRO'] != true)) // Sinon si le modèle est un répertoire
	{
		$Nb_Arg = count($T_Argument);
		for ($i=0;$i<$Nb_Arg;$i++)
		{
			if ($T_Argument[$i] == "l"){ // controle Non récursif
				$T_Argument[$i]="Non";
			} else if ($T_Argument[$i] == "r") { // controle récursif
				$T_Argument[$i]="Oui";
			} else if ($T_Argument[$i] == "a") { // mode Attente
				$T_Argument[$i]="Présence";
			} else if ($T_Argument[$i] == "p") { // mode Purge
				$T_Argument[$i]="Absence";
			} else if ($T_Argument[$i] == "b") { // mode Plage
				$T_Argument[$i]="Plage";
			};
			//addlog("Argument=" . $T_Argument[$i]);
		};
	} else if ($res_liste_service['MS_EST_MACRO'] == true) 
	{
		//echo 'EST_MACRO'.$res_liste_service['MS_EST_MACRO'];
		$Liste_Argument = explode('!',$res_liste_service['Parametres']);
		$Liste_Macro = explode('!',$res_liste_service['MS_Macro']);
		
// 		echo '<pre>';
// 		print_r($Liste_Macro);
// 		echo '</pre>';
// 		echo '<pre>';
// 		print_r($Liste_Argument);
// 		echo '</pre>';
		
		// pour chaque Valeur de MS_Macro, faire correspondre l'argument de la liste
		$NbMacro = count($Liste_Macro);
		$i=0;
		foreach ($Liste_Macro AS $Macro_Name)
		{
//			echo "Macro_Name=".$Macro_Name;
			$Macro_trouvee=False;
//			for ($j=0;$j<$NbMacro;$j++)
			foreach ($Liste_Argument AS $Macro_Argument)
			{
				$Argument_Name=stristr($Macro_Argument,':',True);
				$Argument_Valeur=substr(stristr($Macro_Argument,':'),1);
				//echo "Argument_Name".$j."=".stristr($Liste_Argument[$j],':',True);
//				echo "Argument_Name=" . $Argument_Name;
//				echo "Argument_Valeur=" . $Argument_Valeur;
				//if (!isset($Liste_Argument[$j]))
				if (!isset($Macro_Argument))
				{
					//$Liste_Argument[$j] = "NC:NC";
					$Macro_Argument = "NC:NC";
				};
				//if ($Macro_Name == stristr($Liste_Argument[$j],':',True))
				//if (($Macro_Name == $Argument_Name) && ($Macro_trouvee==False))
				/**
				 * Concordance Macro et Argument
				 * Prise en compte automatique INTERFACE ou INTERFACEID pour les équipements réseau
				 */
				if ((($Macro_Name == $Argument_Name) || ($Macro_Name . "ID" == $Argument_Name)) && ($Macro_trouvee==False))
				{
					//$T_Argument[$i] = substr(stristr($Liste_Argument[$j],':'),1);
					$T_Argument[$i] = $Argument_Valeur;
					$Macro_trouvee=True;
//					echo "Argument_Valeur=" . $T_Argument[$i] . "\n";
					//addlog("valeur".$j."=".$T_Argument[$i]);
				};
			};
//			echo "\n";
			if (!isset($T_Argument[$i]) || ($Macro_trouvee==False))
			{
				$T_Argument[$i] = "NC";
			}
			$i++;
		};
	};

	// on compte le nb enregistrement
$nbLibelle=count($T_Libelle);
//$Num_Argument = 1; // initialisé dans gestion_affichage_arguments.php
$Description = $res_liste_service['MS_Description'];

include('gestion_affichage_arguments.php');
