<?php
// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include_once('connexion_sql_supervision.php');
//include('log.php'); // chargement de la fonction de log
/* echo $_POST['monclient']; */
//$sMonClient = (isset($_POST["monclient"])) ? $_POST["monclient"] : NULL;

//if ($sMonClient ) {
// récupérer la liste de toutes les demandes à traiter et en cours
try {
	include_once('requete_BAM_liste_notifications.php');
} catch (Exception $e) {
	die('Erreur requete BAM liste notifications: ' . $e->getMessage());
};

/**
 * parcourir les éléments et construire le tableau
 */ 
$res_bam = $req_bam->fetchall();
foreach ($res_bam AS $valeur)
{
	
};


echo '<table id="T_Liste_Notif" class="liste_notification_bam">';
echo '<tr>';
echo '<th>Nom de la règle</th>';
echo '<th>Objet du mail</th>';
echo '<th>Liste de diffusion</th>';
echo '<th>Jours de notification</th>';
echo '<th>Heure d\'envoi</th>';
echo '<th>Gestion des notifications</th>';
echo '</tr>';
$i = 1;
foreach ($res_bam AS $valeur) // on boucle sur les valeurs remontée par la requête
{
	$gb_jour="";
	echo '<tr>';
	echo '<td>' . htmlspecialchars ( $valeur ['gb_nom'] ) . '</td>';
	echo '<td>' . htmlspecialchars ( $valeur ['gb_mail_objet'] ) . '</td>';
	echo '<td>' . htmlspecialchars ( $valeur ['gb_mail_liste'] ) . '</td>';
	if (htmlspecialchars($valeur['gb_lundi'])=='1')
	{
		$gb_jour = ',L';
	};
	if (htmlspecialchars($valeur['gb_mardi'])=='1')
	{
		$gb_jour .= ',M';
	};
	if (htmlspecialchars($valeur['gb_mercredi'])=='1')
	{
		$gb_jour .= ',Me';
	};
	if (htmlspecialchars($valeur['gb_jeudi'])=='1')
	{
		$gb_jour .= ',J';
	};
	if (htmlspecialchars($valeur['gb_vendredi'])=='1')
	{
		$gb_jour .= ',V';
	};
	if (htmlspecialchars($valeur['gb_samedi'])=='1')
	{
		$gb_jour .= ',S';
	};
	if (htmlspecialchars($valeur['gb_dimanche'])=='1')
	{
		$gb_jour .= ',D';
	};
	$gb_jour = substr($gb_jour,1);
	echo '<td>' . $gb_jour . '</td>';
	echo '<td>' . htmlspecialchars ( $valeur ['gb_heure'] ) . '</td>';
	echo '<td>';
	if (htmlspecialchars ( $valeur ['gb_actif'] ) == 0)
	{
		echo '<input type="checkbox" id="gb_activation'.$valeur['gb_id'].'" name="gb_activation'.$valeur['gb_id'].'" onchange="gb_activation('.$valeur['gb_id'].')" title="Activer cette notification."></input>';
	} else
	{
		echo '<input checked="Checked" type="checkbox" id="gb_activation'.$valeur['gb_id'].'" name="gb_activation'.$valeur['gb_id'].'" onchange="gb_activation('.$valeur['gb_id'].')" title="Désactiver cette notification."></input>';
	};
		echo '<button id="gb_supprimer'.$valeur['gb_id'].'" onclick="gb_supprimer('.$valeur['gb_id'].')" title="Supprimer cette notification.">Supprimer</button>';
		echo '<button id="gb_forcer'.$valeur['gb_id'].'" onclick="gb_forcer('.$valeur['gb_id'].')" title="Forcer la notification maintenant.">Forcer</button><br />';
		echo '</td>';
	echo '</tr>';
	$i ++;
};
echo '</table>';
