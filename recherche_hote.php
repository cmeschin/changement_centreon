<?php
echo '<div id="Hote_Rechercher">';
	echo '<div>';
		echo '<p><b>Attention, la recherche est limitée aux 15 premiers résultats.</b></p>';
		echo '<label for="recherche_hote">Rechercher un Hôte :</label>';
		echo '<input onKeyPress="clic_recherche_hote(event);" type="text" id="recherche_hote" name="recherche_hote" value="" placeholder="saisir le nom ou l\'IP..." title="Saisir tout ou partie du nom ou de l\'IP de l\'hôte à rechercher. Seuls les hôtes n\'appartenant pas à la prestation sélectionnée peuvent être recherchés ici."/>';
		echo '<button id="Rechercher" onclick="charger_liste_recherche_hote()">Rechercher</button>';
		echo '<div id="liste_recherche_hote"></div>';
		echo '<button disabled="disabled" id="Ajouter_Selection_Hote" onclick="chargerlistes_Ajout()">Ajouter la sélection</button>';
	echo '</div>';
	echo '<p>Si votre recherche aboutie, sélectionnez les hôtes concernés puis cliquez sur "ajouter la sélection".</p>';
	echo '<p>Si après recherche, aucun hôte ou service ne correspond à votre demande, cliquez sur "Valider votre sélection", même vide. Vous pourrez ajouter les hôtes et/ou services manquants dans l\'onglet paramétrage.</p>';
echo '</div>';
