<?php 
if (session_id()=='')
{
	session_start();
};
$_SESSION['auth_changement_centreon'] = "changement_centreon";
//$_SESSION['groupe_changement_centreon'] = "GG_DEMANDECENTREON_ADMIN";
//$_SESSION['groupe_changement_centreon'] = "GG_DEMANDECENTREON_USER";
$_SESSION['groupe_changement_centreon'] = "UserOfDomain";
$_SESSION['name_changement_centreon'] = "Cedric MESCHIN";
$_SESSION['user_changement_centreon'] = "cmeschin";
$_SESSION['email_changement_centreon'] = "cedric.meschin@tessi.fr";
