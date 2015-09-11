<?php
// déprécié 
// Permet de supprimer les doublons automatiquement via l'ajout d'un index sur le couple ID_Hote_Centreon et ID_Demande
//addlog("INSERTION_SELECTION: Traitement des doublons dans hote_temp...");
//$DEL_Doublons = $bdd_supervision->prepare('ALTER IGNORE TABLE hote_temp ADD UNIQUE INDEX (ID_Hote_Centreon,ID_Demande)');
//$DEL_Doublons->execute(Array()) or die(print_r($DEL_Doublons->errorInfo()));
//// L index est ensuite supprimé pour ne pas bloquer  l'insertion suivante
//$DROP_Index = $bdd_supervision->prepare('ALTER TABLE hote_temp DROP INDEX ID_Hote_Centreon');
//$DROP_Index->execute(Array()) or die(print_r($DROP_Index-errorInfo()));
//$DEL_Doublons = $bdd_supervision->prepare('DELETE ht FROM hote_temp as ht LEFT JOIN (SELECT MIN(ID_Hote_temp) AS id FROM hote_temp GROUP BY ID_Demande, ID_Hote_Centreon) AS T1 ON ht.ID_Hote_Temp=T1.id WHERE T1.id IS NULL');
//$DEL_Doublons->execute(Array()) or die(print_r($DEL_Doublons->errorInfo()));

//addlog("INSERTION_SELECTION: Traitement des doublons dans hote_temp effectué.");
