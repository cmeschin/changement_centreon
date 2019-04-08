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
//require_once(dirname(__FILE__).'/_html2pdf/html2pdf.class.php');
// html2pdf 4.5.0
require_once(dirname(__FILE__).'/html2pdf/vendor/autoload.php');
try
{
	$html2pdf = new HTML2PDF("L", "A4", "fr");
	$html2pdf->getHtmlFromPage($content);
	$html2pdf->pdf->setDisplayMode("fullpage");
	$html2pdf->writeHTML($content);
	$file = basename(tempnam('.', 'tmp'));
	rename($file, $file.'.pdf');
	$file = 'extraction_pdf/' . $prestation . '.pdf';
	//$html2pdf->Output($file, 'F');
	// html2pdf 4.5.0
	$html2pdf->Output(dirname(__FILE__).'/'.$file, 'F');
	echo '<a href="' . $file . '" target="_blank">Cliquez ici pour télécharger le fichier pdf</a>';
	exit;
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
};
