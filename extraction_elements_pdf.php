<?php 
if (session_id () == '') {
	session_start ();
};
$prestation = (isset ( $_POST ["prestation"] )) ? $_POST ["prestation"] : NULL;
$_SESSION['Extraction'] = true;
$_SESSION['PDF'] = true;

/**
 * gestion PDF avec HTML2PDF
 */

ob_start();

include( "requete_extraction_elements.php" ); // préparation des données
include("construction_page_pdf.php");// construction de la page spécifique HTML2PDF
$content = ob_get_clean();
//require_once( "html2pdf/html2pdf.class.php");
require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
try
{
	$html2pdf = new HTML2PDF("L", "A4", "fr");
	// $html2pdf->setModeDebug();
	$html2pdf->getHtmlFromPage($content);
//	$html2pdf->setDefaultFont("Arial");
	$html2pdf->pdf->setDisplayMode("fullpage");
	$html2pdf->writeHTML($content);
	//$html2pdf->Output("votre_pdf.pdf");
	//Détermination d'un nom de fichier temporaire dans le répertoire courant
	$file = basename(tempnam('.', 'tmp'));
	rename($file, $file.'.pdf');
	$file = 'extraction_pdf/' . $prestation . '.pdf';
	//Sauvegarde du PDF dans le fichier
	$html2pdf->Output($file, 'F');
	//Redirection
	echo '<a href="' . $file . '" target="_blank">Cliquez ici pour télécharger le fichier pdf</a>';
	exit;
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
};
