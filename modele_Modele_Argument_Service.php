<?php
$NbArgument = (isset($_POST["NbArgument"])) ? $_POST["NbArgument"]+1 : 1;
echo '<div id="Argument_Service' . $NbArgument . '" class="Argument_Modele_Service">';
	echo '<label for="Libelle' . $NbArgument . '">Arg ' . $NbArgument . ':</label>';
	echo '<input type="text" id="Libelle' . $NbArgument . '" name="Libelle' . $NbArgument . '" onblur="verifChamp(this)" value="" placeholder="Libelle argument' . $NbArgument . '" size="40"/>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Libelle' . $NbArgument . '" />';
	echo '<input type="text" id="Argument' . $NbArgument . '" name="Argument' . $NbArgument . '" onblur="verifChamp(this)" value="" placeholder="exemple' . $NbArgument . '" size="50"/>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Argument' . $NbArgument . '" />';
	echo '<input type="text" id="Macro' . $NbArgument . '" name="Macro' . $NbArgument . '" value="" placeholder="Nom MACRO' . $NbArgument . '" size="30"/>';
	echo '<button id="Supprimer_Argument' . $NbArgument . '" onclick="supprime_Argument(this)">Supprimer</button>';
echo '</div>';
