<?php

function NumeroCeros($num) 
{
 $texto_num='';
$largo= strlen($num);
switch ($largo) {
    case 1:
     $texto_num='0000000'.$num;
    break;

    case 2:
        $texto_num='000000'.$num;
    break;

    case 3:
        $texto_num='00000'.$num;
    break;

    case 4:
        $texto_num='0000'.$num;
    break;

    case 5:
        $texto_num='000'.$num;
    reak;

    case 6:
        $texto_num='00'.$num;
    break;

    case 7:
        $texto_num='0'.$num;
    break;

    case 8:
        $texto_num= $num;
    break;
    

}


    return $texto_num; 
}

function fechaconvertir($fecha){
    $date = trim($fecha);
    $year=substr($date,0,4);
    $month=substr($date,5,2);
    $day=substr($date,8,2);
    $conversionfecha=$day.'/'.$month.'/'.$year;
    return $conversionfecha;
  }

?>