<?php
/**
 * Lancement génération graphique
 */

/**
 * Récupération des informations
 */

	$reqConsigne = $bdd_supervision->prepare('
		SELECT 
			DATE_FORMAT(sc_date,"%Y-%m-%d") as sc_date,
			sc_direct,
			sc_model,
			sc_indirect,
			sc_total,
			sc_total-sc_direct-sc_indirect as sc_vide
		FROM suivi_consigne 
		ORDER BY sc_date;');
	$reqConsigne->execute(array()) or die(print_r($reqConsigne->errorInfo()));

/**
 * Constitution des chaines pour les séries
 */
	$date="";
	$direct="";
	$indirect="";
	$model="";
	$total="";
	$vide="";
	while($resConsigne = $reqConsigne->fetch())
 	{
	  /* Push the results of the query in an array */
	  $date[]= $resConsigne["sc_date"];
	  $direct[]= $resConsigne["sc_direct"];
 	  $indirect[]= $resConsigne["sc_indirect"];
 	  $model[]= $resConsigne["sc_model"];
 	  $total[]= $resConsigne["sc_total"];
 	  $vide[]= $resConsigne["sc_vide"];
 	}
/**
 * Construction du graphe
 * basé sur pChart
 */
	
 /* CAT:Line chart */

  /* pChart library inclusions */
//  include("pChart/class/pData.class.php");
//  include("pChart/class/pDraw.class.php");
//  include("pChart/class/pImage.class.php");

 /* Inutile pour ce graphe

 function YAxisFormat($Value) {
 	$heures=round($Value/3600,0);
 	//$minutes=round(($Value%3600)/60,0);
 	$charge=$heures . "h"; //. $minutes;
 	//	$minsec = gmdate("i:s", $Value);
 	//	$hours = gmdate("d", $Value)*24 + gmdate("H", $Value);
 	//echo $Value . "=>" . $heures.'h'.$minutes . "\n";
 	return($charge);
 };
  */
  
 /* Create and populate the pData object */
 $MyData = new pData();
 /* Will append the "autumn" palette to the current one */
 $MyData->addPoints($direct,"consignes directes");
 $MyData->addPoints($indirect,"consignes indirectes");
 $MyData->addPoints($vide,"sans consignes");
 $MyData->setAxisName(0,"Nombre de consignes");
 $MyData->addPoints($date,"Labels");
 $MyData->setSerieDescription("Labels","Dates");
 $MyData->setAbscissa("Labels");

 $serieSettings = array("R"=>55,"G"=>91,"B"=>127,"Alpha"=>80);
 $MyData->setPalette("consignes directes",$serieSettings);
 $serieSettings = array("R"=>97,"G"=>193,"B"=>203,"Alpha"=>80);
 $MyData->setPalette("consignes indirectes",$serieSettings);
 $serieSettings = array("R"=>238,"G"=>209,"B"=>122,"Alpha"=>50);
 $MyData->setPalette("sans consignes",$serieSettings);
 /* Normalize the data series to 100% */
 $MyData->normalize(100,"%");
 /* Create the pChart object */
 $myPicture = new pImage(1100,450,$MyData);
 /* Overlay with a gradient - dégradé de couleur de fond*/ 
 $myPicture->drawGradientArea(0,0,1100,450,DIRECTION_VERTICAL,array("StartR"=>0, "StartG"=>0, "StartB"=>0, "EndR"=>0, "EndG"=>0, "EndB"=>0, "Alpha"=>0));
 //$myPicture->drawGradientArea(0,0,1100,450,DIRECTION_VERTICAL,array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50));
 //$myPicture->drawGradientArea(0,0,1100,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
  
 /* Set the default font properties */
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/verdana.ttf","FontSize"=>8,"R"=>0,"G"=>0,"B"=>0));
 
 /* Define the chart area */
 $myPicture->setGraphArea(60,40,1050,370);

 /* Draw the scale */
 $AxisBoundaries = array(0=>array("Min"=>0,"Max"=>100));
 $scaleSettings = array("XMargin"=>0,"YMargin"=>0,"Floating"=>TRUE,"LabelRotation"=>45,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>false,"CycleBackground"=>False,"Mode"=>SCALE_MODE_MANUAL,"ManualScale"=>$AxisBoundaries);
 $myPicture->drawScale($scaleSettings);
// $myPicture->drawScale(array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"DrawSubTicks"=>TRUE,"Mode"=>SCALE_MODE_ADDALL_START0));
 $myPicture->drawStackedAreaChart(array("DrawPlot"=>false,"DrawLine"=>TRUE,"LineSurrounding"=>-20));
 //$myPicture->drawPlotChart(array("DisplayValues"=>False,"DisplayColor"=>255,255,255));
 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Write the chart legend */
 $myPicture->drawLegend(690,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>0,"FontG"=>0,"FontB"=>0));
 
 /* Render the picture (choose the best way) */
 //ob_end_flush();
 //  $myPicture->autoOutput("charge_temps.png");
 //  	$myPicture2->autoOutput("charge_nombre.png");
 $myPicture->Render("suivi_consigne.png");
 
 
 /* Create and populate the pData object */
 $MyData2 = new pData();
 $MyData2->addPoints($direct,"consignes directes");
 $MyData2->addPoints($indirect,"consignes indirectes");
 $MyData2->addPoints($total,"Nombre total de services");
 $MyData2->setAxisName(0,"Nombre de consignes/services");
 $MyData2->addPoints($date,"Labels");
 $MyData2->setSerieDescription("Labels","Dates");
 $MyData2->setAbscissa("Labels");
 
 $serieSettings = array("R"=>55,"G"=>91,"B"=>127);
 $MyData2->setPalette("consignes directes",$serieSettings);
 $serieSettings = array("R"=>97,"G"=>193,"B"=>203);
 $MyData2->setPalette("consignes indirectes",$serieSettings);
 $serieSettings = array("R"=>238,"G"=>209,"B"=>122);
 $MyData2->setPalette("sans consignes",$serieSettings);
 
//  /* Normalize the data series to 100% */
//  $MyData->normalize(100,"%");
 /* Create the pChart object */
 $myPicture2 = new pImage(1100,450,$MyData2);
 /* Overlay with a gradient - dégradé de couleur de fond*/
 $myPicture2->drawGradientArea(0,0,1100,450,DIRECTION_VERTICAL,array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50));
 //$myPicture->drawGradientArea(0,0,1100,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
 
 /* Set the default font properties */
 $myPicture2->setFontProperties(array("FontName"=>"pChart/fonts/calibri.ttf","FontSize"=>8,"R"=>0,"G"=>0,"B"=>0));
 
 /* Define the chart area */
 $myPicture2->setGraphArea(60,40,1050,370);
 
 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>True,"LabelRotation"=>45,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture2->drawScale($scaleSettings);
 $myPicture2->drawLineChart();
 $myPicture2->drawPlotChart(array("DisplayValues"=>True,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));
 
//  // $myPicture->drawScale(array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"DrawSubTicks"=>TRUE,"Mode"=>SCALE_MODE_ADDALL_START0));
//  $myPicture2->drawStackedAreaChart(array("DrawPlot"=>TRUE,"DrawLine"=>TRUE,"LineSurrounding"=>-20));
//  $myPicture2->drawPlotChart(array("DisplayValues"=>TRUE,"DisplayColor"=>255,255,255));
 /* Enable shadow computing */
 $myPicture2->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 
 /* Write the chart legend */
 $myPicture2->drawLegend(690,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>0,"FontG"=>0,"FontB"=>0));
 
 /* Render the picture (choose the best way) */
 //ob_end_flush();
 //  $myPicture->autoOutput("charge_temps.png");
 //  	$myPicture2->autoOutput("charge_nombre.png");
 $myPicture2->Render("suivi_consigne2.png");
 