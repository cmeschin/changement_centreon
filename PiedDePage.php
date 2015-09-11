<p>Tessi Technologies - Interface développée par C Meschin - <?php
// récupère la première ligne du fichier Version.txt
$o = fopen("Version.txt","r");
$l = fgets($o);
echo $l;
?></p>
