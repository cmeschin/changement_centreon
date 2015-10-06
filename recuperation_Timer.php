<?php
if (session_id()=='')
{
	session_start();
}; 
include ('connexion_sql_supervision.php');
$Timer=$_SESSION['Timer'];
$date=date_create();
$maintenant=date_timestamp_get($date);
$Delai_Timer=abs($maintenant)-abs($Timer);
 
echo $Delai_Timer;