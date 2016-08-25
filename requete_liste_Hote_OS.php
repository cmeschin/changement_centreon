<?php

$req_OS = $bdd_supervision->query('SELECT
		 Type_OS,
		 Type_OS_Desc
		 FROM hote_os
		 WHERE Type_OS <> "NC"
		 ORDER BY Type_OS'
) or die(print_r($req_OS->errorInfo()));
