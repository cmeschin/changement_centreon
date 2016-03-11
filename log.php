<?php
function addLog($txt)
{
	file_put_contents("log/log_" . $_SESSION['user_changement_centreon'] . "_" . date("Y-m-j") . ".txt", date("[j/m/y H:i:s]")." - " . htmlspecialchars($txt) ." \r\n", FILE_APPEND);
};