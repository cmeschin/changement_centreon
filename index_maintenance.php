<!DOCTYPE html>
<html>
<head>
<!--	<meta charset="utf-8" /> -->
<!--	<link href="css/style.css" rel="stylesheet" type="text/css" /> -->
<!--	<title>Changement Centreon - Tessi Technologies</title> -->
<link rel="icon" href="./images/favicon.ico" />
<?php
	if (session_id()=='')
	{
	session_start();
	};
	include('log.php'); // chargement de la fonction de log
	addlog("Chargement de la page supervision!");
	include('top.php');
	if ((isset($_SESSION['groupe_changement_centreon'])) && ($_SESSION['groupe_changement_centreon']=="GG_DEMANDECENTREON_ADMIN")) 
	{
	        $_SESSION['Admin']=True;
	        //echo "Admin";
	}else {
	        $_SESSION['Admin']=False;
	//      $_SESSION['user']="toto";
	        //echo "erreur authentification";
	};
	include_once('head.php');
?>
</head>
<body>
<div id="principal">
	<header id="en-tete">
		<?php
			//include_once('menu.php');
		?>
	</header>
	<section>
		<p>Bonjour <?php echo (isset($_SESSION['groupe_changement_centreon'])) ? $_SESSION['name_changement_centreon']: "" ?>,
		<br />
			<?php
				$date_demande=date("d-m-Y H:i:s");
				echo 'nous sommes le ' . $date_demande;
			?>
		<br />
		<br />
		<h3>Site en maintenance, merci de votre compréhension.</h3></p>
	</section>
	<footer>
		<?php
			include_once('PiedDePage.php');
		?>
	</footer>
</div>
</body>
</html>
