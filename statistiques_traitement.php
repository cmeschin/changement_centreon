<?php
/**
 * Lancement
 */

/**
 * Initialisation des constantes
 * Date du jour => pour le stockage de l'heure d'envoi du mail
 * Jour de la semaine => pour la vérification sur la calendrier => De 1 (pour Lundi) à 7 (pour Dimanche)
 * Heure actuelle => pour la vérification sur l'heure de notification
 */
// $debug=false; // activation du mode debug
// //initialisation mail
// $adresse_mail = "jean-marc.raud@tessi.fr;nicolas.schmitt@tessi.fr;lilian.nayagom@tessi.fr;veronique.genay@tessi.fr;cedric.meschin@tessi.fr";
// //$adresse_mail = "c.meschin@free.fr";
// $adresse_mail = str_replace(";", ",", $adresse_mail); // converti les ; en , et ajoute un espace
// if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $adresse_mail)) // On filtre les serveurs qui rencontrent des bogues.
// {
// 	$passage_ligne = "\r\n";
// }
// else
// {
// 	$passage_ligne = "\n";
// };

// try {
// 	include_once('connexion_sql_supervision.php'); // connexion à la base changement
//	$bdd_supervision->beginTransaction();
// 	$heure_envoi = date("d/m/Y H:i");
// 	$jour_semaine = date("N");
// 	$jour7=$jour_semaine+7;
// 	$jour14=$jour_semaine+14;
// 	$jour21=$jour_semaine+21;
	

		
/**
 * Récupération de la liste des demandes effectuées
 */

	$req_lst_dde = $bdd_supervision->prepare('
		SELECT 
			date_format(Date_Supervision_Demandee,"%x-%v") AS Semaine, 
			sum(temps_hote + temps_service)*60 AS Temps_Global, 
			count(Etat_demande) AS Nbre 
		FROM demande 
		WHERE Date_Supervision_Demandee >= DATE_ADD(Now(),INTERVAL -53 WEEK) GROUP BY date_format(Date_Supervision_Demandee,"%x-%v") ORDER BY Semaine;');
	$req_lst_dde->execute(array()) or die(print_r($req_lst_dde->errorInfo()));

	$req_lst_trt = $bdd_supervision->prepare('
		SELECT
			date_format(Date_Fin_Traitement,"%x-%v") AS Semaine,
			sum(temps_hote + temps_service)*60 AS Temps_Global,
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Etat_Demande="Traité" AND Date_Fin_Traitement >= DATE_ADD(Now(),INTERVAL -53 WEEK) GROUP BY date_format(Date_Fin_Traitement,"%x-%v") ORDER BY Semaine;');
	$req_lst_trt->execute(array()) or die(print_r($req_lst_dde->errorInfo()));
	
/**
 * Constitution des chaines pour les séries
 */
	//$res_lst_trt = $req_lst_trt->fetchAll();
	//$res_lst_dde = $req_lst_dde->fetchAll();
	$Semaine="";
	$Temps_A_Traiter="";
	$Nbre_A_Traiter="";
	$Temps_Traite="";
	$Nbre_Traite="";
	while($res_lst_dde = $req_lst_dde->fetch())
 	{
	  /* Push the results of the query in an array */
	  $Semaine[]= $res_lst_dde["Semaine"];
	  $Temps_A_Traiter[] = $res_lst_dde["Temps_Global"];
	  $Nbre_A_Traiter[]= $res_lst_dde["Nbre"];
	 }
	while($res_lst_trt = $req_lst_trt->fetch())
 	{
	  /* Push the results of the query in an array */
	  $Temps_Traite[] = $res_lst_trt["Temps_Global"];
	  $Nbre_Traite[]= $res_lst_trt["Nbre"];
	};
	 // 	$Semaine=$Semaine;
// 	$Temps_A_Traiter=$Temps_A_Traiter;
// 	$Nbre_A_Traiter=$Nbre_A_Traiter;
// 	$Temps_Traite=$Temps_Traite;
// 	$Nbre_Traite=$Nbre_Traite;
	
// 	echo "Semaine\n";
// 	var_dump($Semaine);
//  	echo "Tps_A_Traiter\n";
// 	var_dump($Temps_A_Traiter);
// 	echo "Nbre_A_Traiter=" . var_dump($Nbre_A_Traiter) . "\n";
// 	echo "Tps_Traite=" . var_dump($Temps_Traite) . "\n";
// 	echo "Nbre_Traite=" . var_dump($Nbre_Traite) . "\n";
/**
 * Construction du graphe
 * basé sur pChart
 */
	
 /* CAT:Line chart */

 /* pChart library inclusions */
 include("pChart/class/pData.class.php");
 include("pChart/class/pDraw.class.php");
 include("pChart/class/pImage.class.php");

 function YAxisFormat($Value) {
 	$heures=round($Value/3600,0);
 	//$minutes=round(($Value%3600)/60,0);
 	$charge=$heures . "h"; //. $minutes;
 	//	$minsec = gmdate("i:s", $Value);
 	//	$hours = gmdate("d", $Value)*24 + gmdate("H", $Value);
 	//echo $Value . "=>" . $heures.'h'.$minutes . "\n";
 	return($charge);
 };
 
 /* Create and populate the pData object */
 $MyData = new pData();
 	$MyData2 = new pData();
 $MyData->addPoints($Temps_Traite,"Demandes traitées");
 $MyData->addPoints($Temps_A_Traiter,"Demandes rédigées");
 	$MyData2->addPoints($Nbre_Traite,"Demandes traitées");
 	$MyData2->addPoints($Nbre_A_Traiter,"Demandes rédigées");
 $MyData->setSerieWeight("charge semaine",2);
	$MyData2->setSerieWeight("charge semaine",2);
// $MyData->setSerieTicks("Probe 2",4);
 $MyData->setAxisName(0,"Temps estimé");
 	$MyData2->setAxisName(0,"Nombre de demandes");
 $MyData->addPoints($Semaine,"Labels");
 	$MyData2->addPoints($Semaine,"Labels");
 $MyData->setSerieDescription("Labels","Semaines");
 	$MyData2->setSerieDescription("Labels","Semaines");
// $MyData->setSerieOnAxis("Charge_semaine",0);
 $MyData->setAbscissa("Labels");
 	$MyData2->setAbscissa("Labels");
 // $MyData->setAxisUnit(0,"s"); //unité des données
 
 //$MyData->setAxisDisplay(1,"AXIS_FORMAT_TIME","H:i");
 $MyData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"YAxisFormat");
 /* Create the pChart object */
 $myPicture = new pImage(1100,430,$MyData);
 	$myPicture2 = new pImage(1100,430,$MyData2);
 
 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;
 	$myPicture2->Antialias = FALSE;
 
 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,1100,430,$Settings);
 	$myPicture2->drawFilledRectangle(0,0,1100,430,$Settings);
 
 /* Overlay with a gradient - dégradé de couleur de fond*/ 
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,1100,430,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,1100,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
 	$myPicture2->drawGradientArea(0,0,1100,430,DIRECTION_VERTICAL,$Settings);
 	$myPicture2->drawGradientArea(0,0,1100,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
 
 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,1099,429,array("R"=>0,"G"=>0,"B"=>0));
 	$myPicture2->drawRectangle(0,0,1099,429,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/Forgotte.ttf","FontSize"=>8,"R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawText(10,16,"Charge par semaine",array("FontSize"=>11,"Align"=>TEXT_ALIGN_BOTTOMLEFT));
 	$myPicture2->setFontProperties(array("FontName"=>"pChart/fonts/Forgotte.ttf","FontSize"=>8,"R"=>255,"G"=>255,"B"=>255));
 	$myPicture2->drawText(10,16,"Charge par semaine",array("FontSize"=>11,"Align"=>TEXT_ALIGN_BOTTOMLEFT));
 
 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/pf_arma_five.ttf","FontSize"=>6,"R"=>0,"G"=>0,"B"=>0));
	$myPicture2->setFontProperties(array("FontName"=>"pChart/fonts/pf_arma_five.ttf","FontSize"=>6,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(60,40,1050,370);
 	$myPicture2->setGraphArea(60,40,1050,370);
 
 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"LabelRotation"=>45,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);
 	$myPicture2->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;
 	$myPicture2->Antialias = TRUE;
 
 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 	$myPicture2->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 
 /* Draw the line chart */
 $myPicture->drawLineChart();
 $myPicture->drawPlotChart(array("DisplayValues"=>FALSE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));
 	$myPicture2->drawLineChart();
 	$myPicture2->drawPlotChart(array("DisplayValues"=>FALSE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));
 
 /* Write the chart legend */
 $myPicture->drawLegend(690,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));
 	$myPicture2->drawLegend(690,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));
 
 /* Render the picture (choose the best way) */
//ob_end_flush();
//  $myPicture->autoOutput("charge_temps.png");
//  	$myPicture2->autoOutput("charge_nombre.png");
  $myPicture->Render("charge_temps.png");
  	$myPicture2->Render("charge_nombre.png");
 	
// 	$bdd_supervision->commit();
// } catch (Exception $e) {
//  	$bdd_supervision->rollBack();
//  	die('Erreur traitement envoi_mail: '. $e->getMessage());
// };
