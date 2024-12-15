# ExamenIntegracion
Proyecto de Integración de Servicios: Gestión de Reservas y Disponibilidad de Habitaciones
Descripción
Este proyecto es una solución integrada que incluye tres componentes principales:

Servicio SOAP para consultar la disponibilidad de habitaciones.
API REST para gestionar reservas de habitaciones.
Microservicio de Inventario para gestionar las habitaciones y su estado.
Objetivo
El objetivo de este proyecto es proporcionar una plataforma de gestión de reservas de habitaciones, permitiendo a los usuarios consultar la disponibilidad y realizar reservas, todo ello gestionado por diferentes servicios que interactúan entre sí.

Estructura del Proyecto
El proyecto está organizado en tres componentes principales:

SOAP Service (ubicado en la carpeta soap)
API REST (ubicado en la carpeta api)
Microservicio de Inventario (ubicado en la carpeta microservicio)
Estructura de la Base de Datos
Tabla availability (Usada por el servicio SOAP):

room_id (int)
room_type (varchar)
available_date (date)
status (varchar)
Tabla reservations (Usada por la API REST):

reservation_id (int)
room_number (int)
customer_name (varchar)
start_date (date)
end_date (date)
status (varchar)
Tabla rooms (Usada por el Microservicio de Inventario):

room_id (int)
room_number (int)
room_type (varchar)
status (varchar)
Componentes del Proyecto
1. SOAP Service (server.php y client.php)
El servicio SOAP proporciona información sobre la disponibilidad de habitaciones. Los métodos disponibles son:

Consultar Disponibilidad: Permite consultar la disponibilidad de habitaciones dentro de un rango de fechas.
Funcionamiento:

El cliente SOAP realiza una solicitud al servidor SOAP con las fechas de inicio y fin de la reserva y el tipo de habitación.
El servidor SOAP devuelve un listado de habitaciones disponibles en formato XML.
Ruta del servicio SOAP:

http://localhost/Integracion/soap/server.php
Método: checkAvailability(start_date, end_date, room_type)

Parámetros:

start_date: Fecha de inicio de la reserva.
end_date: Fecha de fin de la reserva.
room_type: Tipo de habitación solicitada.
2. API REST (rest.php)
La API REST permite gestionar las reservas de habitaciones, incluyendo crear, consultar y cancelar reservas.

Endpoints:
POST /reservations: Crear una nueva reserva.

Verifica la disponibilidad llamando al servicio SOAP.
Registra la reserva en la base de datos reservations.
GET /reservations/{id}: Consultar una reserva existente.

Recupera los detalles de la reserva mediante el ID de la reserva.
DELETE /reservations/{id}: Cancelar una reserva.

Elimina la reserva de la base de datos.
Ruta base de la API REST:

http://localhost/Integracion/api/rest.php
3. Microservicio de Inventario (inventory.php)
El microservicio de inventario maneja la información sobre las habitaciones disponibles y su estado.

Funciones del Microservicio:
Consultar habitaciones disponibles: Permite consultar todas las habitaciones disponibles en el sistema.
Actualizar el estado de una habitación: Permite actualizar el estado (disponible o no disponible) de una habitación.
Ruta base del Microservicio:

http://localhost/Integracion/microservicio/inventory.php
