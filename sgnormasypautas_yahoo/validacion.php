<?php

// +--------------------------------------------------------------------------------+
// | INTRANET  TTI                                                                  |
// | PHP version 4.0                                                                |
// +--------------------------------------------------------------------------------+
// | Copyright (c) 2001 The TTI Group                                               |
// +--------------------------------------------------------------------------------+
// | Archivo: validacion.php3                                                       |
// | Funcion: Este programa esta para hacer validaciones                            |
// | Tablas que usa: ----                                                           |
// | Templates que usa: ----                                                        |
// | Flujo de invocaciones: ----                                                    |
// | Validaciones que realiza: ----                                                 |
// | Funciones externas usadas: ----                                                |
// | Funciones que define:                                                          |
// |  - is_digito()                                                                 |
// |        comprueba si $digito es un digito o no                                  |
// |  - is_vacio()                                                                  |
// |        chequea si es un string vacio                                           |
// |  - is_valid()                                                                  |
// |        chequea si es un string valido                                          |
// |  - is_alpha()                                                                  |
// |        is_alpha(string un_string, int min_long, int max_long)                  |
// |        chequea si un_string esta compuesto por caracteres alfabeticos unic.    |
// |        chequea si posee una longitud entre min_long y max_long                 |
// |  - is_numerico()                                                               |
// |        is_numerico(string un_string, int min_long, int max_long)               |
// |        chequea si un_string esta compuesto por caracteres numericos unicamente |
// |        chequea si posee una longitud entre min_long y max_long                 |
// |  - is_alphanumeric()                                                           |
// |        is_numerico(string un_string, int min_long, int max_long)               |
// |        chequea si un_string esta compuesto por caracteres alfa_numericos       |
// |        chequea si posee una longitud entre min_long y max_long                 |
// |  - is_email()                                                                  |
// |        comprueba que la entrada sea una direcci�n de e-mail valida             |
// |  - is_clean_text()                                                             |
// |        is_clean_text(string un_string, int min_long, int max_long)             |
// |        chequea si un_string esta compuesto por  una linea de texto limpio      |
// |        chequea si posee una longitud entre min_long y max_long                 |
// |  - contains_bad_words()                                                        |
// |        comprueba que la entrada no contenga alguna palabra no deseada          |
// |  - contains_phone_number()                                                     |
// |        comprueba que la entrada contenga alg�n numero telef�nico               |
// |                                                                                |
// +--------------------------------------------------------------------------------+
// | Autores: Diego Demartini <ddemarti@frsf.utn.edu.ar>                            |
// |          Dardo Guidobono <dguidobo@frsf.utn.edu.ar>                            |
// |          Anibal Alegre   <aalegre@frsf.utn.edu.ar>                             |
// +--------------------------------------------------------------------------------+
//
// Fecha=04-05-2001

function is_digito($digito)
{
    //       Comprueba si $digito es un d�gito o no
 if($digito=='0' || $digito=='1' || $digito=='2' || $digito=='3' || $digito=='4' ||
    $digito=='5' || $digito=='6' || $digito=='7' || $digito=='8' || $digito=='9'){return TRUE;
    }else{return FALSE;}
}

function is_vacio($string)
{
    //       Chequea si es un string vac�o
    $str = trim($string);
    if(empty($str))
    {
        return(false);
    }
    else return(1);
    }

function _is_valid($string, $min_length, $max_length, $regex)
{
    //       Chequea si es un string vac�o
    $str = trim($string);
    if(empty($str))
    {
        return(false);
    }

    //       Chequea si es un string con caracteres enteramente de tipos
    if(!ereg("^$regex$", $string))
    {
        return(false);
    }

    //      chequea por la entrada opcional de longitud
    $strlen = strlen($string);
    if(($min_length != 0 && $strlen < $min_length) || ($max_length != 0 && $strlen > $max_length))
    {
        return(false);
    }

    //      OK
    return(true);

}


function is_alpha($string, $min_length = 0, $max_length = 0)
//          is_alpha(string un_string, int min_long, int max_long)
//          Chequea si un_string esta compuesto por caracteres alfabeticos unicamente
//          chequea si posee una longitud entre min_long y max_long
{
    $ret = _is_valid($string, $min_length, $max_length, "[[:alpha:],áéíóú.������������������(){}=?��!-_/������������������������������������������[:space:]]$+");

    return($ret);
}

function is_numerico($string, $min_length = 0, $max_length = 0)
//          is_numerico(string un_string, int min_long, int max_long)
//          Chequea si un_string esta compuesto por caracteres numericos unicamente
//          chequea si posee una longitud entre min_long y max_long
{
    $ret = _is_valid($string, $min_length, $max_length, "[[:digit:]]+");

    return($ret);
}

function is_alphanumeric($string, $min_length = 0, $max_length = 0)
//          is_numerico(string un_string, int min_long, int max_long)
//          Chequea si un_string esta compuesto por caracteres alfa_numericos
//          chequea si posee una longitud entre min_long y max_long
{
    $ret = _is_valid($string, $min_length, $max_length, "[[:alnum:],áéíóú.������������������(){}=?��!-_/������������������������������������������[:space:]]+");

    return($ret);
}

function is_email($string)
//          Comprueba que la entrada sea una direcci�n de e-mail valida
{
    $string = trim($string);

    $ret = ereg(
                '^([a-z0-9_]|\\-|\\.)+'.
                '@'.
                '(([a-z0-9_]|\\-)+\\.)+'.
                '[a-z]{2,4}$',
                $string);

    return($ret);
}


function is_clean_text($string, $min_length = 0, $max_length = 0)
//          is_clean_text(string un_string, int min_long, int max_long)
//          chequea si un_string esta compuesto por  una linea de texto limpio
//          chequea si posee una longitud entre min_long y max_long
{
    $ret = _is_valid($string, $min_length, $max_length, "[a-zA-Záéíóú[:space:]�������������������������������������������������������������`�']+");

    return($ret);
}

function contains_bad_words($string)
//          comprueba que la entrada no contenga alguna palabra no deseada
{
    $bad_words = array(
                    'anal',           'ass',        'bastard',       'puta',
                    'bitch',          'blow',       'butt',          'trolo',
                    'cock',           'clit',       'cock',          'pija',
                    'cornh',          'cum',        'cunnil',        'verga',
                    'cunt',           'dago',       'defecat',       'cajeta',
                    'dick',           'dildo',      'douche',        'choto',
                    'erotic',         'fag',        'fart',          'trola',
                    'felch',          'fellat',     'fuck',          'puto',
                    'gay',            'genital',    'gosh',          'pajero',
                    'hate',           'homo',       'honkey',        'pajera',
                    'horny',          'vibrador',   'jew',           'lesbiana',
                    'jiz',            'kike',       'kill',          'eyaculacion',
                    'lesbian',        'masoc',      'masturba',      'anal',
                    'nazi',           'nigger',     'nude',          'mamada',
                    'nudity',         'oral',       'pecker',        'teta',
                    'penis',          'potty',      'pussy',         'culo',
                    'rape',           'rimjob',     'satan',         'mierda',
                    'screw',          'semen',      'sex',           'bastardo',
                    'shit',           'slut',       'snot',
                    'spew',           'suck',       'tit',
                    'twat',           'urinat',     'vagina',
                    'viag',           'vibrator',   'whore',
                    'xxx'
    );

    //      verifica
    for($i=0; $i<count($bad_words); $i++)
    {
        if(strstr(strtoupper($string), strtoupper($bad_words[$i])))
        {
            return(true);
        }
    }

    //      OK
    return(false);
}


function contains_phone_number($string)
//          comprueba que la entrada contenga alg�n numero telef�nico
{
     //     verifica
     if(ereg("[[:digit:]]{3,10}[\. /\)\(-]*[[:digit:]]{6,10}", $string))
     {
        return(true);
     }

     //     OK
     return(false);
}

/* Id: validacion.php3,v 1.3 2001/04/20 ALEGRE,DEMARTINI,GUIDOBONO The TTI Group */
?>
