<?php
////////////////////////////////////////////////
// Si el string es muy largo lo corta
////////////////////////////////////////////////

function cortar_palabra($texto,$largo) {
	$texto = preg_replace('/(\w{'.$largo.'})/i', '\\1 ', $texto);	
	return $texto;
}

function cortar_palabra2($texto,$largo){
        $texto = wordwrap($texto, $largo, "\n", true);
        return $texto;
}

?>