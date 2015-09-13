<?php
if (session_id()=='')
{
	session_start();
};

echo '<option value="' . htmlspecialchars($res_type['Type_Hote']) . '">' . htmlspecialchars($res_type['Type_Description']) . ' [' . htmlspecialchars($res_type['Type_Hote']) . ']</option>';