<?php
// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include_once('connexion_sql_supervision.php');
include_once('connexion_sql_centreon.php');

echo '<label for="gb_nom" class="gb_nom" onclick="alert(\'Nom de la règle de notification BAM. Ce nom doit être unique.\')">Nom de la règle <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
echo '<input type="text" id="gb_nom" name="gb_nom" onblur="verifChamp(this)" size="100" value="" class="gb_nom gb_config"/>';
echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_gb_nom" /> <br />';

echo '<label for="gb_mail_objet" class="gb_mail_objet" onclick="alert(\'Objet du mail de notification.\')">Objet du mail <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
echo '<input type="text" id="gb_mail_objet" name="gb_mail_objet" onblur="verifChamp(this)" size="100" value="" class="gb_mail_objet gb_config"/>';
echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_gb_mail_objet" /> <br />';

echo '<label for="gb_mail_titre" class="gb_mail_titre" onclick="alert(\'Titre du corps du mail.\')">Titre du corps du mail <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
echo '<input type="text" id="gb_mail_titre" name="gb_mail_titre" onblur="verifChamp(this)" size="100" value="" class="gb_mail_titre gb_config"/>';
echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_gb_mail_titre" /> <br />';

echo '<label for="gb_mail_liste" class="gb_mail_liste" onclick="alert(\'Liste de diffusion. Séparer les adresses par un point-virgule (;)\')">Liste de diffusion <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
echo '<input type="text" id="gb_mail_liste" name="gb_mail_liste" onblur="verifChampMail(this)" size="100" value="" class="gb_mail_liste gb_config"/>';
echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_gb_mail_liste" /> <br />';

echo '<div class="div_jour">';
echo '<p id="liste_jour">Cochez les jours de notifications :<br />';
	echo '<input type="checkbox" name="lundi" id="lundi" class="gb_jour gb_config"/> <label for="lundi">Lundi</label><br />';
	echo '<input type="checkbox" name="mardi" id="mardi" class="gb_jour gb_config"/> <label for="mardi">Mardi</label><br />';
	echo '<input type="checkbox" name="mercredi" id="mercredi" class="gb_jour gb_config"/> <label for="mercredi">Mercredi</label><br />';
	echo '<input type="checkbox" name="jeudi" id="jeudi" class="gb_jour gb_config"/> <label for="jeudi">Jeudi</label><br />';
	echo '<input type="checkbox" name="vendredi" id="vendredi" class="gb_jour gb_config"/> <label for="vendredi">Vendredi</label><br />';
	echo '<input type="checkbox" name="samedi" id="samedi" class="gb_jour gb_config"/> <label for="samedi">Samedi</label><br />';
	echo '<input type="checkbox" name="dimanche" id="dimanche" class="gb_jour gb_config"/> <label for="dimanche">Dimanche</label><br />';
	echo '</p>';
echo '</div>';
	echo '<div id="heure">';
	echo '<label for="gb_heure" class="gb_heure" onclick="alert(\'Heure de la notification.\')">Heure d\'envoi de la notification <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<input type="text" id="gb_heure" name="gb_heure" size="5" onblur="verifChamp(this)" class="gb_heure gb_config"/>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_gb_heure" /><br />';
echo '</div>';
echo '<div id="charger_liste_am">';
	include('BAM_charge_liste_am.php');
echo '</div>';
echo '<button id="Enregistre_Notif_BAM" onclick="Enregistre_Notif_BAM();">Enregistrer notif BAM</button> <br />';
echo '</div>';
