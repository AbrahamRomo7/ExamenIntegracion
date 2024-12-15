<?php
header('Content-Type: application/json');

// Incluir la conexión a la base de datos y el cliente SOAP
include_once 'conexion3.php';  // Corrige la ruta a conexion.php
include_once '../soap/client.php';  // Corrige la ruta al cliente SOAP

// Función para verificar disponibilidad
function checkAvailability($start_date, $end_date, $room_type) {
    global $client;  // Cliente SOAP
    $params = array('start_date' => $start_date, 'end_date' => $end_date, 'room_type' => $room_type);
    $response = $client->__soapCall('checkAvailability', array($params));
    return $response;
}

// Manejo de la solicitud POST para crear una reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] == '/Integracion/microservicio/inventory/reservations') {
    $data = json_decode(file_get_contents("php://input"), true);
    $room_type = $data['room_type'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $customer_name = $data['customer_name'];

    // Verificar la disponibilidad llamando al servicio SOAP
    $availability = checkAvailability($start_date, $end_date, $room_type);

    if ($availability->status == 'Available') {
        // Si la habitación está disponible, registrar la reserva
        $stmt = $pdo->prepare("INSERT INTO reservations (room_number, room_type, customer_name, start_date, end_date, status) 
                               VALUES (:room_number, :room_type, :customer_name, :start_date, :end_date, 'Confirmed')");
        $stmt->bindParam(':room_number', $availability->room_number);
        $stmt->bindParam(':room_type', $room_type);
        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();

        echo json_encode(['message' => 'Reserva realizada exitosamente']);
    } else {
        echo json_encode(['error' => 'Habitación no disponible']);
    }
}

// Manejo de la solicitud GET para consultar una reserva
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && preg_match('/^\/Integracion\/microservicio\/inventory\/reservations\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
    $reservation_id = $matches[1];

    // Buscar la reserva en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = :reservation_id");
    $stmt->bindParam(':reservation_id', $reservation_id);
    $stmt->execute();

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($reservation) {
        echo json_encode($reservation);
    } else {
        echo json_encode(['error' => 'Reserva no encontrada']);
    }
}

// Manejo de la solicitud DELETE para cancelar una reserva
elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE' && preg_match('/^\/Integracion\/microservicio\/inventory\/reservations\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
    $reservation_id = $matches[1];

    // Eliminar la reserva de la base de datos
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE reservation_id = :reservation_id");
    $stmt->bindParam(':reservation_id', $reservation_id);
    $stmt->execute();

    echo json_encode(['message' => 'Reserva cancelada exitosamente']);
} else {
    echo json_encode(['error' => 'Endpoint not found.']);
}
?>
