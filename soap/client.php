<?php
$client = new SoapClient(
    null, // URI del fichero WSDL o NULL si funciona en modo non-WSDL.
    array(
        'location' => "http://localhost/Integracion/soap/server.php", // URL del servidor SOAP donde enviar la petición.
        'uri'      => "http://localhost/Integracion/soap/server.php", // Espacio de nombres destino del servicio SOAP.
        'trace'    => 1 // Activa el seguimiento de la petición para debug.
    )
);

try {
    // Parámetros de entrada: fecha de inicio, fecha de fin y tipo de habitación
    $startDate = '2024-12-01'; // Fecha de inicio
    $endDate = '2024-12-10';   // Fecha de fin
    $roomType = 'Single';      // Tipo de habitación

    // Llamada al método FuncionSaludo con los parámetros
    $return = $client->__soapCall("FuncionSaludo", array($startDate, $endDate, $roomType));

    // Mostrar el XML recibido
    echo "Respuesta del servidor SOAP (XML): <br>";
    echo "<pre>" . htmlspecialchars($return) . "</pre>";

    // Si deseas procesar el XML, puedes cargarlo en un objeto SimpleXMLElement
    $xml = simplexml_load_string($return);
    echo "Contenido XML: <br>";
    echo "<pre>";
    print_r($xml);
    echo "</pre>";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
