<?php

function RespuestaServidor($Json_Emp, $Json_Fac, $Tipo, $MensajeBD)
{
    foreach ($Json_Emp as $cpe) { $ruc_Emisor = $cpe['rucEmisor']; }

    if ($Tipo == "Anulados") 
    {
        foreach ($Json_Fac as $cabezera) 
        {
            $TipoComprobante    = $cabezera['tipDocBaja'];
            $SerieComp          = $cabezera['serDocBaja'];
            $NumeroComp         = $cabezera['numDocBaja'];
            $FechaReferencia    = $cabezera['fecGeneracion'];
            $lote               = $cabezera['numLote'];
            $fecharesultado     = substr($FechaReferencia, 0, 4) . substr($FechaReferencia, 5, 2) . substr($FechaReferencia, 8, 2);
        }

        $documento_xml = $ruc_Emisor . '-RA-' . $fecharesultado . '-' . $lote;
        if (file_exists('../Xml/xml-firmados/' . $documento_xml . '.xml')) 
        {
            $xml = base64_encode(file_get_contents('../Xml/xml-firmados/' . $documento_xml . '.xml'));
        } 
        else 
        {
            $xml = base64_encode("Error no se firmo el Comprobante Electr贸nico");
        }

        if (file_exists('../Ticket/' . $documento_xml . '.xml')) 
        {
            $xmlrpta = base64_encode(file_get_contents('../Xml/xml-firmados/' . $documento_xml . '.xml'));
        } 
        else 
        {
            $xmlrpta = base64_encode("Error no se encontro respuesta de Sunat");
        }

        $lista = 
        [
            'status'            => true,
            'TipoComprobante'   => $TipoComprobante,
            'SerieComprobante'  => $SerieComp,
            'NumeroComprobante' => $NumeroComp,
            'Ticket'            => $MensajeBD,
            'xmlRpta_Base64'    => $xmlrpta,
            'Xml_Base64'        => $xml,
            'TipoEnvio'         => $Tipo
        ];
        
        $json_string = json_encode($lista, JSON_UNESCAPED_UNICODE);
    } 
    else 
    {
        foreach ($Json_Fac as $cpe) 
        {
            $TipoComprobante    = $cpe['tipComp'];
            $serieComp          = $cpe['serieComp'];
            $numeroComp         = $cpe['numeroComp'];
            $impresion          = $cpe["impresion"];
        }

        $documento_xml = $ruc_Emisor . "-" . $TipoComprobante . "-" . $serieComp . "-" . $numeroComp;
        if (file_exists('../Xml/xml-firmados/' . $documento_xml . '.xml')) 
        {
            $xml = base64_encode(file_get_contents('../Xml/xml-firmados/' . $documento_xml . '.xml'));
            $xmlHash = simplexml_load_file('../Xml/xml-firmados/' . $documento_xml . '.xml');
            /*C贸digo Hash*/
            foreach ($xmlHash->xpath('ext:UBLExtensions//ext:UBLExtension//ext:ExtensionContent//ds:SignatureValue') as $hash) {
            };
        } 
        else 
        {
            $xml = base64_encode("Error no se firmo el Comprobante Electr贸nico");
        }

        if (file_exists('../Cdr/R-' . $documento_xml . '.xml')) 
        {
            $cdr = base64_encode(file_get_contents('../Cdr/R-' . $documento_xml . '.xml'));
            $xmlCdr = simplexml_load_file('../Cdr/R-' . $documento_xml . '.xml');

            foreach($xmlCdr->xpath('cac:DocumentResponse//cac:Response//cbc:ResponseCode') as $c_resp)
            {
                $codigo_respuesta = $c_resp;
            }

            foreach($xmlCdr->xpath('cac:DocumentResponse//cac:Response//cbc:Description') as $d_resp)
            {
                $des_respuesta = $d_resp;
            }
        } else {
            $cdr = base64_encode("No se encuentra la Constancia Sunat del Comprobante Electr贸nico");
        }

        //Creamos el Json Respuesta:
        $lista =
            [
                'status'            => true,
                'TipoComprobante'   => $TipoComprobante,
                'SerieComprobante'  => $serieComp,
                'NumeroComprobante' => $numeroComp,
                'Observaciones'     => '',
                'BaseDatos'         => $MensajeBD,
                'Aceptada_Sunat'    => '',
                //'Xml_Base64'      => $xml,
                //'Cdr_Base64'      => $cdr,
                //'Codigo_Hash'     => $hash,
                'TipoEnvio'         => $Tipo,
                'codigo_respuesta'  => $codigo_respuesta,
                'des_respuesta'     => $des_respuesta
            ];

        $json_string = json_encode($lista, JSON_UNESCAPED_UNICODE);
    }

    return $json_string;
}
