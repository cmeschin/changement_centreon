<?php //include "top_auth.php";


session_start ();
if ($_GET['etat'] == "disconnect") {
	$_SESSION['auth_changement_centreon']="";
}

if($_SESSION['auth_changement_centreon']=="changement_centreon") {
        header('Location: index.php');
}
	
$groupe="GG_DEMANDECENTREON_ADMIN UserOfDomain";
$post_user=$_POST["post_user"];
$post_pass=$_POST["post_pass"];
$post_auth=$_POST["post_auth"];
$bouton_recommencer='<br><input type="button" value="Recommencer" OnClick="window.location.href=\'auth.php\'"><br>';

if ($post_auth != "" && $post_pass != "" && $post_user != "" ) {
 //       $post_pass2=addslashes($post_pass);
        $cmd="/mnt/data/www/_bin/auth.sh \"$groupe\" \"$post_user\" \"$post_pass\"";
//	echo $cmd;exit;
        exec($cmd, $exec_output, $exec_retval);
        $i="1";
        foreach($exec_output as $exec_outputline2)
        {
//		echo "$i>> $exec_outputline2<br>";
			if(($i == "1") && ($exec_outputline2=="user ko")) { $error1="user";}
			if(($i == "3") && ($exec_outputline2=="groupe ko")) { $error2="groupe";} elseif($i == "3") {$groupe_user=$exec_outputline2;}
			if ($groupe_user==$groupe) $groupe_user="GG_DEMANDECENTREON_ADMIN";
			if(($i == "2") && ($exec_outputline2=="pass ko")) { $error3="pass";}
            if($i == "4") { $email=$exec_outputline2;}
            if($i == "5") { $name=$exec_outputline2;}
			$i=$i+1;
        };
        $error="$error1$error2$error3";
        if($error!="") {
            //echo "Erreur d'autentification</span><br>";
            if ($error1!="") $error_auth="- Votre user est incorrect<br>";
            if ($error3!="") $error_auth.="- Votre password est incorrect<br>";
            if ($error2!="") $error_auth.="- Vous n'&ecirc;tes pas autoris&eacute; &agrave; consulter cet espace<br>";
        } else {
            //success
            session_start();
            $_SESSION['auth_changement_centreon'] = "changement_centreon";
            $_SESSION['user_changement_centreon'] = $post_user;
			$_SESSION['groupe_changement_centreon'] = $groupe_user;
            $_SESSION['name_changement_centreon'] = $name;
            $_SESSION['email_changement_centreon'] = $email;
            header('Location: /changement_centreon/index.php');
        };
};

if ($post_auth != "" && $post_pass == "" ) {
                if ($error3=="") $error_auth.="- Votre password est incorrect<br>";
};

if ($post_auth != "" && $post_user == "" ) {
                if ($error1=="") $error_auth.="- Votre user est incorrect<br>";
}

$error_auth="<br><div style='color:red; font-size:11px;margin-left:20px'>$error_auth</div>";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Portail Tessi Technologies</title>


<link rel="stylesheet" type="text/css" href="/menu-tt/styles_auth.css">
  
</head>
<body>
  <div id="header">
    <div class="logo">
      <div class="logolink">
        
        <h1><a href="http://heb.tessi-techno.fr/" tabindex="1"><img alt="Site Tessi Technologies" title="Site heb.tessi-techno.fr" src="/menu-tt/logo.png" height="50px"></a> <strong>tessi</strong> technologies</h1>
      </div>
    </div>
	
  </div>
  <div id="sign-in-container">
    <div id="sign-in-header">
      <h1>pour continuer ...</h1>
      <p style="margin-left:2px;"><strong>identifiez-vous</strong></p>
    </div>
    <div id="login-container">
      <div id="large-login-container">
        <div class="active" id="large-login-bg">
          <div id="large-login">
            <h2>s'identifier sur tessi-techno.fr</h2>
            <img src="/menu-tt/img_login-window2.png" alt="Bienvenue" title="Bienvenue" height="140" width="230">
            <div id="sign-in-form">
              <div id="initial-form">
                <div class="error-container-small" id="email-error" style="display:none"></div>
                <form action="" id="authentication_form" method='post'>
                  <fieldset><legend>Login Form</legend>
                  <dl><dd>
									
										<div id="credential-lemma">
											<p><label for="user_credential" style="font-size:11px;"><strong>login</strong></label></p>
										</div>
                    <input class="email" id="post_user"  name="post_user"  tabindex="10" style="font-size: 14px; padding: 0px; border: 1px solid rgb(162, 162, 162);" type="text">
        <input type="hidden" name="post_auth" id="post_auth" value="soumis">

                    <div class="error-container-small" id="password-error" style="display:none;"></div>
                    <div id="password-container">
                      <a class="forgotten-password" href="https://heb.tessi-techno.fr/demande2vm/change-my-password/index.php?action=change-my-password" tabindex="22">mot de passe oublié ?</a>
                      <p class="sign-in-password"><label for="user_password"><strong>mot de passe :</strong></label></p>
                      <input class="password" id="post_auth" name="post_pass" tabindex="20" value="" type="password">
                    </div>
                    <div id="password-anchor" style="display:none;">&nbsp;</div>
                      <div class="valider-mask">
                      <input class="active" value=" " src="/menu-tt/btn_valider.gif" id="valider" alt="Valider" title="Valider" tabindex="21" type="image"> 
                      <img src="/menu-tt/imgChecking.gif" alt="Chargement en cours" title="Chargement en cours" height="16" width="16">
                    </div>
                <!--    <div id="login-checkboxes">
											<label class="checkbox-lbl" for="mem_user" id="label_mem_user">
												<input class="check" id="mem_user" checked="checked" tabindex="23" title="Décochez cette case si vous utilisez un ordinateur public (cybercafé, école, ...)" type="checkbox"><span>mémoriser le login</span>
											</label>
											<label class="checkbox-lbl" for="mem_password" id="label_mem_password">
												<input class="check" id="mem_password" tabindex="24" title="Cochez cette case pour être identifié automatiquement sur cet ordinateur." type="checkbox"><span>mémoriser le mot de passe</span>
											</label>
                    </div>
-->
                  </dd></dl>
                  </fieldset>
                </form>
		<? echo $error_auth; ?>

                <div id="left-retour-login" class="login-confirm" style="display:none; margin-left:5px;">
									<span class="orange">&lt;</span> 
                </div>
                <div class="clear"></div>
              </div>
            </div>
          </div>
        </div>
     
      </div>
    </div>
    <div id="profile-right-container">
			<div class="" id="aux-login-bg">
        <div id="aux-login">
					<h2>Gestionnaire des changements Centreon...</h2>
          <div id="login-mobile-confirm" class="login-confirm" style="display:none">
						<h3>Gestionnaire des changements Centreon</h3>
            <div id="login-pass">
              <img src="/menu-tt/img_mobile.gif" class="mobile" alt="Orange Mobile" title="Orange Mobile"><br>
							<div id="login-arrows"></div>
							<div style="margin-bottom:10px; margin-top:5px; /margin-top:15px; -margin-top:5px;">
								<img class="infoIcon" alt="Information" title="Information" src="/menu-tt/0.gif" height="25" width="25">
								<p>
								</p>
							</div>
						</div>
					</div>
          <div id="login-options">
						<div id="login-mobile">
							<div style="float:left;">
							<img width=60px src="/menu-tt/logo1.png"  >
							</div>
							<div>
								<h3>Le processus</h3>
                                                                <p>	Tout ajout, modification ou suppression de service dans Centreon est un changement et doit être référencé.</p><br />
                                                                <p>	Cette interface a pour but de centraliser et de formaliser les changements en production afin qu'ils soient appliqués dans les meilleures conditions.</p>
                                                                <p>	Les plateformes de recette ne sont pas supervisées.</p>
							</div>
						</div>						
						<div id="login-internet">
							<div style="float:left;">
								<img width=60px src="/menu-tt/logo2.png"  >
							</div>
							<div>
								<h3>Le traitement</h3>
                                                                <p>Les demandes sont traitées dans la mesure du possible au fil de l'eau et en respectant au maximum la date de supervision souhaitée.</p><br />
                                                                <p>Selon l'importance des demandes en terme de volumétrie, elles seront plus ou moins longues à traiter, merci de les anticiper.</p>
                                                                <p></p>
							</div>
						</div>
						<div id="login-fixe">
							<div style="float:left;">
<img width=60px src="/menu-tt/logo3.png"  >							</div>
							<div id="login-fixe-text">
								<h3>L'interface</h3>
								<p>L'ensemble des membres du domaine peuvent se connecter sur l'interface.<br/>
							</div>
						</div>
						<div id="login-footer-text">
							<div class="login-area">
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
  <div class="clear"></div>
  <div class="footer">
		<ul id="listBottom">
			<li><a href='https://heb.tessi-techno.fr/'>heb.tessi-techno.fr</a>
			</li>
			<li>
			</li>
			<li>
			</li>
			<li>
			</li>
			<li>
			</li>
			<li>
			</li>
			<li>
			</li>
			<li>
			</li>
			<li>
			</li>
		</ul>
	</div>
	

</body></html>
