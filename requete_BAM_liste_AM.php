<?php
// $req_lst_bam = $bdd_centreon->prepare(
// 	'SELECT
// 		ba_id,
// 		name as ba_nom,
// 		description as ba_description,
// 		level_w as ba_seuil_degrade,
// 		level_c as ba_seuil_critique,
// 		current_level as ba_niveau,
// 		last_state_change as ba_dernier_changement,
// 		current_status as ba_statut
// 	FROM mod_bam;');
$req_lst_bam = $bdd_centreon->prepare(
	'SELECT
		mbc_ba_id,
		mbc_ba_nom,
		mbc_ba_description
		FROM mod_bam_centreon;');
$req_lst_bam->execute(array()) or die(print_r($req_lst_bam->errorInfo()));