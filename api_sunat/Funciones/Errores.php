<?php 
function Error_Acceso()
{
    $lista =
    [
        'Rpta' => '1000 - El Acceso al API REST a fallado'
    ];

    $json_string = json_encode($lista);
    return $json_string;
}

function Error_Estado()
{
    $lista = 
    [
        'Rpta' => '1001 - El estado del usuario no está autorizado'
    ];

    $json_string = json_encode($lista);
    return $json_string;
}

function Error_Email()
{
    $lista=array('Rpta'=> '1002 - El Email no esta autorizado');
    $json_string = json_encode($lista);
    return $json_string;
}

function Error_Token()
{
    $lista=array('Rpta'=> '1003 - El Token no Esta Autorizado');
    $json_string = json_encode($lista);
    return $json_string;
}

function Error_Out()
{
    $lista = 
    [
        "status"    => false,
        "msg"       => '1004 - No se realizo la peticion, no devuelve Resultados'
    ];

    // Y esto cuando estaría mal
    //ycuando sale esto
    $json_string = json_encode($lista);
    return $json_string;
}
function Error_In()
{
    $lista  = 
    [
        "status"    => false,
        "msg"       => '1005 - No se registro la Peticion, no devuelve Resultados'
    ];

    $json_string = json_encode($lista);
    return $json_string;
}

// Success
function Resp()
{
    $lista = 
    [
        'Rpta' => '2000 - La Peticion se realizo con Exito'
    ];

    $json_string = json_encode($lista);
    return $json_string;
}

?>