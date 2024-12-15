<?php
class ContactoExterior {
    public function FuncionSaludo($startDate, $endDate, $roomType) {
        // Datos de la base de datos
        $serverName = "localhost";
        $dbName = "cont1";
        $userName = "root";
        $password = "";

        // Conectar a la base de datos MySQL
        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
            // Configurar el modo de error de PDO
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparar la consulta con parámetros para fecha de inicio, fecha de fin y tipo de habitación
            $stmt = $conn->prepare("
                SELECT * FROM availability 
                WHERE available_date BETWEEN :startDate AND :endDate
                AND room_type = :roomType
                AND status = 'Available'
            ");
            // Enlazar parámetros
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->bindParam(':roomType', $roomType);
            $stmt->execute();

            // Obtener todos los resultados
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convertir el resultado a XML
            $xml = new SimpleXMLElement('<root/>');
            foreach ($result as $row) {
                $item = $xml->addChild('room');
                foreach ($row as $key => $value) {
                    $item->addChild($key, $value);
                }
            }

            // Retornar el XML como respuesta
            return $xml->asXML();

        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}

try {
    $server = new SOAPServer(
        NULL,
        array(
            'uri' => 'http://localhost/Integracion/soap/server.php'
        )
    );

    $server->setClass('ContactoExterior');
    $server->handle();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
