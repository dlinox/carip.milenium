<?php

function NumerosALetras($monto) 
{
    $maximo = pow(10,9);
    $unidad            = array(1=>"UNO", 2=>"DOS", 3=>"TRES", 4=>"CUATRO", 5=>"CINCO", 6=>"SEIS", 7=>"SIETE", 8=>"OCHO", 9=>"NUEVE");
    $decena            = array(10=>"DIEZ", 11=>"ONCE", 12=>"DOCE", 13=>"TRECE", 14=>"CATORCE", 15=>"QUINCE", 20=>"VEINTE", 30=>"TREINTA", 40=>"CUARENTA", 50=>"CINCUENTA", 60=>"SESENTA", 70=>"SETENTA", 80=>"OCHENTA", 90=>"NOVENTA");
    $prefijo_decena    = array(10=>"DIECI", 20=>"VEINTI", 30=>"TREINTA Y ", 40=>"CUARENTA Y ", 50=>"CINCUENTA Y ", 60=>"SESENTA Y ", 70=>"SETENTA Y ", 80=>"OCHENTA Y ", 90=>"NOVENTA Y ");
    $centena           = array(100=>"CIEN", 200=>"DOSCIENTOS", 300=>"TRESCIENTOS", 400=>"CUANTROCIENTOS", 500=>"QUINIENTOS", 600=>"SEISCIENTOS", 700=>"SETECIENTOS", 800=>"OCHOCIENTOS", 900=>"NOVECIENTOS");   
    $prefijo_centena   = array(100=>"CIENTO ", 200=>"DOSCIENTOS ", 300=>"TRESCIENTOS ", 400=>"CUANTROCIENTOS ", 500=>"QUINIENTOS ", 600=>"SEISCIENTOS ", 700=>"SETECIENTOS ", 800=>"OCHOCIENTOS ", 900=>"NOVECIENTOS ");
    $sufijo_miles      = "MIL";
    $sufijo_millon     = "UN MILLON";
    $sufijo_millones   = "MILLONES";
    
    //echo var_dump($monto); die;
    
    $base         = strlen(strval($monto));
    $pren         = intval(floor($monto/pow(10,$base-1)));
    $prencentena  = intval(floor($monto/pow(10,3)));
    $prenmillar   = intval(floor($monto/pow(10,6)));
    $resto        = $monto%pow(10,$base-1);
    $restocentena = $monto%pow(10,3);
    $restomillar  = $monto%pow(10,6);
    
    if (!$monto) return "";
    
    if (is_int($monto) && $monto>0 && $monto < abs($maximo)) 
    {            
        switch ($base) {
            case 1: return $unidad[$monto]; 
            case 2: return array_key_exists($monto, $decena)  ? $decena[$monto]  : $prefijo_decena[$pren*10]   . NumerosALetras($resto);
            case 3: return array_key_exists($monto, $centena) ? $centena[$monto] : $prefijo_centena[$pren*100] . NumerosALetras($resto);
            case 4: case 5: case 6: return ($prencentena>1) ? NumerosALetras($prencentena). " ". $sufijo_miles . " " . NumerosALetras($restocentena) : $sufijo_miles. " " . NumerosALetras($restocentena);
            case 7: case 8: case 9: return ($prenmillar>1)  ? NumerosALetras($prenmillar). " ". $sufijo_millones . " " . NumerosALetras($restomillar)  : $sufijo_millon. " " . NumerosALetras($restomillar);
        }
    } else {
        echo "ERROR con el numero - $monto<br/> Debe ser un numero entero menor que " . number_format($maximo, 0, ".", ",") . ".";
    }
    
    //return $texto;
    
}

function MontoMonetarioEnLetras($monto,$moneda) 
{

    $monto = str_replace(',','',$monto); //ELIMINA LA COMA

    $pos = strpos($monto, '.');
        
    if ($pos == false)  {
        $monto_entero = $monto;
        $monto_decimal = '00';
    }else{
        $monto_entero = substr($monto,0,$pos);
        $monto_decimal = substr($monto,$pos,strlen($monto)-$pos);
        $monto_decimal = $monto_decimal * 100;
    }

    $monto = (int)($monto_entero);
    if ($moneda=='PEN') {
        $texto_con = " CON $monto_decimal/100 SOLES";
    }else if($moneda=='USD') {
        $texto_con = " CON $monto_decimal/100 DOLARES AMERICANOS";
    }

    //echo NumerosALetras($monto).$texto_con; 
 return NumerosALetras($monto).$texto_con; 
}
/*
echo "1,850.50";
echo " --> ";
echo MontoMonetarioEnLetras("1,850.50");
echo "<br>";
echo "19,251.36";
echo " --> ";
echo MontoMonetarioEnLetras("19,251.36");
echo "<br>";
echo "525.10";
echo " --> ";
echo MontoMonetarioEnLetras("525.10");
*/

?>