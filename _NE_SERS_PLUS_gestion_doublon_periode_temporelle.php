<?php
// déprécié
// // Permet de supprimer les doublons automatiquement via l'ajout d'un index sur le couple ID_Demande et Nom_Periode
// addlog("INSERTION_SELECTION: Traitement des doublons dans periode_temporelle...");
// //$DEL_Doublons = $bdd_supervision->prepare('ALTER IGNORE TABLE periode_temporelle ADD UNIQUE INDEX (ID_Demande,Nom_Periode)');
// //$DEL_Doublons->execute(Array()) or die(print_r($DEL_Doublons->errorInfo()));
// //// L index est ensuite supprimé pour ne pas bloquer  l'insertion suivante
// //$DROP_Index = $bdd_supervision->prepare('ALTER TABLE periode_temporelle DROP INDEX ID_Demande');
// //$DROP_Index->execute(Array()) or die(print_r($DROP_Index-errorInfo()));
// $DEL_Doublons = $bdd_supervision->prepare('DELETE pt FROM periode_temporelle as pt LEFT JOIN (SELECT MIN(ID_Periode_Temporelle) AS id FROM periode_temporelle GROUP BY ID_Demande, Nom_Periode) AS T1 ON pt.ID_Periode_Temporelle=T1.id WHERE T1.id IS NULL');
// $DEL_Doublons->execute(Array()) or die(print_r($DEL_Doublons->errorInfo()));
// addlog("INSERTION_SELECTION: Traitement des doublons dans periode_temporelle effectué.");
