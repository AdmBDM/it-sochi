<?php

function myDebug($arr, $die = false){
	echo '<pre>' . print_r($arr, true) . '</pre>';
	if ($die) die;
}

function cl_print_r ($var, $label = '') {
	$str = json_encode(print_r ($var, true));
	echo "<script>console.group('".$label."');console.log('".$str."');console.groupEnd();</script>";
}

function cl_var_dump ($var, $label = '') {
	ob_start();
	var_dump($var);
	$result = json_encode(ob_get_clean());
	echo "<script>console.group('".$label."');console.log('".$result."');console.groupEnd();</script>";
}

function my_usort($array, $args = ['id' => 'asc']) {
	usort( $array, function( $a, $b ) use ( $args ){
		$res = 0;

		$a = (object) $a;
		$b = (object) $b;

		foreach( $args as $k => $v ){
			if( $a->$k == $b->$k ) continue;

			$res = ( $a->$k < $b->$k ) ? -1 : 1;
			if( $v=='desc' ) $res= -$res;
			break;
		}

		return $res;
	} );

	return $array;
}

function clearPhone($mobile = '') {
	return str_replace(' ', '',
			str_replace('(', '',
				str_replace(')', '',
					str_replace('-', '', $mobile))));
}

return [];