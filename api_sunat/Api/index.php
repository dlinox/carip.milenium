<?php

require '../Xml/facturacion_2_1.php';
require '../Xml/notacredito_2_1.php';
require '../Xml/notadebito_2_1.php';
require '../Funciones/Errores.php';
require '../Funciones/RespuestaServidor.php';
require '../Firma/firmar-xml.php';
require '../Envio/sunat_conexion.php';
header('Access-Control-Allow-Origin: *');


function FacturacionUBL($Json_In)
{
    $Json_Fac   = json_decode("[" . $Json_In . "]", JSON_UNESCAPED_UNICODE);
    $ruc        = '20610316884';
    $send_sunat = 'Off';

    foreach ($Json_Fac as $cpe) 
    {
        $TipoComprobante    = $cpe['tipComp'];
        $serieComp          = $cpe['serieComp'];
        $numeroComp         = $cpe['numeroComp'];
        $EnvioSunat         = $cpe['envioSunat'];
        $VersionUbl         = $cpe['UBL'];
    }

    // Validamos el envio del json con el envio de la lista
    if ($send_sunat == 'On') 
    {
        $EnvioSunat = true;
    }

    // Guardamos el Json en el directorio Local
    $json_string            = $Json_In;
    $cpe_                   = $ruc . '-' . $TipoComprobante . '-' . $serieComp . '-' . $numeroComp;
    $file                   = '../Json/' . $cpe_ . '.json';
    file_put_contents($file, $json_string);

    /*Informaci贸n de la Empresa*/
    $data       = file_get_contents("../Json/" . $ruc . ".json");
    $Json_Emp   = json_decode($data, JSON_UNESCAPED_UNICODE);

    /**
     * Generamos el XML, firma y env铆o
    */
    if ($TipoComprobante == "01" || $TipoComprobante == "03") // Factura o boleta
    {
        $instancia  = new cpeFacturacionUBL2_1();
        $array      = $instancia->Registrar_FacturacionUBL2_1($Json_Fac, $Json_Emp); // Genera el XML
        if ($array[0] == 'Registrado') {
            $firmado    = new firmadocpe();
            $arrayFirma = $firmado->FirmarCpe($Json_Fac, $Json_Emp); // Firmar el XML
            if ($arrayFirma[0] == 'Registrado') {
                if ($EnvioSunat == true) {
                    $enviado        = new enviocpe();
                    $arrayEnviar    = $enviado->EnviarCpe($Json_Fac, $Json_Emp); // Enviar xml a sunat

                    if ($arrayEnviar[0] == 'Error') 
                    {
                        $error_out[0]      = Error_Out();
                        echo json_encode($error_out);
                    } 
                    else 
                    {
                        $respuesta_servidor[0] = RespuestaServidor($Json_Emp, $Json_Fac, "Enviado", "Registrado");
                        echo json_encode($respuesta_servidor);
                    }
                } 
                else 
                {
                    $respuesta_servidor[0] = RespuestaServidor($Json_Emp, $Json_Fac, "Firmado", "Registrado");
                    echo json_encode($respuesta_servidor);
                }
            } else {
                $error_in[0]   = Error_In();
                echo json_encode($error_in);
            }
        } else 
        {
            $error_in[0]   = Error_In();
            echo json_encode($error_in);
        }
    } elseif ($TipoComprobante == "07") // Nota de cr茅dito
    {
        $instancia  = new cpenotacredito();
        $array      = $instancia->NotaCredito($Json_Fac, $Json_Emp); // General el XML
        if ($array[0] == 'Registrado') {
            $firmado = new firmadocpe();
            $arrayFirma = $firmado->FirmarCpe($Json_Fac, $Json_Emp); // Firmar el XML
            if ($arrayFirma[0] == 'Registrado') 
            {
                if ($EnvioSunat == true) 
                {
                    $enviado        = new enviocpe();
                    $arrayEnviar    = $enviado->EnviarCpe($Json_Fac, $Json_Emp); // Enviar xml a sunat

                    if ($arrayEnviar[0] == 'Error') 
                    {
                        $error_out[0]      = Error_Out();
                        echo json_encode($error_out);
                    } 
                    else 
                    {
                        $respuesta_servidor[0] = RespuestaServidor($Json_Emp, $Json_Fac, "Enviado", "Registrado");
                        echo json_encode($respuesta_servidor);
                    }
                } 
                else 
                {
                    $respuesta_servidor[0] = RespuestaServidor($Json_Emp, $Json_Fac, "Firmado", "Registrado");
                    echo json_encode($respuesta_servidor);
                }
            } 
            else 
            {
                $error_in[0]   = Error_In();
                echo json_encode($error_in);
            }
        }
    } 
     
    else 
    {
        echo json_encode(['status' => false, 'msg' => 'No se registr贸']);
    }
}

$datosJSON              = $_POST['datosJSON'];
FacturacionUBL($datosJSON);