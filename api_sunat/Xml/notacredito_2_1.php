<?php
require_once '../Funciones/nletras.php';

class cpenotacredito
{
    function NotaCredito($Json_Fac, $Json_Emp)
    {
        // Información de la empresa
        foreach ($Json_Emp as $cabezera) {
            $RucEmpresa             = $cabezera['rucEmisor'];
            $NameEmpresa            = $cabezera['razEmisor'];
            $DireccionEmpresa       = $cabezera['direccionEmisor'];
            $PaisEmpresa            = $cabezera['paisEmisor'];
            $UbigeoEmpresa          = $cabezera['ubigeoEmisor'];
            $DepartamentoEmpresa    = $cabezera['depEmisor'];
            $ProvinciaEmpresa       = $cabezera['provEmisor'];
            $DistritoEmpresa        = $cabezera['distEmisor'];
            $ComercialEmpresa       = $cabezera['comercialEmisor'];
            $TipoEmpresa            = "6";
            $UrbanizacionEmpresa    = $cabezera['urbEmisor'];
            $LocalEmpresa           = $cabezera['localEmisor'];
        }

        // Cabecera del archivo json
        // Lectura del archivo json
        $ArrayItem                  = array();
        $ArrayTipoUnidad            = array();
        $ArrayDetalleBienServicio   = array();
        $ArrayCodigoBienServicio    = array();
        $ArrayCantidad              = array();
        $ArrayValorVenta            = array();
        $ArrayPrecioUnitario        = array();
        $ArrayValorUnitario         = array();
        $ArrayPrecioVenta           = array();
        $ArrayTipoPrecioVenta       = array();
        $ArrayIndicadorDscto        = array();
        $ArrayDescuento             = array();
        $ArrayAfectacionIgv         = array();
        $ArrayIgv                   = array();
        $ArrayCat05                 = array();
        $ArrayName05                = array();
        $ArrayTaxTypeCode05         = array();
        $ArrayCategoryCode05        = array();
        $ArrayValorTotal            = array();
        $ArrayPorcentaje            = array();

        // Sumatorias
        $TotalValorVta              = 0;
        $TotalPrecioVta             = 0;
        $TotalDescuentos            = 0;
        $TotalOtrosCargos           = 0;
        $TotalAnticipos             = 0;
        $ImporteTotalVta            = 0;

        $item                       = 1;
        $igv                        = 0;

        foreach ($Json_Fac as $cabezera) 
        {
            $TipoOperacion          = $cabezera['tipOperacion'];
            $FechaComprobante       = $cabezera['fecEmision'];
            $FechaVencimiento       = $cabezera['fecVencimiento'];
            $TipoComprobante        = $cabezera['tipComp'];
            $serieComp              = $cabezera['serieComp'];
            $numeroComp             = $cabezera['numeroComp'];
            $NumComprobante         = $serieComp . "-" . $numeroComp;
            $DomicilioFiscalEmisor  = $cabezera['codLocalEmisor'];
            $TipoCliente            = $cabezera['tipDocUsuario'];
            $RucCliente             = $cabezera['numDocUsuario'];
            $NameCliente            = $cabezera['rznSocialUsuario'];
            $TipoMoneda             = $cabezera['tipMoneda'];
            $TipoCambio             = $cabezera['tipCambio'];
            $Descuentosglobales     = $cabezera['DsctoGlobal'];
            $SumatoriaOtrosCargos   = $cabezera['otrosCargos'];
            $OperacionesGravadas    = $cabezera['Gravada'];
            $OperacionesInafectas   = $cabezera['Inafecta'];
            $OperacionesExoneradas  = $cabezera['Exonerada'];
            $OperacionesBase        = $OperacionesGravadas + $OperacionesInafectas + $OperacionesExoneradas;
            $OperacionesGratuitas   = $cabezera['Gratuita'];
            $OperacionesAnticipo    = $cabezera['Anticipo'];
            $SumatoriaIgv           = $cabezera['mtoIgv'];
            $ImporteTotal           = $cabezera['mtoTotal'];

            $codefechavence         = "-";
            $UbigeoCliente          = $cabezera['codUbigeoCliente'];
            $DepartamentoCliente    = $cabezera['deptCliente'];
            $ProvinciaCliente       = $cabezera['provCliente'];
            $DistritoCliente        = $cabezera['distCliente'];
            $PaisCliente            = $cabezera['codPaisCliente'];
            $DireccionCliente       = $cabezera['desDireccionCliente'];
            $UrbanizacionCliente    = '-';
            $resultado              = substr($cabezera['docRef'], 15, 13);
            $Refenciacomprobante    = $resultado;
            $Codcatalogo09          = $cabezera['Cat09'];
            $DetalleNota            = $cabezera['detCat'];
            $codRefenciacomprobante = substr($cabezera['docRef'], 12, 2);

            foreach ($cabezera['items'] as $detalle) 
            {
                // echo $detalle["ctdUnidadItem"];
                $ArrayItem[]                = $item;
                $ArrayTipoUnidad[]          = $detalle['codUnidadMedida'];
                $ArrayDetalleBienServicio[] = utf8_decode($detalle['desItem']);
                $ArrayCodigoBienServicio[]  = $detalle['codProducto'];
                $ArrayCantidad[]            = $detalle['ctdUnidadItem'];
                $ArrayValorVenta[]          = $detalle['mtoValorUnitario'];
                $ArrayValorUnitario[]       = $detalle['mtoValorUnitario'] / $detalle['ctdUnidadItem']; // Valor unitario

                $ArrayPrecioVenta[]         = $detalle['mtoPrecioVentaItem'];
                $ArrayPrecioUnitario[]      = $detalle['mtoPrecioVentaItem'] / $detalle['ctdUnidadItem']; // Precio unitario

                $ArrayTipoPrecioVenta[]     = $detalle['tipPrecio']; // Catalogo N°16
                $ArrayIndicadorDscto[]      = 'false';
                $ArrayDescuento[]           = $detalle['mtoDsctoItem']; // Descuento por ítem
                $ArrayIgv[]                 = $detalle['mtoIgvItem']; // Igv por item

                $ArrayAfectacionIgv[]       = $detalle['tipAfeIGV']; // Catalogo N° 07

                if ($detalle['tipAfeIGV'] == '10') 
                {
                    $tributocat5 = '1000';
                } 
                
                else if ($detalle['tipAfeIGV'] == '10' ||  $detalle['tipAfeIGV'] == '11' ||  
                        $detalle['tipAfeIGV'] == '12' ||  $detalle['tipAfeIGV'] == '13' ||  
                        $detalle['tipAfeIGV'] == '14' ||  $detalle['tipAfeIGV'] == '15' ||  
                        $detalle['tipAfeIGV'] == '16' ||  $detalle['tipAfeIGV'] == '17') 
                {
                    $tributocat5 = '9996';
                } 
                
                else if ($detalle['tipAfeIGV'] == '20') 
                {
                    $tributocat5 = '9997';
                } 
                
                else if ($detalle['tipAfeIGV'] == '21') {
                    $tributocat5 = '9996';
                } 
                
                else if ($detalle['tipAfeIGV'] == '30') {
                    $tributocat5 = '9998';
                } 
                
                else if ($detalle['tipAfeIGV'] == '32' || $detalle['tipAfeIGV'] == '33' || 
                        $detalle['tipAfeIGV'] == '34' || $detalle['tipAfeIGV'] == '35' || 
                        $detalle['tipAfeIGV'] == '36') 
                {
                    $tributocat5 = '9996';
                } 
                
                else if ($detalle['tipAfeIGV'] == '40') 
                {
                    $tributocat5 = '9995';
                }

                if ($tributocat5 == '1000') 
                {
                    $ArrayCat05[]           = '1000';
                    $ArrayName05[]          = 'IGV'; // Catalogo N° 05
                    $ArrayTaxTypeCode05[]   = 'VAT'; // Catalogo N° 05
                    $ArrayCategoryCode05[]  = 'S';
                } 
                
                else if ($tributocat5 == '9995') 
                {
                    $ArrayCat05[]           = '9995';
                    $ArrayName05[]          = 'EXP';  // Catalogo N° 05
                    $ArrayTaxTypeCode05[]   = 'FRE';
                    $ArrayCategoryCode05[]  = 'G';
                } 
                
                else if ($tributocat5 == '9996') 
                {
                    $ArrayCat05[]           = '9996';
                    $ArrayName05[]          = 'GRAT';  // Catalogo N° 05
                    $ArrayTaxTypeCode05[]   = 'FRE';  // Catalogo N° 05
                    $ArrayCategoryCode05[]  = 'Z';
                } 
                
                else if ($tributocat5 == '9997') 
                {
                    $ArrayCat05[]           = '9997';
                    $ArrayName05[]          = 'EXO';  // Catalogo N° 05
                    $ArrayTaxTypeCode05[]   = 'VAT';  // Catalogo N° 05
                    $ArrayCategoryCode05[]  = 'E';
                } 
                
                else if ($tributocat5 == '9998') 
                {
                    $ArrayCat05[]           = '9998';
                    $ArrayName05[]          = 'INA';  // Catalogo N° 05
                    $ArrayTaxTypeCode05[]   = 'FRE';  // Catalogo N° 05
                    $ArrayCategoryCode05[]  = 'O';
                }


                $ArrayValorTotal[]          = $detalle['mtoValorVentaItem'];
                $ArrayPorcentaje[]          = $detalle['porcentajeIgv'];

                $TotalValorVta              = $TotalValorVta + $detalle['mtoValorVentaItem'];
                $igv                        = $igv + $detalle['mtoIgvItem'];
                $TotalPrecioVta             = $TotalPrecioVta + $detalle['mtoPrecioVentaItem'];

                $ImporteTotalVta            = $ImporteTotalVta + $detalle['mtoPrecioVentaItem'];
                $item                       = $item + 1;
            }
        }

        $Arraymonetary                      = ["1001","1002","1003","1004"];
        $ArrayTotalesmonetary               = 
        [
            $OperacionesGravadas,$OperacionesInafectas,$OperacionesExoneradas,$OperacionesGratuitas
        ];

        // Importes detalle
        $TotalesIGV                         = number_format($igv,2);

        $AllowanceTotal                     = "0.00"; // 50.- Descuentos globales
        $ChargeTotal                        = "0.00"; // 25. Sumatoria otros cargos
        $PayableTotal                       = $ImporteTotal; // 27. Importe total de la venta, de la cesión en uso o del servicio prestado

        $InfSistema                         = "ELABORADO POR MYTEMS E.I.R.L.";
        $HoraComprobante                    = "01:00:00";


        $CodigoCat15                        = "1000";
        $DetalleCat15                       = MontoMonetarioEnLetras($ImporteTotalVta, $TipoMoneda);

        /**
		 * XML UBL 2.1
		*/

        $xml                        = new DomDocument('1.0', 'utf-8');
        $xml->standalone            = false;
        $Invoice                    = $xml->createElement('CreditNote');
        $Invoice                    = $xml->appendChild($Invoice);

        // Establecer los atributos
        $Invoice->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2');
        $Invoice->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $Invoice->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $Invoice->setAttribute('xmlns:ccts',"urn:un:unece:uncefact:documentation:2"); 
        $Invoice->setAttribute('xmlns:ds',"http://www.w3.org/2000/09/xmldsig#"); 
        $Invoice->setAttribute('xmlns:ext',"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"); 
        $Invoice->setAttribute('xmlns:qdt',"urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"); 
        $Invoice->setAttribute('xmlns:sac',"urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"); 
        $Invoice->setAttribute('xmlns:udt',"urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2");
        $Invoice->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");

        $UBLExtension               = $xml->createElement('ext:UBLExtensions'); 
        $UBLExtension               = $Invoice ->appendChild($UBLExtension);

        //UBL firma
        $extFirma                   = $xml->createElement('ext:UBLExtension'); 
        $extFirma                   = $UBLExtension ->appendChild($extFirma);
        $contentsFirma              = $xml->createElement('ext:ExtensionContent'); 
        $contentsFirma              = $extFirma->appendChild($contentsFirma);

        // Informacion del comprobante y version
        $UBLVersion                 = $xml->createElement('cbc:UBLVersionID'); 
        $UBLVersion                 = $Invoice ->appendChild($UBLVersion);
        $value                      = $xml->createTextNode('2.1');
        $value                      = $UBLVersion->appendChild($value);

        $CBCCustomization           = $xml->createElement('cbc:CustomizationID'); 
        $CBCCustomization           = $Invoice ->appendChild($CBCCustomization); 
        $value                      = $xml->createTextNode('2.0');
        $value                      = $CBCCustomization->appendChild($value);

        $CBCid                      = $xml->createElement('cbc:ID'); 
        $CBCid                      = $Invoice ->appendChild($CBCid); 
        $value                      = $xml->createTextNode($NumComprobante);
        $value                      = $CBCid->appendChild($value);

        $CBCIssueDate               = $xml->createElement('cbc:IssueDate'); 
        $CBCIssueDate               = $Invoice ->appendChild($CBCIssueDate);
        $value                      = $xml->createTextNode($FechaComprobante);
        $value                      = $CBCIssueDate->appendChild($value);

        // Hora de envío
        $CBCIssueTime               = $xml->createElement('cbc:IssueTime'); 
        $CBCIssueTime               = $Invoice ->appendChild($CBCIssueTime);
        $value                      = $xml->createTextNode($HoraComprobante);
        $value                      = $CBCIssueTime->appendChild($value); 

        // Nota para el Catalogo 15 
        $cbcNote                    = $xml->createElement('cbc:Note'); 
        $cbcNote                    = $Invoice->appendChild($cbcNote);
        $value                      = $xml->createTextNode($DetalleCat15);
        $value                      = $cbcNote->appendChild($value); 
        $cbcNote->setAttribute('languageLocaleID',$CodigoCat15);

        $CBCDocumentCurrencyCode    = $xml->createElement('cbc:DocumentCurrencyCode'); 
        $CBCDocumentCurrencyCode    = $Invoice ->appendChild($CBCDocumentCurrencyCode); 
        $value                      = $xml->createTextNode($TipoMoneda);
        $value                      = $CBCDocumentCurrencyCode->appendChild($value);
        $CBCDocumentCurrencyCode->setAttribute('listAgencyName','United Nations Economic Commission for Europe');
        $CBCDocumentCurrencyCode->setAttribute('listID',"ISO 4217 Alpha");
        $CBCDocumentCurrencyCode->setAttribute('listName','Currency');

        // Información de la nota de crédito
        $CABDiscrepancyResponse     = $xml->createElement('cac:DiscrepancyResponse'); 
        $CABDiscrepancyResponse     = $Invoice ->appendChild($CABDiscrepancyResponse);

        $CBCReferenceID             = $xml->createElement('cbc:ReferenceID'); 
        $CBCReferenceID             = $CABDiscrepancyResponse ->appendChild($CBCReferenceID); 
        $value                      = $xml->createTextNode($Refenciacomprobante);
        $value                      = $CBCReferenceID->appendChild($value);

        $CBCResponseCode            = $xml->createElement('cbc:ResponseCode'); 
        $CBCResponseCode            = $CABDiscrepancyResponse ->appendChild($CBCResponseCode); 
        $value                      = $xml->createTextNode($Codcatalogo09);
        $value                      = $CBCResponseCode->appendChild($value);

        $CBCDescriptionNota         = $xml->createElement('cbc:Description'); 
        $CBCDescriptionNota         = $CABDiscrepancyResponse ->appendChild($CBCDescriptionNota); 
        $value                      = $xml->createTextNode($DetalleNota);
        $value                      = $CBCDescriptionNota->appendChild($value);

        // 31. Serie y número del documento que modifica
        $CABBillingReference        = $xml->createElement('cac:BillingReference'); 
        $CABBillingReference        = $Invoice ->appendChild($CABBillingReference);

        $CABInvoiceDocumentReference = $xml->createElement('cac:InvoiceDocumentReference'); 
        $CABInvoiceDocumentReference = $CABBillingReference ->appendChild($CABInvoiceDocumentReference); 

        $CBCID                      = $xml->createElement('cbc:ID'); 
        $CBCID                      = $CABInvoiceDocumentReference ->appendChild($CBCID); 
        $value                      = $xml->createTextNode($Refenciacomprobante);
        $value                      = $CBCID->appendChild($value);

        $CBCDocumentTypeCode        = $xml->createElement('cbc:DocumentTypeCode'); 
        $CBCDocumentTypeCode        = $CABInvoiceDocumentReference ->appendChild($CBCDocumentTypeCode); 
        $value                      = $xml->createTextNode($codRefenciacomprobante);
        $value                      = $CBCDocumentTypeCode->appendChild($value);

        // Tipo y número de otro documento y código relacionado con la operacion
        /**if ($AdditionalDocumentReference == true) 
        {
            $CABAdditionalDocumentReference=$xml->createElement('cac:AdditionalDocumentReference'); 
            $CABAdditionalDocumentReference =$Invoice ->appendChild($CABAdditionalDocumentReference);
            
            $CBCID=$xml->createElement('cbc:ID'); 
            $CBCID=$CABAdditionalDocumentReference ->appendChild($CBCID); 
            $value = $xml->createTextNode($numCompRelacionado);
            $value = $CBCID->appendChild($value);
            
            $CBCDocumentTypeCode=$xml->createElement('cbc:DocumentTypeCode'); 
            $CBCDocumentTypeCode=$CABAdditionalDocumentReference ->appendChild($CBCDocumentTypeCode); 
            $value = $xml->createTextNode($addCompRelacionado);
            $value = $CBCDocumentTypeCode->appendChild($value);
        } */

        // Información de la empresa emisora -  (Signature)
        $CACSignature               = $xml->createElement('cac:Signature'); 
        $CACSignature               = $Invoice ->appendChild($CACSignature);

        $IdEmpresa                  = $xml->createElement('cbc:ID'); 
        $IdEmpresa                  = $CACSignature->appendChild($IdEmpresa);
        $value                      = $xml->createTextNode($RucEmpresa);
        $value                      = $IdEmpresa->appendChild($value); 

        // Nota de la empresa que genera el XML 
        $cbcNote                    = $xml->createElement('cbc:Note'); 
        $cbcNote                    = $CACSignature->appendChild($cbcNote);
        $value                      = $xml->createTextNode($InfSistema);
        $value                      = $cbcNote->appendChild($value);

        $CACSignatoryParty          = $xml->createElement('cac:SignatoryParty'); 
        $CACSignatoryParty          = $CACSignature->appendChild($CACSignatoryParty);

        // Nombre comercial
        $CACPartyIdentification     = $xml->createElement('cac:PartyIdentification'); 
        $CACPartyIdentification     = $CACSignatoryParty->appendChild($CACPartyIdentification);
        $PartyEmpresa               = $xml->createElement('cbc:ID'); 
        $PartyEmpresa               = $CACPartyIdentification->appendChild($PartyEmpresa);
        $value                      = $xml->createTextNode($RucEmpresa);
        $value                      = $PartyEmpresa->appendChild($value); 

        $CACPartyName               = $xml->createElement('cac:PartyName'); 
        $CACPartyName               = $CACSignatoryParty->appendChild($CACPartyName);
        $PartyName                  = $xml->createElement('cbc:Name'); 
        $PartyName                  = $CACPartyName->appendChild($PartyName);
        $value                      = $xml->createTextNode($ComercialEmpresa);
        $value                      = $PartyName->appendChild($value);

        // Si Aplica UBL 2.1
        $CACDigitalSignatureAttachment = $xml->createElement('cac:DigitalSignatureAttachment'); 
        $CACDigitalSignatureAttachment = $CACSignature->appendChild($CACDigitalSignatureAttachment);

        $CACExternalReference       = $xml->createElement('cac:ExternalReference'); 
        $CACExternalReference       = $CACDigitalSignatureAttachment->appendChild($CACExternalReference);

        $CBCUri                     = $xml->createElement('cbc:URI'); 
        $CBCUri                     = $CACExternalReference->appendChild($CBCUri);
        $value                      = $xml->createTextNode('SIGN');
        $value                      = $CBCUri->appendChild($value);
        // Fin Signature

        // Datos del domicilio fiscal del emisor de la factura electrónica. (cac:AccountingSupplierParty)
        $CACAccountingSupplierParty = $xml->createElement('cac:AccountingSupplierParty'); 
        $CACAccountingSupplierParty = $Invoice ->appendChild($CACAccountingSupplierParty);

        $CABParty                   = $xml->createElement('cac:Party'); 
        $CABParty                   = $CACAccountingSupplierParty ->appendChild($CABParty);

        $CABPartyIdentification     = $xml->createElement('cac:PartyIdentification'); 
        $CABPartyIdentification     = $CABParty ->appendChild($CABPartyIdentification);

        $CBCPartyID                 = $xml->createElement('cbc:ID'); 
        $CBCPartyID                 = $CABPartyIdentification ->appendChild($CBCPartyID);
        $value                      = $xml->createTextNode($RucEmpresa);
        $value                      = $CBCPartyID->appendChild($value);
        $CBCPartyID->setAttribute('schemeID',"6");
        $CBCPartyID->setAttribute('schemeName',"Documento de Identidad");
        $CBCPartyID->setAttribute('schemeAgencyName',"PE:SUNAT");
        $CBCPartyID->setAttribute('schemeURI',"urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06");

        $CABPartyName               = $xml->createElement('cac:PartyName'); 
        $CABPartyName               = $CABParty ->appendChild($CABPartyName);

        $CBCPartyName               = $xml->createElement('cbc:Name'); 
        $CBCPartyName               = $CABPartyName ->appendChild($CBCPartyName);
        $value                      = $xml->createTextNode($ComercialEmpresa);
        $value                      = $CBCPartyName->appendChild($value);

        // Razón social de la empresa
        $CACPartyLegalEntity        = $xml->createElement('cac:PartyLegalEntity'); 
        $CACPartyLegalEntity        = $CABParty->appendChild($CACPartyLegalEntity);

        $CBCPartyLegalEntity        = $xml->createElement('cbc:RegistrationName'); 
        $CBCPartyLegalEntity        = $CACPartyLegalEntity->appendChild($CBCPartyLegalEntity);
        $value                      = $xml->createTextNode($NameEmpresa);
        $value                      = $CBCPartyLegalEntity->appendChild($value);

        // Datos adicionales de la empresa
        $CABPostalAddress           = $xml->createElement('cac:RegistrationAddress'); 
        $CABPostalAddress           = $CACPartyLegalEntity->appendChild($CABPostalAddress);

        // Local de la empresa
        $CBCUbigeo                  = $xml->createElement('cbc:AddressTypeCode'); 
        $CBCUbigeo                  = $CABPostalAddress ->appendChild($CBCUbigeo);
        $value                      = $xml->createTextNode($LocalEmpresa);
        $value                      = $CBCUbigeo->appendChild($value);

        // Urbanizacion
        $CBCCitySubdivisionName     = $xml->createElement('cbc:CitySubdivisionName'); 
        $CBCCitySubdivisionName     = $CABPostalAddress ->appendChild($CBCCitySubdivisionName);
        $value                      = $xml->createTextNode($UrbanizacionEmpresa);
        $value                      = $CBCCitySubdivisionName->appendChild($value);

        // Departamento
        $CBCCityName                = $xml->createElement('cbc:CityName'); 
        $CBCCityName                = $CABPostalAddress ->appendChild($CBCCityName);
        $value                      = $xml->createTextNode($DepartamentoEmpresa);
        $value                      = $CBCCityName->appendChild($value);

        // Provincia
        $CBCCountrySubentity        = $xml->createElement('cbc:CountrySubentity'); 
        $CBCCountrySubentity        = $CABPostalAddress ->appendChild($CBCCountrySubentity);
        $value                      = $xml->createTextNode($ProvinciaEmpresa);
        $value                      = $CBCCountrySubentity->appendChild($value);

        // Ubigeo
        $CBCStreetName              = $xml->createElement('cbc:CountrySubentityCode'); 
        $CBCStreetName              = $CABPostalAddress ->appendChild($CBCStreetName);
        $value                      = $xml->createTextNode($UbigeoEmpresa);
        $value                      = $CBCStreetName->appendChild($value);

        // Distrito
        $CBCDistrict                = $xml->createElement('cbc:District'); 
        $CBCDistrict                = $CABPostalAddress ->appendChild($CBCDistrict);
        $value                      = $xml->createTextNode($DistritoEmpresa);
        $value                      = $CBCDistrict->appendChild($value);

        // Direccion de la Empresa
        $CACCountry                 = $xml->createElement('cac:AddressLine'); 
        $CACCountry                 = $CABPostalAddress ->appendChild($CACCountry);

        $CBCIdentificationCode      = $xml->createElement('cbc:Line'); 
        $CBCIdentificationCode      = $CACCountry ->appendChild($CBCIdentificationCode);
        $value                      = $xml->createTextNode($DireccionEmpresa);
        $value                      = $CBCIdentificationCode->appendChild($value);

        // Id del pais
        $CACCountry                 = $xml->createElement('cac:Country'); 
        $CACCountry                 = $CABPostalAddress ->appendChild($CACCountry);

        $CBCIdentificationCode      = $xml->createElement('cbc:IdentificationCode'); 
        $CBCIdentificationCode      = $CACCountry ->appendChild($CBCIdentificationCode);
        $value                      = $xml->createTextNode($PaisEmpresa);
        $value                      = $CBCIdentificationCode->appendChild($value);
        // Fin - (cac:AccountingSupplierParty)

        // (cac:AccountingCustomerParty)
        // Apellidos y nombres o denominación o razón social del adquirente o usuario
        $CACAccountingCustomerParty = $xml->createElement('cac:AccountingCustomerParty'); 
        $CACAccountingCustomerParty = $Invoice ->appendChild($CACAccountingCustomerParty);


        $CACPartyCliente            = $xml->createElement('cac:Party'); 
        $CACPartyCliente            = $CACAccountingCustomerParty ->appendChild($CACPartyCliente);

        $CABPartyIdentification     = $xml->createElement('cac:PartyIdentification'); 
        $CABPartyIdentification     = $CACPartyCliente ->appendChild($CABPartyIdentification);

        $CBCPartyID                 = $xml->createElement('cbc:ID'); 
        $CBCPartyID                 = $CABPartyIdentification ->appendChild($CBCPartyID);
        $value                      = $xml->createTextNode($RucCliente);
        $value                      = $CBCPartyID->appendChild($value);
        $CBCPartyID->setAttribute('schemeID',$TipoCliente);
        $CBCPartyID->setAttribute('schemeName',"Documento de Identidad");
        $CBCPartyID->setAttribute('schemeAgencyName',"PE:SUNAT");
        $CBCPartyID->setAttribute('schemeURI',"urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06");

        // Razón social de la empresa
        $CACPartyLegalEntity2       = $xml->createElement('cac:PartyLegalEntity'); 
        $CACPartyLegalEntity2       = $CACPartyCliente->appendChild($CACPartyLegalEntity2);

        $CBCPartyLegalEntity2       = $xml->createElement('cbc:RegistrationName'); 
        $CBCPartyLegalEntity2       = $CACPartyLegalEntity2->appendChild($CBCPartyLegalEntity2);
        $value                      = $xml->createTextNode($NameCliente);
        $value                      = $CBCPartyLegalEntity2->appendChild($value);

        $CACTaxTotal                = $xml->createElement('cac:TaxTotal'); 
        $CACTaxTotal                = $Invoice ->appendChild($CACTaxTotal);
        
        $CACTaxAmount               = $xml->createElement('cbc:TaxAmount'); 
        $CACTaxAmount               = $CACTaxTotal ->appendChild($CACTaxAmount);
        $value                      = $xml->createTextNode($TotalesIGV);
        $value                      = $CACTaxAmount->appendChild($value);
        $CACTaxAmount->setAttribute('currencyID',$TipoMoneda);

        // Validar como facturacion
        if(in_array('1000', $ArrayCat05))
		{
			$valor_categoria_05				= '1000';
			$valor_name_05					= 'IGV';
			$valor_type_code_05				= 'VAT';

			$CACTaxSubtotal 				= $xml->createElement('cac:TaxSubtotal');
			$CACTaxSubtotal 				= $CACTaxTotal->appendChild($CACTaxSubtotal);

			$CBCTaxableAmount 				= $xml->createElement('cbc:TaxableAmount');
			$CBCTaxableAmount 				= $CACTaxSubtotal->appendChild($CBCTaxableAmount);
			$value 							= $xml->createTextNode(str_replace(",", "", number_format($OperacionesGravadas, 2)));
			$value 							= $CBCTaxableAmount->appendChild($value);
			$CBCTaxableAmount->setAttribute('currencyID', $TipoMoneda);

			$CBCTaxAmount 					= $xml->createElement('cbc:TaxAmount');
			$CBCTaxAmount 					= $CACTaxSubtotal->appendChild($CBCTaxAmount);
			$value 							= $xml->createTextNode(str_replace(",", "", number_format($TotalesIGV, 2)));
			$value 							= $CBCTaxAmount->appendChild($value);
			$CBCTaxAmount->setAttribute('currencyID', $TipoMoneda);

			$CACTaxCategory 				= $xml->createElement('cac:TaxCategory');
			$CACTaxCategory 				= $CACTaxSubtotal->appendChild($CACTaxCategory);

			$CACTaxScheme 					= $xml->createElement('cac:TaxScheme');
			$CACTaxScheme 					= $CACTaxCategory->appendChild($CACTaxScheme);

			$CBCId 							= $xml->createElement('cbc:ID');
			$CBCId 							= $CACTaxScheme->appendChild($CBCId);
			$value 							= $xml->createTextNode($valor_categoria_05);
			$value 							= $CBCId->appendChild($value);
			$CBCId->setAttribute('schemeAgencyName', "PE:SUNAT");
			$CBCId->setAttribute('schemeID', "UN/ECE 5153");
			$CBCId->setAttribute('schemeName', "Codigo de tributos");

			$CBCName 						= $xml->createElement('cbc:Name');
			$CBCName 						= $CACTaxScheme->appendChild($CBCName);
			$value 							= $xml->createTextNode($valor_name_05);
			$value 							= $CBCName->appendChild($value);

			$CBCTaxTypeCode 				= $xml->createElement('cbc:TaxTypeCode');
			$CBCTaxTypeCode 				= $CACTaxScheme->appendChild($CBCTaxTypeCode);
			$value 						 	= $xml->createTextNode($valor_type_code_05);
			$value 						 	= $CBCTaxTypeCode->appendChild($value);
		}

		if(in_array('9997', $ArrayCat05))
		{
			$valor_categoria_05				= '9997';
			$valor_name_05					= 'EXO';
			$valor_type_code_05				= 'VAT';

			$CACTaxSubtotal 				= $xml->createElement('cac:TaxSubtotal');
			$CACTaxSubtotal 				= $CACTaxTotal->appendChild($CACTaxSubtotal);

			$CBCTaxableAmount 				= $xml->createElement('cbc:TaxableAmount');
			$CBCTaxableAmount 				= $CACTaxSubtotal->appendChild($CBCTaxableAmount);
			$value 							= $xml->createTextNode(str_replace(",", "", number_format($OperacionesExoneradas, 2)));
			$value 							= $CBCTaxableAmount->appendChild($value);
			$CBCTaxableAmount->setAttribute('currencyID', $TipoMoneda);

			$CBCTaxAmount 					= $xml->createElement('cbc:TaxAmount');
			$CBCTaxAmount 					= $CACTaxSubtotal->appendChild($CBCTaxAmount);
			$value 							= $xml->createTextNode(str_replace(",", "", number_format("0.00", 2)));
			$value 							= $CBCTaxAmount->appendChild($value);
			$CBCTaxAmount->setAttribute('currencyID', $TipoMoneda);

			$CACTaxCategory 				= $xml->createElement('cac:TaxCategory');
			$CACTaxCategory 				= $CACTaxSubtotal->appendChild($CACTaxCategory);

			$CACTaxScheme 					= $xml->createElement('cac:TaxScheme');
			$CACTaxScheme 					= $CACTaxCategory->appendChild($CACTaxScheme);

			$CBCId 							= $xml->createElement('cbc:ID');
			$CBCId 							= $CACTaxScheme->appendChild($CBCId);
			$value 							= $xml->createTextNode($valor_categoria_05);
			$value 							= $CBCId->appendChild($value);
			$CBCId->setAttribute('schemeAgencyName', "PE:SUNAT");
			$CBCId->setAttribute('schemeID', "UN/ECE 5153");
			$CBCId->setAttribute('schemeName', "Codigo de tributos");

			$CBCName 						= $xml->createElement('cbc:Name');
			$CBCName 						= $CACTaxScheme->appendChild($CBCName);
			$value 							= $xml->createTextNode($valor_name_05);
			$value 							= $CBCName->appendChild($value);

			$CBCTaxTypeCode 				= $xml->createElement('cbc:TaxTypeCode');
			$CBCTaxTypeCode 				= $CACTaxScheme->appendChild($CBCTaxTypeCode);
			$value 						 	= $xml->createTextNode($valor_type_code_05);
			$value 						 	= $CBCTaxTypeCode->appendChild($value);
		}

		if(in_array('9998', $ArrayCat05))
		{
			$valor_categoria_05				= '9998';
			$valor_name_05					= 'INA';
			$valor_type_code_05				= 'FRE';

			$CACTaxSubtotal 				= $xml->createElement('cac:TaxSubtotal');
			$CACTaxSubtotal 				= $CACTaxTotal->appendChild($CACTaxSubtotal);

			$CBCTaxableAmount 				= $xml->createElement('cbc:TaxableAmount');
			$CBCTaxableAmount 				= $CACTaxSubtotal->appendChild($CBCTaxableAmount);
			$value 							= $xml->createTextNode(str_replace(",", "", number_format($OperacionesInafectas, 2)));
			$value 							= $CBCTaxableAmount->appendChild($value);
			$CBCTaxableAmount->setAttribute('currencyID', $TipoMoneda);

			$CBCTaxAmount 					= $xml->createElement('cbc:TaxAmount');
			$CBCTaxAmount 					= $CACTaxSubtotal->appendChild($CBCTaxAmount);
			$value 							= $xml->createTextNode(str_replace(",", "", number_format("0.00", 2)));
			$value 							= $CBCTaxAmount->appendChild($value);
			$CBCTaxAmount->setAttribute('currencyID', $TipoMoneda);

			$CACTaxCategory 				= $xml->createElement('cac:TaxCategory');
			$CACTaxCategory 				= $CACTaxSubtotal->appendChild($CACTaxCategory);

			$CACTaxScheme 					= $xml->createElement('cac:TaxScheme');
			$CACTaxScheme 					= $CACTaxCategory->appendChild($CACTaxScheme);

			$CBCId 							= $xml->createElement('cbc:ID');
			$CBCId 							= $CACTaxScheme->appendChild($CBCId);
			$value 							= $xml->createTextNode($valor_categoria_05);
			$value 							= $CBCId->appendChild($value);
			$CBCId->setAttribute('schemeAgencyName', "PE:SUNAT");
			$CBCId->setAttribute('schemeID', "UN/ECE 5153");
			$CBCId->setAttribute('schemeName', "Codigo de tributos");

			$CBCName 						= $xml->createElement('cbc:Name');
			$CBCName 						= $CACTaxScheme->appendChild($CBCName);
			$value 							= $xml->createTextNode($valor_name_05);
			$value 							= $CBCName->appendChild($value);

			$CBCTaxTypeCode 				= $xml->createElement('cbc:TaxTypeCode');
			$CBCTaxTypeCode 				= $CACTaxScheme->appendChild($CBCTaxTypeCode);
			$value 						 	= $xml->createTextNode($valor_type_code_05);
			$value 						 	= $CBCTaxTypeCode->appendChild($value);
		}

        // 25. Sumatoria otros cargos
        $CACLegalMonetaryTotal      = $xml->createElement('cac:LegalMonetaryTotal'); 
        $CACLegalMonetaryTotal      = $Invoice ->appendChild($CACLegalMonetaryTotal);
        
        // 1.- Total valor de venta 
        $CBCLineExtensionAmount     = $xml->createElement('cbc:LineExtensionAmount'); 
        $CBCLineExtensionAmount     = $CACLegalMonetaryTotal ->appendChild($CBCLineExtensionAmount);
        $value = $xml->createTextNode(str_replace(",","",number_format($TotalValorVta,2)));
        $value = $CBCLineExtensionAmount->appendChild($value);
        $CBCLineExtensionAmount->setAttribute('currencyID',$TipoMoneda);
        
        // 2.- Total precio de venta (incluye impuestos)//
        $CBCTaxInclusiveAmount      = $xml->createElement('cbc:TaxInclusiveAmount'); 
        $CBCTaxInclusiveAmount      = $CACLegalMonetaryTotal ->appendChild($CBCTaxInclusiveAmount);
        $value                      = $xml->createTextNode(str_replace(",","",number_format($TotalPrecioVta,2)));
        $value                      = $CBCTaxInclusiveAmount->appendChild($value);
        $CBCTaxInclusiveAmount->setAttribute('currencyID',$TipoMoneda);
        
        // 3.- Monto total de descuentos del comprobantes 
        $CBCAllowanceTotalAmount    = $xml->createElement('cbc:AllowanceTotalAmount'); 
        $CBCAllowanceTotalAmount    = $CACLegalMonetaryTotal ->appendChild($CBCAllowanceTotalAmount);
        $value                      = $xml->createTextNode(str_replace(",","",number_format($TotalDescuentos,2)));
        $value                      = $CBCAllowanceTotalAmount->appendChild($value);
        $CBCAllowanceTotalAmount->setAttribute('currencyID',$TipoMoneda);
        
        // 4.- Monto total de otros cargos del comprobante 
        $CBCChargeTotalAmount       = $xml->createElement('cbc:ChargeTotalAmount'); 
        $CBCChargeTotalAmount       = $CACLegalMonetaryTotal ->appendChild($CBCChargeTotalAmount);
        $value                      = $xml->createTextNode(str_replace(",","",number_format($TotalOtrosCargos,2)));
        $value                      = $CBCChargeTotalAmount->appendChild($value);
        $CBCChargeTotalAmount->setAttribute('currencyID',$TipoMoneda);

        // 5.- Monto total de anticipos del comprobante 
        $CBCPrepaidAmount           = $xml->createElement('cbc:PrepaidAmount'); 
        $CBCPrepaidAmount           = $CACLegalMonetaryTotal ->appendChild($CBCPrepaidAmount);
        $value                      = $xml->createTextNode(str_replace(",","",number_format($TotalAnticipos,2)));
        $value                      = $CBCPrepaidAmount->appendChild($value);
        $CBCPrepaidAmount->setAttribute('currencyID',$TipoMoneda);
        
        // 6.- Importe total de la venta, cesión en uso o del servicio prestado
        $CBCPayableAmount           = $xml->createElement('cbc:PayableAmount'); 
        $CBCPayableAmount           = $CACLegalMonetaryTotal ->appendChild($CBCPayableAmount);
        $value                      = $xml->createTextNode(str_replace(",","",number_format($ImporteTotalVta,2)));
        $value                      = $CBCPayableAmount->appendChild($value);
        $CBCPayableAmount->setAttribute('currencyID',$TipoMoneda);

        // B.2.4. Tag InvoiceLines
        for ($i=0; $i<count($ArrayItem); $i++)
        {
            $CACInvoiceLine         = $xml->createElement('cac:CreditNoteLine'); 
            $CACInvoiceLine         = $Invoice ->appendChild($CACInvoiceLine);
            
            $CBCID                  = $xml->createElement('cbc:ID'); 
            $CBCID                  = $CACInvoiceLine ->appendChild($CBCID);
            $value                  = $xml->createTextNode($ArrayItem[$i]);
            $value                  = $CBCID->appendChild($value);
            
            $CBCInvoicedQuantity    = $xml->createElement('cbc:CreditedQuantity'); 
            $CBCInvoicedQuantity    = $CACInvoiceLine ->appendChild($CBCInvoicedQuantity);
            $value                  = $xml->createTextNode($ArrayCantidad[$i]);
            $value                  = $CBCInvoicedQuantity->appendChild($value);
            $CBCInvoicedQuantity->setAttribute('unitCode',$ArrayTipoUnidad[$i]);
            $CBCInvoicedQuantity->setAttribute('unitCodeListID',"UN/ECE rec 20");
            $CBCInvoicedQuantity->setAttribute('unitCodeListAgencyName',"United Nations Economic Commission for Europe");
            
            $CBCineExtensionAmount  = $xml->createElement('cbc:LineExtensionAmount'); 
            $CBCineExtensionAmount  = $CACInvoiceLine ->appendChild($CBCineExtensionAmount);
            $value                  = $xml->createTextNode($ArrayValorTotal[$i]);
            $value                  = $CBCineExtensionAmount->appendChild($value);
            $CBCineExtensionAmount->setAttribute('currencyID',$TipoMoneda);
            
            $CACPricingReference    = $xml->createElement('cac:PricingReference'); 
            $CACPricingReference    = $CACInvoiceLine ->appendChild($CACPricingReference);
            
            $CACAlternativeConditionPrice = $xml->createElement('cac:AlternativeConditionPrice'); 
            $CACAlternativeConditionPrice = $CACPricingReference ->appendChild($CACAlternativeConditionPrice);
            
            $CBCPriceAmount         = $xml->createElement('cbc:PriceAmount'); 
            $CBCPriceAmount         = $CACAlternativeConditionPrice ->appendChild($CBCPriceAmount);
            $value                  = $xml->createTextNode($ArrayPrecioUnitario[$i]);
            $value                  = $CBCPriceAmount->appendChild($value);
            $CBCPriceAmount->setAttribute('currencyID',$TipoMoneda);
            
            $CBCPriceTypeCode       = $xml->createElement('cbc:PriceTypeCode'); 
            $CBCPriceTypeCode       = $CACAlternativeConditionPrice ->appendChild($CBCPriceTypeCode);
            $value                  = $xml->createTextNode($ArrayTipoPrecioVenta[$i]);
            $value                  = $CBCPriceTypeCode->appendChild($value);
            $CBCPriceTypeCode->setAttribute('listName',"Tipo de Precio");
            $CBCPriceTypeCode->setAttribute('listAgencyName',"PE:SUNAT");
            $CBCPriceTypeCode->setAttribute('listURI',"urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16");
        
        
            /*Igv por Item de Bien o Servicio*/
            $CACTaxTotal            = $xml->createElement('cac:TaxTotal'); 
            $CACTaxTotal            = $CACInvoiceLine ->appendChild($CACTaxTotal);
            
            $CACTaxAmount           = $xml->createElement('cbc:TaxAmount'); 
            $CACTaxAmount           = $CACTaxTotal ->appendChild($CACTaxAmount);
            $value                  = $xml->createTextNode($ArrayIgv[$i]);
            $value                  = $CACTaxAmount->appendChild($value);
            $CACTaxAmount->setAttribute('currencyID',$TipoMoneda);
            
            $CACTaxSubtotal         = $xml->createElement('cac:TaxSubtotal'); 
            $CACTaxSubtotal         = $CACTaxTotal ->appendChild($CACTaxSubtotal);
        
            $CBCTaxableAmount       = $xml->createElement('cbc:TaxableAmount'); 
            $CBCTaxableAmount       = $CACTaxSubtotal ->appendChild($CBCTaxableAmount);
            $value                  = $xml->createTextNode($ArrayValorTotal[$i]);
            $value                  = $CBCTaxableAmount->appendChild($value);
            $CBCTaxableAmount->setAttribute('currencyID',$TipoMoneda);
            
            $CBCTaxAmount           = $xml->createElement('cbc:TaxAmount'); 
            $CBCTaxAmount           = $CACTaxSubtotal ->appendChild($CBCTaxAmount);
            $value                  = $xml->createTextNode($ArrayIgv[$i]);
            $value                  = $CBCTaxAmount->appendChild($value);
            $CBCTaxAmount->setAttribute('currencyID',$TipoMoneda);
            
            $CACTaxCategory         = $xml->createElement('cac:TaxCategory'); 
            $CACTaxCategory         = $CACTaxSubtotal ->appendChild($CACTaxCategory);
            
            
            $CBCPercent             = $xml->createElement('cbc:Percent'); 
            $CBCPercent             = $CACTaxCategory ->appendChild($CBCPercent);
            $value                  = $xml->createTextNode($ArrayPorcentaje[$i]);
            $value                  = $CBCPercent->appendChild($value);
        
            $CACTaxExemptionReasonCode  = $xml->createElement('cbc:TaxExemptionReasonCode'); 
            $CACTaxExemptionReasonCode  = $CACTaxCategory ->appendChild($CACTaxExemptionReasonCode);
            $value                  = $xml->createTextNode($ArrayAfectacionIgv[$i]);
            $value                  = $CACTaxExemptionReasonCode->appendChild($value);
            $CACTaxExemptionReasonCode->setAttribute('listAgencyName',"PE:SUNAT");
            $CACTaxExemptionReasonCode->setAttribute('listName',"Afectacion del IGV");
            $CACTaxExemptionReasonCode->setAttribute('listURI',"urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07");
            
            
            $CACTaxScheme           = $xml->createElement('cac:TaxScheme'); 
            $CACTaxScheme           = $CACTaxCategory ->appendChild($CACTaxScheme);
            
            $CBCId                  = $xml->createElement('cbc:ID'); 
            $CBCId                  =$CACTaxScheme ->appendChild($CBCId);
            $value                  = $xml->createTextNode($ArrayCat05[$i]);
            $value                  = $CBCId->appendChild($value);
            $CBCId->setAttribute('schemeAgencyName',"PE:SUNAT");
            $CBCId->setAttribute('schemeID',"UN/ECE 5153");
            $CBCId->setAttribute('schemeName',"Codigo de tributos");
        
        
            $CBCName                = $xml->createElement('cbc:Name'); 
            $CBCName                = $CACTaxScheme ->appendChild($CBCName);
            $value                  = $xml->createTextNode($ArrayName05[$i]);
            $value                  = $CBCName->appendChild($value);
            
            $CBCTaxTypeCode         = $xml->createElement('cbc:TaxTypeCode'); 
            $CBCTaxTypeCode         = $CACTaxScheme ->appendChild($CBCTaxTypeCode);
            $value                  = $xml->createTextNode($ArrayTaxTypeCode05[$i]);
            $value                  = $CBCTaxTypeCode->appendChild($value);
            
            $CACItem                = $xml->createElement('cac:Item'); 
            $CACItem                = $CACInvoiceLine ->appendChild($CACItem);
            
            $CBCDescription         = $xml->createElement('cbc:Description'); 
            $CBCNDescription        = $CACItem ->appendChild($CBCDescription);
            $value                  = $xml->createTextNode($ArrayDetalleBienServicio[$i]);
            $value                  = $CBCDescription->appendChild($value);
        
            $CABSellersItemIdentification = $xml->createElement('cac:SellersItemIdentification'); 
            $CABSellersItemIdentification = $CACItem ->appendChild($CABSellersItemIdentification);
            
            $CBCID                  = $xml->createElement('cbc:ID'); 
            $CBCID                  = $CABSellersItemIdentification ->appendChild($CBCID);
            $value                  = $xml->createTextNode($ArrayCodigoBienServicio[$i]);
            $value                  = $CBCID->appendChild($value);
            
            $CACPrice               = $xml->createElement('cac:Price'); 
            $CACPrice               = $CACInvoiceLine ->appendChild($CACPrice);
            
            $CBCPriceAmount         = $xml->createElement('cbc:PriceAmount'); 
            $CBCPriceAmount         = $CACPrice ->appendChild($CBCPriceAmount);
            $value                  = $xml->createTextNode($ArrayValorUnitario[$i]);
            $value                  = $CBCPriceAmount->appendChild($value);
            $CBCPriceAmount->setAttribute('currencyID',$TipoMoneda);
        }

        $xml->formatOutput = true; 
        $strings_xml = $xml->saveXML(); 
        $file = '../Xml/xml-no-firmados/'. $RucEmpresa .'-'. $TipoComprobante .'-'. $NumComprobante.'.xml';
        $xml->save($file); 

        if (file_exists($file)) 
        {
            $r[0] = 'Registrado';
        } 
        else 
        {
            $r[0] = 'Error';
        }
        return $r;

    }
}
