<?php
// récupère la première ligne du fichier Version.txt
$line = fopen("Version.txt","r");
$version = fgets($line);
echo '<p>Tessi Technologies - Interface développée par C Meschin - ' . $version . '</p>';
