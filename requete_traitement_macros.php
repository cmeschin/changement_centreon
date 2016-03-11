<?php
	$EST_MACRO = True;
	addlog("EST_MACRO=".$EST_MACRO);
		
	// récupère les arguments de type Macro
	//1) récupère la liste exhaustive des macro liées à la commande avec un maximum de 7 modèles (ce qui doit être largement suffisant)
	//	+-----------------------------------------------------------------------------------------------------------+
	//	| Macro                                                                                                     |
	//	+-----------------------------------------------------------------------------------------------------------+
	//	| $_SERVICEINTERFACEID$ -w $_SERVICEWARNING$ -c $_SERVICECRITICAL$ -T $_SERVICEIFSPEED$ -S $_SERVICE64BITS$ |
	//	+-----------------------------------------------------------------------------------------------------------+
	$req_Select_Macro = $bdd_centreon->prepare('
	SELECT SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)-2) AS Macro
	 FROM service AS S
	 LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
	 LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
	 LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
	 LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
	 LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
	 LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
	 LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
	 LEFT JOIN command AS c on c.command_id = coalesce(S.command_command_id,T1.command_command_id,T2.command_command_id,T3.command_command_id,T4.command_command_id,T5.command_command_id,T6.command_command_id,T7.command_command_id)
	 WHERE c.command_line IS NOT NULL
		AND S.service_id= :ID_Service_Centreon');
	$req_Select_Macro->execute(Array(
		'ID_Service_Centreon' => $ID_Service_Centreon
	)) or die(print_r($req_Select_Macro->errorinfo()));

//	echo '<pre>';
//	print_r($req_Select_Macro);
//	echo '</pre>';

	//2) extrait chaque Macro de la chaine
	$Chaine_Macro = "";
	while ($res_Select_Macro = $req_Select_Macro->fetch())
	{
		$Chaine_Macro .= " " . htmlspecialchars($res_Select_Macro['Macro']);
	};
	addlog("Chaine_Macro=".TRIM($Chaine_Macro));
	$T_Chaine_Macro = explode(" ",TRIM($Chaine_Macro)); // découpe la chaine en tableau par les espaces
	
//	echo '<pre>';
//	print_r($T_Chaine_Macro);
//	echo '</pre>';

	$NbLigne=count($T_Chaine_Macro);
	$Liste_Macro = Array(); // recrée un nouveau tableau qui contiendra uniquement les noms des macro
	$i=0;
	for ($j=0;$j<$NbLigne;$j++)
	{
		/**
		 * redécoupe les chaines pour extraire les valeurs des MACRO
		 * cas possibles
		 * 	--warning-in-traffic=$_SERVICEWARNING$
		 * 	$_SERVICEINTERFACEID$'
		 * 	--critical-in-traffic='$_SERVICECRITICAL$'
		 * 	--interface='^$_SERVICEINTERFACE$$$'
		 */

		/**
		 * récupérer la position du premier $
		 */
		
		$ChaineBrute=$T_Chaine_Macro[$j];
		addlog("chaineMacrobrute_avant=" . $ChaineBrute);
		$ChaineBrute=preg_replace('/\${2,}/', '\$', $ChaineBrute); // Supprime les dollars multiples
		addlog("chaineMacrobrute_apres=" . $ChaineBrute);
		
		$pos_premier_dollar=strpos($ChaineBrute,'$');
		$pos_second_dollar=strpos($ChaineBrute,'$',$pos_premier_dollar+1);
		$ChaineMacro=substr($ChaineBrute,$pos_premier_dollar,$pos_second_dollar-$pos_premier_dollar);
		addlog("position premier dollar=".$pos_premier_dollar);
		addlog("position second dollar=".$pos_second_dollar);
		addlog("chaineMacro=".$ChaineMacro);
		
		if (substr($ChaineMacro,0,9) == "\$_SERVICE")
		{
			$Liste_Macro[$i] = substr($ChaineMacro,9,$pos_second_dollar); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
						
			addlog("valeur_macro ajoutée=".$Liste_Macro[$i]);
			$i++;
		};
	};

	//3) récupérer la chaine des modèles afin de récupérer la liste des valeurs de chaque modèle
	//	+------------+------------+------------+------------+------------+------------+------------+------------+
	//	| service_id | service_id | service_id | service_id | service_id | service_id | service_id | service_id |
	//	+------------+------------+------------+------------+------------+------------+------------+------------+
	//	|       6405 |       7239 |       5325 |        878 |       5334 |       NULL |       NULL |       NULL |
	//	+------------+------------+------------+------------+------------+------------+------------+------------+
	
	$req_Liste_Modele = $bdd_centreon->prepare('select DISTINCT T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id
		FROM service AS S
		LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
		LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
		LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
		LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
		LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
		LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
		LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
		LEFT JOIN on_demand_macro_service AS M on M.svc_svc_id=coalesce(T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id)
		WHERE S.Service_id = :ID_Service_Centreon');
	$req_Liste_Modele->execute(Array(
		'ID_Service_Centreon' => $ID_Service_Centreon
	)) or die(print_r($req_Liste_Modele->errorInfo()));
	//4) boucle sur les id pour remplir chaque macro
	// on charge l'ensemble des valeur de macro
	$Macro = False; // indicateur
	$NbMacro = count($Liste_Macro);
	//addlog("Nbligne_Macro=".$NbMacro);
	$Val_Macro = Array();
	while ($res_Liste_Modele = $req_Liste_Modele->fetch()) // pour chaque service_id trouvé
	{// on recherche les valeurs de macro renseignée avec une boucle sur les 8 service_id
		for ($k=0;$k<8;$k++)
		{
			$svc_svc_id = htmlspecialchars($res_Liste_Modele[$k]);
			//addlog("svc_svc_id=".$svc_svc_id);
			if (($svc_svc_id != NULL) OR ($svc_svc_id != "")) // si le modèle n'est pas null, on traite
			{
				$req_Macro_Valeur = $bdd_centreon->prepare('SELECT SUBSTR(svc_macro_name, 10, CHAR_LENGTH(svc_macro_name) - 10),svc_macro_value FROM on_demand_macro_service WHERE svc_svc_id= :svc_svc_id');
				$req_Macro_Valeur->execute(Array(
					'svc_svc_id' => $svc_svc_id
				)) or die(print_r($req_Macro_Valeur->errorInfo()));
				$res_Macro_Valeur = $req_Macro_Valeur->fetchall();
/*
				echo '<pre>';
				print_r($res_Macro_Valeur);
				echo '</pre>';
*/
				for($j=0;$j<$NbMacro;$j++) // pour chaque Liste_Macro
				{
					foreach ($res_Macro_Valeur AS $Macro_Name) // on boucle sur les valeurs remontée par la requête
					{
						//addlog("Liste_Macro=".$Liste_Macro[$j] . "\n" . "Macro_Name=".$Macro_Name[0] . "\n" . "Macro_value=".$Macro_Name[1]);
						if ((strcasecmp($Liste_Macro[$j], $Macro_Name[0]) == 0) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et Macro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
						// strcasecmp => comparaison insensible à la casse
						{
							$Val_Macro[$Macro_Name[0]] = $Macro_Name[0] . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
							addlog("valeur stockée=". $Val_Macro[$Macro_Name[0]]);
						};
					};
				};
			};
		};
	};
/*	echo '<pre>';
	print_r($res_Liste_Modele);
	echo '</pre>'; 
	echo '<pre>';
	print_r($Val_Macro);
	echo '</pre>';
*/
