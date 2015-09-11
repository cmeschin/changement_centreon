<?php

//$req_type = $bdd_supervision->query('SELECT ID_Type_Hote, Type_Hote, Type_Description FROM Type_Hote ORDER BY Type_Description') or die(print_r($req_type->errorInfo()));
// $req_type = $bdd_supervision->query('SELECT
// 		 Type_Hote,
// 		 Type_Description
// 		 FROM type_hote
// 		 ORDER BY Type_Description'
// 	) or die(print_r($req_type->errorInfo()));
$req_type = $bdd_supervision->query('SELECT
		 Type_Hote,
		 Type_Description
		 FROM hote_type
		 ORDER BY Type_Description'
) or die(print_r($req_type->errorInfo()));
