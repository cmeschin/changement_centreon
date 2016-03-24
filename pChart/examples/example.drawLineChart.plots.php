<?php   
 /* CAT:Line chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(34740,  26940,  54960,  53220,  40320,  13440,   5580,  27420,  49620,  22200,  19080,  33360,  34680,  31320,  41700,  52560,  25440,   5460,  19500,   7080,   8700,  18900,  41400,  23760,  39480,  56580,  30300,  16140, 103320,  30600,  33900,   8580,  63120,  46980,  24840,  57360,  57660,  39000,  28440,  17820,  11880,   4620,  65820,  10080,  36780,  32700,  10680,  21540,   9240,  22860,  21180,  57660,  49140,  33240),"Charge_semaine");
 //$MyData->addPoints(array(2,7,5,18,19,22),"Probe 2");
 $MyData->setSerieWeight("charge semaine",2);
 //$MyData->setSerieTicks("Probe 2",4);
 $MyData->setAxisName(0,"Temps estimé");
 $MyData->addPoints(array("2015-12","2015-13","2015-14","2015-15","2015-16","2015-17","2015-18","2015-19","2015-20","2015-21","2015-22","2015-23","2015-24","2015-25","2015-26","2015-27","2015-28","2015-29","2015-30","2015-31","2015-32","2015-33","2015-34","2015-35","2015-36","2015-37","2015-38","2015-39","2015-40","2015-41","2015-42","2015-43","2015-44","2015-45","2015-46","2015-47","2015-48","2015-49","2015-50","2015-51","2015-52","2015-53","2016-01","2016-02","2016-03","2016-04","2016-05","2016-06","2016-07","2016-08","2016-09","2016-10","2016-11","2016-12"),"Labels");
 $MyData->setSerieDescription("Labels","Semaines");
// $MyData->setSerieOnAxis("Charge_semaine",0);
 $MyData->setAbscissa("Labels");
 //$MyData->setAxisUnit(0,"m"); //unité des données
 
 //$MyData->setAxisDisplay(1,"AXIS_FORMAT_TIME","H:i");
 $MyData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"YAxisFormat");
 /* Create the pChart object */
 $myPicture = new pImage(1100,430,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,1100,430,$Settings);

 /* Overlay with a gradient - dégradé de couleur de fond*/ 
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,1100,430,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,1100,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,1099,429,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>8,"R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawText(10,16,"Charge par semaine",array("FontSize"=>11,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(60,40,1050,370);

 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"LabelRotation"=>45,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the line chart */
 $myPicture->drawLineChart();
 $myPicture->drawPlotChart(array("DisplayValues"=>FALSE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));

 /* Write the chart legend */
 $myPicture->drawLegend(690,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/test.png");
 
 function YAxisFormat($Value) { 
	$heures=round($Value/3600,0);
	//$minutes=round(($Value%3600)/60,0);
	$charge=$heures . "h"; //. $minutes;
//	$minsec = gmdate("i:s", $Value);
//	$hours = gmdate("d", $Value)*24 + gmdate("H", $Value);
	//echo $Value . "=>" . $heures.'h'.$minutes . "\n";
 
 return($charge); }
?>