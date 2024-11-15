<?php
class enviocpe
{
    function EnviarCpe($Json_Fac,$Json_Emp)
    {
        require("Soap.php");
        // Colocamos el nombre del archivo generado; lo recogemos por POST
        foreach ($Json_Emp as $cpe) 
        {
            $ruc_Emisor = $cpe['rucEmisor'];
            $usersol    = base64_decode($cpe['userSol']);
            $clavesol   = base64_decode($cpe['claveSol']);
            $servidor   = $cpe['servidorSunat']; 
        }

        foreach ($Json_Fac as $cpe) 
        {
            $TipoComprobante    = $cpe['tipComp'];
            $serieComp          = $cpe['serieComp'];
            $numeroComp         = $cpe['numeroComp'];      
        } 

        $Nombre_Archivo         = $ruc_Emisor . "-" . $TipoComprobante ."-". $serieComp ."-". $numeroComp;
        $ruta_cdr               = '../Cdr/';
        $ruta_zip               = '../Xml/xml-firmados/';

        function soapCall($wsdlURL, $callFunction = "", $XMLString)
        {
            $client = new feedSoap($wsdlURL, array('trace' => true));
            $reply  = $client->SoapClientCall($XMLString);
            //echo "REQUEST:\n" . $client->__getFunctions() . "\n";
            $client->__call("$callFunction", array(), array());
            //$request = prettyXml($client->__getLastRequest());
            //echo highlight_string($request, true) . "<br/>\n";
            return $client->__getLastResponse();
        }

        //Estructura del XML para la conexión
        $XMLString = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken Id="ABC-123">
                    <wsse:Username>'. $ruc_Emisor . $usersol .'</wsse:Username>
                    <wsse:Password>'. $clavesol .'</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendBill>
                <fileName>'.$Nombre_Archivo.'.zip</fileName>
                <contentFile>' . base64_encode(file_get_contents($ruta_zip.$Nombre_Archivo.'.zip')) . '</contentFile>
            </ser:sendBill>
        </soapenv:Body>
        </soapenv:Envelope>';

        //URL para enviar las solicitudes a SUNAT
        switch ($servidor) 
        {
            case '1': // Produccion
                $wsdlURL = 'https://facturacion.mytems.cloud/Envio/SunatProd.wsdl';
                break;
            case '2': // Homologacion
                $wsdlURL = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService?wsdl';
                break;
            case '3': // Beta
            // $wsdlURL = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl';
                $wsdlURL = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl';
                break;
        }

        // Realizamos la llamada a nuestra función
        
        try
        {
            $result         = soapCall($wsdlURL, $callFunction = "sendBill", $XMLString);
            //Descargamos el Archivo Response
            $archivo        = fopen('C'. $Nombre_Archivo .'.xml','w+');
            fputs($archivo,$result);
            fclose($archivo);

            // Leemos el archivo XML
            $xml            = simplexml_load_file('C'.$Nombre_Archivo.'.xml'); 
            foreach ($xml->xpath('//applicationResponse') as $response){ }

            // Descargamos el CDR (Constancia de recepción)
            $cdr            = base64_decode($response);
            $archivo        = fopen('R'. $Nombre_Archivo .'.zip','w+');
            fputs($archivo,$cdr);
            fclose($archivo);

            $enlace         = 'R'. $Nombre_Archivo .'.zip';
            $nuevo_fichero  = $ruta_cdr.$enlace;

            if (!copy($enlace, $nuevo_fichero)) {}

            // Eliminamos el archivo Response
            unlink($enlace);
            unlink('C'.$Nombre_Archivo.'.xml');

            if (file_exists($nuevo_fichero)) 
            {
                //Extraemos el CDR ZIPEADO
                $zip = new ZipArchive;
                if ($zip->open($nuevo_fichero) === TRUE) 
                {
                    $zip->extractTo($ruta_cdr);
                    $zip->close();
                    $r[0] = 'Registrado';
            
                }
                else
                {
                    $r[0] = 'Error';
                }   
            } 
            else 
            {
                $r[0] = 'Error';
            }
        }

        catch(Exception $error)
        {
            $r[0]   = 'Error';
        }

        return $r;
    }
}