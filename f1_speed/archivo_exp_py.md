# 🏎️ F1 24 Telemetry Bridge: Especificación Técnica de Ingeniería

Este documento detalla la arquitectura, lógica de red y procesamiento de señales del script `bridge.py`. El sistema está diseñado para operar como un middleware de alto rendimiento entre el motor de física de Codemasters (F1 24) y una arquitectura web RESTful.

---

## 1. Arquitectura de Red y Protocolo UDP
El script implementa un servidor de escucha basado en **User Datagram Protocol (UDP)**.

* **Socket de Baja Latencia:** Se utiliza `socket.AF_INET` (IPv4) y `socket.SOCK_DGRAM` (UDP). A diferencia de TCP, UDP no realiza el *three-way handshake*, lo que permite recibir paquetes de telemetría a 60Hz o 120Hz sin introducir *jitter* o retrasos por retransmisión.
* **Binding:** El script se vincula a `0.0.0.0`, lo que significa que escuchará paquetes provenientes de cualquier interfaz de red (local o externa si la consola está en la misma LAN).

---

## 2. Deserialización de Datos (The `struct` Layer)
El juego envía datos en formato **Little-Endian**, que es el estándar de memoria para procesadores x86. El script utiliza la librería `struct` para realizar el *casting* de tipos C a tipos de Python.

### Análisis del Header (`HEADER_FORMAT = "<HBBBBBQfIIBB"`)
Cada paquete, independientemente de su tipo, comienza con estos 29 bytes:

| Símbolo | Tipo C | Tamaño | Descripción |
| :--- | :--- | :--- | :--- |
| `<` | - | - | Indica formato **Little-Endian**. |
| `H` | uint16 | 2 bytes | Año del juego (2024). |
| `B` | uint8 | 1 byte | Versión mayor del paquete. |
| `B` | uint8 | 1 byte | Versión menor del paquete. |
| `B` | uint8 | 1 byte | Versión del paquete de datos. |
| `B` | uint8 | 1 byte | **Packet ID**: Define qué datos contiene el cuerpo. |
| `Q` | uint64 | 8 bytes | Session UID: Identificador único de la partida. |
| `f` | float | 4 bytes | Session Time: Tiempo transcurrido en la sesión. |
| `I` | uint32 | 4 bytes | Frame Identifier: Número de frame procesado. |
| `B` | uint8 | 1 byte | **Player Car Index**: El índice de tu coche en la parrilla. |

---

## 3. Lógica de Procesamiento por Packet ID

El script utiliza un despachador condicional para procesar la carga útil (*payload*) según el ID detectada en el header:

### 📡 Packet 0: Motion (Física de Movimiento)
Se calcula el desplazamiento en el espacio euclidiano. 
* **Estructura:** Se salta el header y se busca el offset del jugador (`player_idx * 60`).
* **Datos:** Extrae `world_x`, `world_y` (altura, ignorada aquí) y `world_z`.
* **Filtro Temporal:** Solo almacena coordenadas cada 0.02s para evitar saturación de memoria.

### 🏁 Packet 2: Lap Data (Cronometría)
Es el controlador de estado del script. Gestiona la lógica de "Nueva Vuelta".
* **Detección de sectores:** Captura el tiempo exacto en milisegundos cuando el juego reporta que los sectores 1 y 2 han sido completados.
* **Cierre de Vuelta:** Cuando `lap_num > current_lap_num`, el script dispara un disparador (*trigger*) que empaqueta la telemetría acumulada y la envía a la base de datos mediante un hilo bloqueante (Request POST).

### ⚙️ Packet 6: Car Telemetry (Estado del Vehículo)
Captura los inputs del piloto y el estado del motor.
* **Velocidad:** Llega en km/h como un `uint16`.
* **Pedales:** Los valores de acelerador y freno llegan normalizados de 0.0 a 1.0. El script los multiplica por 100 para obtener un porcentaje legible.
* **Gear:** `-1` es reversa, `0` es punto muerto, `1-8` son marchas adelante.

---

## 4. Optimización de Memoria y Datos

Para garantizar la eficiencia, el script aplica técnicas de **Downsampling**:
1.  **Spatial Sampling:** En lugar de guardar cada paquete de telemetría (que llegaría cada 16ms), el script evalúa la distancia recorrida: `if current_lap_dist > (last_recorded_dist + 0.5)`. Esto crea un set de datos uniforme basado en la posición en pista, no en el tiempo.
2.  **Sanitización:** Uso de `math.isfinite()`. Es crítico para evitar que errores de coma flotante rompan el formato JSON que espera la API de Laravel.

---

## 5. Integración con la API (Capa de Persistencia)
La comunicación con el backend se realiza mediante el protocolo **HTTP/1.1** bajo arquitectura **REST**.

* **Endpoint Metadata:** Se envía una sola vez al detectar el cambio de circuito para actualizar el contexto de la sesión.
* **Payload de Vuelta:** Se envía un objeto JSON complejo que contiene:
    * Tiempos de sectores calculados.
    * Arrays de telemetría (series temporales).
* **Seguridad:** Implementa **Bearer Token Authentication**. El token en el header asegura que solo este bridge pueda escribir en la base de datos del usuario.

---

## 6. Manejo de Errores y Robustez
* **KeyboardInterrupt:** Captura el `Ctrl+C` para cerrar el socket limpiamente sin dejar el puerto 20777 bloqueado en el sistema operativo.
* **ConnectionError:** Si la API de Laravel está caída, el script atrapa la excepción de `requests` para evitar que el bridge se detenga (panic), permitiendo que el usuario siga jugando aunque no se guarden los datos temporalmente.

---
**Nivel de Programador:** 9 | **Estado:** Producción Ready