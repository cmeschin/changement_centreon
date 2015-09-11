<?php
$req_bam = $bdd_supervision->prepare(
		'SELECT
			gb_id,
			gb_nom,
			gb_prestation,
			gb_lundi,
			gb_mardi,
			gb_mercredi,
			gb_jeudi,
			gb_vendredi,
			gb_samedi,
			gb_dimanche,
			gb_heure,
			gb_mail_objet,
			gb_mail_titre,
			gb_mail_liste
		FROM gestion_bam_notification
		ORDER BY gb_nom');
$req_bam->execute(array()) or die(print_r($req_bam->errorInfo()));
