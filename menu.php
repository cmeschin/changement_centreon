<?php
echo '<h1><a class="titre" href="index.php">Gestionnaire des changements CENTREON</a></h1>';
if (file_exists('maintenance.php')==False)
{
	echo '<nav class="menu">';
		echo '<ul>';
		echo '<li><a href="nouvelle_demande.php">Formuler une demande</a></li>';
		if (($_SESSION['Nouveau']== true) OR ($_SESSION['Reprise']== true))
		{
			echo '<li><a href="#">Lister les demandes</a></li>';
			echo '<li><a href="#">Extraire une prestation</a></li>';
			echo '<li><a href="#">Documentation</a></li>';
		} else 
		{
			echo '<li><a href="lister_demande.php">Lister les demandes</a></li>';
			echo '<li><a href="extraction_prestation.php">Extraire une prestation</a></li>';
			echo '<li><a href="documentation.php">Documentation</a></li>';
		};
		if ($_SESSION['Admin']==True)
		{
			echo '<li><a href="administration.php">Administration</a></li>';
		}
		echo '</ul>';
	echo '</nav>';
};

