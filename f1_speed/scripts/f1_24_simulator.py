import socket
import struct
import time
import math
import random

# CONFIGURACION
UDP_IP = "127.0.0.1"
UDP_PORT = 20777

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

def crear_header(packet_id):
    # Formato F1 24: H(2)BBBBB Q(8) f(4) I(4) I(4) BB
    return struct.pack("<HBBBBBQfIIBB", 
        2024, 24, 1, 0, 1, packet_id, 
        12345678, time.time(), 1, 1, 0, 255)

def simular_telemetria():
    print("🚀 Arrancando Simulador UDP F1 24 (Modo Monza)...")
    distancia = 0.0
    vuelta = 1
    
    try:
        while True:
            # 1. Simular Movimiento (ID 6 - Telemetry)
            # Creamos datos creíbles
            speed = int(200 + 100 * math.sin(distancia / 500)) # Oscila entre 100 y 300
            throttle = 1.0 if speed < 280 else 0.5
            brake = 0.0 if speed > 150 else 1.0
            gear = int(speed / 40) + 1 # Marcha según velocidad
            
            # Construir Paquete de Telemetría (ID 6)
            header = crear_header(6)
            # Array de 22 coches (solo rellenamos el nuestro, el 0)
            # m_speed(H), m_throttle(f), m_steer(f), m_brake(f), m_clutch(B), m_gear(b)...
            car_data = struct.pack("<HfffBb", speed, throttle, 0.0, brake, 0, gear)
            # Rellenamos con ceros los otros 21 coches (cada bloque son 60 bytes)
            padding = b'\x00' * (60 - len(car_data)) + (b'\x00' * 60 * 21)
            
            sock.sendto(header + car_data + padding, (UDP_IP, UDP_PORT))

            # 2. Simular Lap Data (ID 2)
            header_lap = crear_header(2)
            # LapData block (63 bytes): dummy times + distance(f) + lap(B)...
            # Simplificamos: saltamos los primeros 20 bytes para llegar a distance
            lap_data = b'\x00' * 20 + struct.pack("<f", distancia) + b'\x00' * 9 + struct.pack("<B", vuelta)
            padding_lap = b'\x00' * (63 - len(lap_data)) + (b'\x00' * 63 * 21)
            
            sock.sendto(header_lap + lap_data + padding_lap, (UDP_IP, UDP_PORT))

            # Avanzar coche
            distancia += (speed / 3.6) * 0.1 # Avanzamos según velocidad
            if distancia > 5793: # Longitud de Monza
                distancia = 0
                vuelta += 1
                print(f"🏁 ¡Vuelta {vuelta-1} completada! Iniciando Vuelta {vuelta}")

            time.sleep(0.1) # 10Hz para no saturar

    except KeyboardInterrupt:
        print("\n🛑 Simulador detenido.")

if __name__ == "__main__":
    simular_telemetria()
