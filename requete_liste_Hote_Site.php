<?php
$req_Localisation = $bdd_supervision->query('SELECT
	 ID_Localisation,
	 Lieux
	 FROM localisation
	 ORDER BY Lieux'
) or die(print_r($req_Localisation->errorInfo()));