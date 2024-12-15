<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-with, Content-type, Authorization');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');

// Cargar la conexión a la base de datos
require_once 'conexion2.php';

// Cargar el cliente SOAP para verificar la disponibilidad
require_once '../soap/client.php';

// Función para verificar disponibilidad usando el servicio SOAP
function checkAvailability($startDate, $endDate, $roomType) {
    global $client;
    try {
        // Llamar al servicio SOAP para verificar la disponibilidad
        $return = $client->__soapCall("FuncionSaludo", array($startDate, $endDate, $roomType));
        // Si el XML es válido, procesamos la respuesta
        $xml = simplexml_load_string($return);
        return count($xml->room) > 0;  // Retorna verdadero si hay habitaciones disponibles
    } catch (Exception $e) {
        return false;
    }
}

// Función para crear una reserva
function createReservation($data) {
    global $conn;

    $roomNumber = $data['room_number'];
    $customerName = $data['customer_name'];
    $startDate = $data['start_date'];
    $endDate = $data['end_date'];
    $status = 'Pending';

    // Verificar la disponibilidad de la habitación
    if (!checkAvailability($startDate, $endDate, $roomType)) {
        return ['error' => 'Room not available for the selected dates.'];
    }

    // Insertar la reserva en la base de datos
    try {
        $stmt = $conn->prepare("INSERT INTO reservations (room_number, customer_name, start_date, end_date, status)
        VALUES (:room_number, :customer_name, :start_date, :end_date, :status)");
        $stmt->bindParam(':room_number', $roomNumber);
        $stmt->bindParam(':customer_name', $customerName);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        // Retornar el id de la reserva creada
        return ['reservation_id' => $conn->lastInsertId(), 'message' => 'Reservation created successfully.'];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Función para obtener una reserva por ID
function getReservation($reservationId) {
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_id = :reservation_id");
        $stmt->bindParam(':reservation_id', $reservationId);
        $stmt->execute();

        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            return $reservation;
        } else {
            return ['error' => 'Reservation not found.'];
        }
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Función para cancelar una reserva
function cancelReservation($reservationId) {
    global $conn;

    try {
        $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = :reservation_id");
        $stmt->bindParam(':reservation_id', $reservationId);
        $stmt->execute();

        return ['message' => 'Reservation canceled successfully.'];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Rutas de la API REST
if ($method == 'POST' && $_SERVER['REQUEST_URI'] == '/Integracion/api/reservations') {
    // Crear reserva
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode(createReservation($data));
} elseif ($method == 'GET' && preg_match('/\/Integracion\/api\/reservations\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    // Consultar reserva
    $reservationId = $matches[1];
    echo json_encode(getReservation($reservationId));
} elseif ($method == 'DELETE' && preg_match('/\/Integracion\/api\/reservations\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    // Cancelar reserva
    $reservationId = $matches[1];
    echo json_encode(cancelReservation($reservationId));
} else {
    // Método no permitido o ruta no encontrada
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Endpoint not found.']);
}
?>
