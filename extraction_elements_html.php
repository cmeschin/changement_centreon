<?php
if (session_id () == '') {
	session_start ();
};

$prestation = (isset ( $_POST ["prestation"] )) ? $_POST ["prestation"] : NULL;
$_SESSION['Extraction'] = true;
$_SESSION['PDF'] = false;


include('requete_extraction_elements.php'); // préparation des données
include("construction_page_html.php");// construction de la page spécifique HTML
