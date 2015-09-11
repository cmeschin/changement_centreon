<!-- <!DOCTYPE html> -->
<!-- <html> -->
<!-- <head> -->
<!-- 	<meta charset="utf-8" /> -->
<!--	<link href="CalendarControl.css"  rel="stylesheet" type="text/css"> -->
<!-- 	<link href="css/style.css" rel="stylesheet" type="text/css" /> -->
<!-- </head> -->
<!--	<p class="logo"><img src="images/Tessi_DS_Mail.gif" alt="logo Tessi Technologies" /></p>-->
	<h1><a class="titre" href="index.php">Gestionnaire des changements CENTREON - DEVELOPPEMENT</a></h1>
	<nav class="menu">
		<ul>
		<li><a href="nouvelle_demande.php">Formuler une demande</a></li>
		<li><a href="lister_demande.php">Lister les demandes</a></li>
		<li><a href="extraction_prestation.php">Extraire une prestation</a></li>
		<li><a href="documentation.php">Documentation</a></li>
		<?php
		if ($_SESSION['Admin']==True)
		{
			echo '<li><a href="administration.php">Administration</a></li>';
		}
		?>
		</ul>
	</nav>	
<!-- </html> -->
