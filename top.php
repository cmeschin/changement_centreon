<?php
if (session_id()=='')
{
	session_start();
};
////////////////////////////////////////////////////////
/////// Initialisation des variables pour la maison
if	(file_exists('_variable_home1.php')==true)
{
	include("_variable_home1.php");
}
////////////////////////////////////////////////////////

if (isset($_GET['etat']) && ($_GET['etat'] == "disconnect")) 
{
	$_SESSION['auth_changement_centreon'] = "";
};
////////////////////////////////////////////////////////
/////// Initialisation des variables pour la maison
if	(file_exists('_variable_home2.php')==true)
{
	include("_variable_home2.php");
}
////////////////////////////////////////////////////////


if((isset($_SESSION['auth_changement_centreon'])) && ($_SESSION['auth_changement_centreon']=="changement_centreon")) 
{
	echo "<div id='container' style='width:100%;min-width=700px;max-width=1400px; margin: 0 auto; font-family:\"trebuchet ms\",sans-serif;font-size:13px; '>";
	echo "<div>";
	echo '<div style="float: left; width: 48%">';
	echo '<div style="padding: 5px;background: #fff;border: 1px solid #D1C8C8;border-radius: 10px;-moz-border-radius: 10px;-webkit-border-radius: 10px;width:300px;margin:0px">';
	echo "Nom: ".$_SESSION['name_changement_centreon']."<br>User: ".$_SESSION['user_changement_centreon']."<br>Groupe: ".$_SESSION['groupe_changement_centreon']." <br>Email: ".$_SESSION['email_changement_centreon']." <br> <a href='index_auth.php?etat=disconnect'>Se d&eacute;connecter</a>";
	echo "</div></div>";
}else 
{
	header('Location: index_auth.php');
}
echo '<div style="float: right; width: 48%">';
echo "<div style='text-align:right;background-color:#fff;width:100%;height:3px;'><a href='http://heb.tessi-techno.fr'><img border='0' style='width:30%' src='images/Tessi_Logo_Transparent.png'></a></div>";
echo "</div>";
echo "</div>";
echo "<div style='clear: both;'></div><br>";
