<?php

$req_type = $bdd_supervision->query('SELECT
		 Type_Hote,
		 Type_Description
		 FROM hote_type
		 ORDER BY Type_Description'
) or die(print_r($req_type->errorInfo()));
