<?php
if (session_id()=='')
{
	session_start();
};

echo '<option value="' . htmlspecialchars($res_Loc['ID_Localisation']) . '">' . htmlspecialchars($res_Loc['Lieux']) . ' [' . htmlspecialchars($res_Loc['ID_Localisation']) . ']</option>';