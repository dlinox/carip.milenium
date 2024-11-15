<?php
    class firmadocpe
    {
        function FirmarCpe($Json_Fac, $Json_Emp)
        {
            // Leemos el json
            foreach ($Json_Emp as $cpe) 
            {
                $ruc_Emisor = $cpe['rucEmisor'];
                $nom_cert   = base64_decode($cpe['nomCertificado']);
                $clav_cert  = base64_decode($cpe['clavCertificado']);
            } 

            foreach ($Json_Fac as $cpe) 
            {
                $TipoComprobante    = $cpe['tipComp'];
                $serieComp          = $cpe['serieComp'];
                $numeroComp         = $cpe['numeroComp']; 
                $VersionUbl         = $cpe['UBL'];      
            }

            if ($VersionUbl=='2.0') 
            {
                $Pos    = 1;
                $Encode = "ISO-8859-1";
             
            }
            elseif ($VersionUbl=='2.1') 
            {
                $Pos    = 0;
                $Encode = "utf-8";
            }

            $documento_xml = $ruc_Emisor ."-". $TipoComprobante ."-". $serieComp ."-". $numeroComp;

            // Firmado del documento
            require_once('../xmlseclibs-master/xmlseclibs.php');
            $file               = '../Xml/xml-firmados/' . $documento_xml .'.xml';
            $xml_semilla        = '../Xml/xml-no-firmados/' . $documento_xml .'.xml';
            $ReferenceNodeName  = 'ExtensionContent';

            // Firmar Digitalmente XML de la semilla
            $doc                        = new DOMDocument('1.0', $Encode);
            $doc->formatOutput          = FALSE; 
            $doc->preserveWhiteSpace    = TRUE;
            $doc->load($xml_semilla);
            $objDSig                    = new XMLSecurityDSig(TRUE);
            $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
            $options['prefix']          = '';
            $options['prefix_ns']       = '';
            $options['force_uri']       = TRUE;
            $options['id_name']         = 'ID';
            $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), $options);
            $objKey                     = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
            $pfx                        = file_get_contents("../Certificado/" . $nom_cert);
            openssl_pkcs12_read($pfx, $key, $clav_cert);
            $objKey->loadKey($key["pkey"]);
            $objDSig->add509Cert($key["cert"]);
            $objDSig->sign($objKey, $doc->getElementsByTagName($ReferenceNodeName)->item($Pos));

            // Guardamos el Documento
            $doc->save($file); //$objDSig->sign($objKey, $doc->documentElement);
            

            $xml        = $file;
            $doc        = new DOMDocument();
            $doc->load($xml);

            // Modificar el Nodo ya creado solo para agregar la etiqueta
            $oldChild   = $doc->getElementsByTagName("Signature")->item(0);
            $oldChild->parentNode->replaceChild($oldChild, $oldChild);
            $oldChild->setAttribute("Id", "SignSUNAT");

            // Guardamos el Documento
            $doc->save($file);

            // Comprimimos el archivo
            if (file_exists($file)) 
            {
                $zip = new ZipArchive();
                $filename = '../Xml/xml-firmados/'.$documento_xml.'.zip';
             
                if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) 
                {
                    exit("cannot open <$filename>\n");
                }
             
                $zip->addFile('../Xml/xml-firmados/'. $documento_xml. '.xml', $documento_xml.".xml");
                $zip->close();
                $r[0] = 'Registrado';
             
            } 
            else 
            {
                $r[0] = 'Error';
            }

            return $r;
        }
    }