<!DOCTYPE html>
<?php
if (session_id()=='')
{
session_start();
};
$R_ID_Demande = (isset($_GET["id_dem"])) ? $_GET["id_dem"] : NULL;
$_SESSION['R_ID_Demande'] = htmlspecialchars($R_ID_Demande);
$_SESSION['Reprise'] = false;

?>
<html>
<head>
        <?php
		include('top.php');
		include('head.php');
        ?>
</head>
<body>
<div id="principal">
	<header id="en-tete">
		<?php
			include('menu.php');
		?>
	</header>
	<section>
		<?php // si Id_Demande transmis dans l'URL on charge simplement le contenu de la demande sinon on charge les deux onglets
		if ($_SESSION['R_ID_Demande'] != NULL )
		{
		?>
			<div id="tabs_DEC">
				 <ul>
					<li><a href="#tabs_DEC-1">Demande recherchée</a></li>
				</ul>
				<div id="tabs_DEC-1">
					<h2>Voici le détail de la demande recherchée</h2>
					<?php
						include('recherche_demande.php');
					?>
				</div>
			</div>
		<?php
		} else 
		{
		?>
			<div id="tabs_DEC">
				 <ul>
					<li><a href="#tabs_DEC-1">Liste des demandes en cours</a></li>
					<li><a href="#tabs_DEC-2">Liste des demandes traitées ou annulées</a></li>
				</ul>
				<div id="tabs_DEC-1">
					<h2>Les brouillons et demandes à traiter</h2>
					<?php
						include_once('liste_demande_encours.php');
						//si ID_Demande transmis on simule le clic
					?>
				</div>
				<div id="tabs_DEC-2">
					<h2>Les demandes traitées ou annulées regroupées par mois</h2>
					<?php
						include_once('liste_demande_traitees.php');
					?>
				</div>
			</div>
		<?php 
		};
		?>
	</section>
	<footer>
		<?php
			include('PiedDePage.php');
		?>
	</footer>
</div>
<?php
        include('section_script_JS.php');
?>
</body>
</html>
