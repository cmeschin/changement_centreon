<?php
// déprécié le 28/11/14
// // Permet de supprimer les doublons automatiquement via l'ajout d'un index sur le triple ID_Demande, Nom_Service et Nom_Hote 
// addlog("INSERTION_SELECTION: Traitement des doublons dans service...");
// //$DEL_Doublons = $bdd_supervision->prepare('ALTER IGNORE TABLE service ADD UNIQUE INDEX (ID_Demande,Nom_Service,Nom_Hote)');
// //$DEL_Doublons->execute(Array()) or die(print_r($DEL_Doublons->errorInfo()));
// //// L index est ensuite supprimé pour ne pas bloquer  l'insertion suivante
// //$DROP_Index = $bdd_supervision->prepare('ALTER TABLE service DROP INDEX ID_Demande');
// //$DROP_Index->execute(Array()) or die(print_r($DROP_Index-errorInfo()));
// $DEL_Doublons = $bdd_supervision->prepare('DELETE s FROM service as s LEFT JOIN (SELECT MIN(ID_Service) AS id FROM service GROUP BY ID_Demande, Nom_Service, Nom_Hote) AS T1 ON s.ID_Service=T1.id WHERE T1.id IS NULL');
// $DEL_Doublons->execute(Array()) or die(print_r($DEL_Doublons->errorInfo()));
// addlog("INSERTION_SELECTION: Traitement des doublons dans service effectué.");
