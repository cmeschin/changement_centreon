<?php
// déprécié 03-11-2014
$DEL_Hote_Temp = $bdd_supervision->prepare('DELETE FROM hote_temp WHERE ID_Demande IN (SELECT ID_Demande FROM demande WHERE Etat_Demande IN ("Traité","Annulé"))');
$DEL_Hote_Temp->execute(array()) or die(print_r($DEL_Hote_Temp->errorInfo()));
