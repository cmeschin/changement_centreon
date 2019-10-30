<?php
$file = 'extraction_pdf/' . $prestation . '.csv';
echo '<h2> Prestation ' . $prestation . '</h2>';
$extract_hote = "Nom_Hote;Description;IP_Hote;Type_Hote;Localisation;OS;Fonction;Actif" . "\r\n";

//die(var_dump($r_hote));
foreach ( $r_hote as $res_hote ) // on boucle sur les valeurs remontée par la requête
{
    //echo $res_hote ['Nom_Hote'];
        $extract_hote .=  htmlspecialchars ( $res_hote ['Nom_Hote'] ) . ';'
        . htmlspecialchars ( $res_hote ['Description'] ) . ';'
        . htmlspecialchars ( $res_hote ['IP_Hote'] ) . ';'
        . htmlspecialchars ( $res_hote ['Type_Hote'] ) . ';'
        . htmlspecialchars ( $res_hote ['ID_Localisation'] ) . ';'
        . htmlspecialchars ( $res_hote ['OS'] ) . ';'
        . htmlspecialchars ( $res_hote ['Fonction'] ) . ';'
        . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . "\r\n";
        //echo $extract_hote;
};

/**
 * affichage service
 */
// echo '<fieldset id="f_extraction_service">';
// echo '<legend>Liste des services</legend>';
$extract_service = "Nom_Hote;IP_Hote;Nom_Service;Parametres;Consigne_Service;Frequence;Nom_Periode;Actif" . "\r\n" ;

foreach ( $r_service as $res_liste_service ) // on boucle sur les valeurs remontée par la requête
{
    $extract_service .= htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . ';'
        . htmlspecialchars ( $res_liste_service ['IP_Hote'] ) . ';'
        . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . ';'
        . htmlspecialchars ( str_replace("\\","",$res_liste_service['Parametres']) ) . ';'
        . htmlspecialchars ( $res_liste_service ['Consigne'] ) . ';'
        . htmlspecialchars ( $res_liste_service ['Frequence'] ) . ';'
        . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . ';'
        . htmlspecialchars ( $res_liste_service ['Controle_Actif'] ) . "\r\n";
    
};

$extract_csv = $extract_hote;
$extract_csv .= "\r\n";
$extract_csv .= "\r\n";
$extract_csv .= $extract_service;

file_put_contents($file, $extract_csv);
echo '<a href="' . $file . '" target="_blank">Cliquez ici pour télécharger le fichier csv</a>';