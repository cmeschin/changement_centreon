<?php
?>
<div id="Hote_Rechercher">
	<div>
		<p><b>Attention, la recherche est limitée aux 15 premiers résultats.</b></p>
		<label for="recherche_hote">Rechercher un Hôte :</label>
		<input onKeyPress="clic_recherche_hote(event);" type="text" id="recherche_hote" name="recherche_hote" value="" placeholder="saisir le nom ou l'IP..." title="Saisir tout ou partie du nom ou de l'IP de l'hôte à rechercher. Seuls les hôtes n'appartenant pas à la prestation sélectionnée peuvent être recherchés ici."/>
		<button id="Rechercher" onclick="charger_liste_recherche_hote()">Rechercher</button>
		<div id="liste_recherche_hote"></div>
		<button disabled="disabled" id="Ajouter_Selection_Hote" onclick="chargerlistes_Ajout()">Ajouter la sélection</button>
	</div>
	<p>Si votre recherche aboutie, sélectionnez les hôtes concernés puis cliquez sur "ajouter la sélection".</p>
	<!-- <p>Si vous souhaitez ajouter un service à un hôte référencé pour cette prestation, vous pourrez le faire après avoir validé votre sélection. Inutile de sélectionner l'hôte concerné dans la liste des hôtes.</p> -->
	<!-- <p>Sélectionnez dans les listes ci-dessous les hôtes et/ou services que vous souhaitez modifier puis cliquez sur "Valider votre sélection" en bas de la page.</p> -->
	<p>Si après recherche, aucun hôte ou service ne correspond à votre demande, cliquez sur "Valider votre sélection", même vide. Vous pourrez ajouter les hôtes et/ou services manquants dans l'onglet paramétrage.</p>
</div>
