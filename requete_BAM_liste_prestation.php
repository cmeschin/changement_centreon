<?php
$req_lst_prestation = $bdd_centreon->prepare(
	'SELECT 
		substring_index(sg_name,"_{",1) as prestation
	 FROM servicegroup
	 WHERE sg_activate="1"
	  ORDER BY sg_name;');
$req_lst_prestation->execute(array()) or die(print_r($req_lst_prestation->errorInfo()));