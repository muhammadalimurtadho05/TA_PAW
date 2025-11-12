<?php
function validasi_jumlah(&$errors, $field_list, $field_name, $minimal,$msg)
{
	if(strlen($field_list[$field_name])<$minimal){
		$errors[$field_name] = $msg;
	}


	
}
function validate(&$errors, $field_list, $field_name,$pattern,$msg, $col)
{
	if (!isset($field_list[$field_name]) || empty($field_list[$field_name]))
		$errors[$field_name] = $col." Wajib Diisi";
	else if (!preg_match($pattern, $field_list[$field_name]))
		$errors[$field_name] = $msg;
}
?> 