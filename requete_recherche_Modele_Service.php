<?php
 header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include_once('connexion_sql_supervision.php');
$ID_Modele = (isset($_POST["ID_Modele"])) ? $_POST["ID_Modele"] : NULL;
 
if ($ID_Modele )
{
	try {
		$req_modele = $bdd_supervision->prepare('SELECT
				 ID_Modele_Service,
				 Modele_Service,
				 MS_Nb_Arg,
				 MS_Libelles,
				 MS_Arguments
				 FROM modele_service
				 WHERE ID_Modele_Service= :ID_Modele_Service
				 ORDER BY ID_Modele_Service');
		$req_modele->execute(Array(
				'ID_Modele_Service' => $ID_Modele
		))	or die(print_r($req_modele->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete_recherche_Modele_Service: ' . $e->getMessage());
	};
	while($res_modele = $req_modele->fetch())
	{
		for($i=0;$i > $res_modele['MS_Nb_Arg'];$i++)
		{
			$liste_lib = explode("!",$res_modele['MS_Libelles']);
			$liste_arg = explode("!",$res_modele['MS_Arguments']);
// Argument 
			echo '<label for="Libelle'. $i . '">Argument '. $i .':</label>';
			echo '<input type="text" id="Libelle'. $i .'" name="Libelle'. $i .'" onblur="verifChamp(this)" value="" placeholder="' . $liste_lib[$i] . '" size="30"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Libelle'. $i .'" /> <br />';
			echo '<input type="text" id="Argument'. $i .'" name="Argument'. $i .'" onblur="verifChamp(this)" value="" placeholder="' . $liste_lib[$i] . '" size="30"/>';
			echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Argument'. $i .'" /> <br />';
		}
	}
}
