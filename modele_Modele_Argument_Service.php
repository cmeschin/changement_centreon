<?php
	$NbArgument = (isset($_POST["NbArgument"])) ? $_POST["NbArgument"]+1 : 1;
?>
	<div id="Argument_Service<?php echo $NbArgument;?>" class="Argument_Modele_Service">
<!-- argument -->
		<label for="Libelle<?php echo $NbArgument;?>">Arg <?php echo $NbArgument;?>:</label>
		<input type="text" id="Libelle<?php echo $NbArgument;?>" name="Libelle<?php echo $NbArgument;?>" onblur="verifChamp(this)" value="" placeholder="Libelle argument<?php echo $NbArgument;?>" size="40"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Libelle<?php echo $NbArgument;?>" />
		<input type="text" id="Argument<?php echo $NbArgument;?>" name="Argument<?php echo $NbArgument;?>" onblur="verifChamp(this)" value="" placeholder="exemple<?php echo $NbArgument;?>" size="50"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Argument<?php echo $NbArgument;?>" />
		<input type="text" id="Macro<?php echo $NbArgument;?>" name="Macro<?php echo $NbArgument;?>" value="" placeholder="Nom MACRO<?php echo $NbArgument;?>" size="30"/>
		<button id="Supprimer_Argument<?php echo $NbArgument;?>" onclick="supprime_Argument(this)">Supprimer</button>
	</div>
