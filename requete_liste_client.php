<?php
$req_client = $bdd_centreon->query(
	'SELECT
		 sg_id AS ID_Client,
		 sg_name AS Code_Client,
		 sg_alias AS Client,
		 sg_activate AS Actif
	 FROM servicegroup
	 ORDER BY sg_name'
	) or die(print_r($req_client->errorInfo()));
