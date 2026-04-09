import socket
import struct
import requests
import time

# --- CONFIGURACIÓN ---
IP_LARAVEL = "192.168.0.27"
API_BASE_URL = f"http://{IP_LARAVEL}:8000/api"
UDP_IP = "0.0.0.0" # Escucha en todas las interfaces del PC actual
UDP_PORT = 20777

# Headers para la API de Laravel
HEADERS = {
    "Authorization": "Bearer 2|JGiAHQz6MVYJszctDArOfqVWvVDPFonBEWAnds18cff4fb43",
    "Accept": "application/json",
    "Content-Type": "application/json"
}

# --- ESTRUCTURAS DE PAQUETES (F1 24) ---
HEADER_FORMAT = "<HBBBBBQfIIBB" # 29 bytes exactos
HEADER_SIZE = 29

def get_active_session_id():
    """Busca la sesión marcada como activa en tu Dashboard (vía API)."""
    try:
        response = requests.get(f"{API_BASE_URL}/active-session", headers=HEADERS, timeout=3)
        if response.status_code == 200:
            session = response.json()
            print(f"[API] Sesión activa detectada: ID {session['id']} ({session['track_id']})")
            return session['id']
        else:
            print(f"[WARN] No se encontró sesión activa en el Dashboard. Usando ID: 1 por defecto.")
            return 1
    except Exception as e:
        print(f"[WARN] Error conectando con API ({e}). Usando sesión ID: 1.")
        return 1

def run_bridge():
    print("\n" + "="*50)
    print("      F1 SPEED Pro - REAL TIME BRIDGE (F1 24)")
    print("="*50)
    
    session_id = get_active_session_id()
    print(f"[UDP] Escuchando juego en puerto {UDP_PORT}...")

    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.bind((UDP_IP, UDP_PORT))

    current_lap_num = -1
    packet_count = 0
    
    # Almacenamiento temporal
    telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": []}
    last_record_time = 0

    try:
        while True:
            data, addr = sock.recvfrom(2048)
            packet_count += 1
            
            # Debug: Monitor de actividad cada 200 paquetes
            if packet_count % 200 == 0:
                print(f"[LIVE] Recibiendo datos... (Pqts: {packet_count})", end="\r")

            header = struct.unpack(HEADER_FORMAT, data[:HEADER_SIZE])
            packet_id = header[5]
            player_idx = header[10] # Índice del jugador principal

            if player_idx == 255: continue

            # 1. Packet Lap Data (ID 2)
            if packet_id == 2:
                offset = HEADER_SIZE + (player_idx * 113)
                # Unpack: lastLapMS(I), curLapMS(I), s1MS(H), s1Min(B), s2MS(H), s2Min(B)
                # Nota: El formato exacto de sectores en F1 24 es un poco más complejo, 
                # simplificamos para obtener el número de vuelta y el tiempo total.
                lap_num = struct.unpack_from("<B", data, offset + 33)[0] # m_currentLapNum está en byte 33
                
                if current_lap_num == -1:
                    current_lap_num = lap_num
                    print(f"\n[START] ¡Pista detectada! Empezando Vuelta {current_lap_num}")

                # Detectamos cruce de meta
                if lap_num > current_lap_num and current_lap_num > 0:
                    # Extraer tiempos finales de la vuelta anterior
                    # F1 24: m_lastLapTimeInMS (0), m_sector1TimeInMS (8), m_sector1TimeMinutes (10), m_sector2TimeInMS (11), m_sector2TimeMinutes (13)
                    last_lap_ms = struct.unpack_from("<I", data, offset)[0]
                    s1_ms = struct.unpack_from("<H", data, offset + 8)[0]
                    s1_min = struct.unpack_from("<B", data, offset + 10)[0]
                    s2_ms = struct.unpack_from("<H", data, offset + 11)[0]
                    s2_min = struct.unpack_from("<B", data, offset + 13)[0]

                    # Conversión a segundos totales
                    s1_total = (s1_min * 60) + (s1_ms / 1000.0)
                    s2_total = (s2_min * 60) + (s2_ms / 1000.0)
                    lap_total = last_lap_ms / 1000.0
                    s3_total = lap_total - s1_total - s2_total

                    print(f"\n[META] Vuelta {current_lap_num}: {lap_total:.3f}s (S1: {s1_total:.3f}, S2: {s2_total:.3f}, S3: {s3_total:.3f})")
                    
                    payload = {
                        "session_id": session_id,
                        "lap_number": current_lap_num,
                        "lap_time": lap_total,
                        "sector_1": s1_total,
                        "sector_2": s2_total,
                        "sector_3": s3_total,
                        "telemetry": telemetria_acumulada
                    }
                    
                    try:
                        res = requests.post(f"{API_BASE_URL}/telemetry/lap", json=payload, headers=HEADERS, timeout=5)
                        if res.status_code == 201:
                            print(f"[OK] ¡Datos guardados en el Dashboard!")
                        else:
                            print(f"[ERROR API] {res.text}")
                    except Exception as e:
                        print(f"[ERR] No se pudo enviar a la web: {e}")

                    # Reset
                    current_lap_num = lap_num
                    telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": []}

            # 2. Packet Telemetría (ID 6)
            elif packet_id == 6:
                offset = HEADER_SIZE + (player_idx * 60)
                # m_speed(H), m_throttle(f), m_steer(f), m_brake(f), m_clutch(B), m_gear(b)
                tel = struct.unpack_from("<HfffBb", data, offset)
                
                now = time.time()
                if now - last_record_time > 0.1: # 10 muestras por segundo (suficiente para análisis)
                    telemetria_acumulada["speed"].append(int(tel[0]))
                    telemetria_acumulada["throttle"].append(int(tel[1] * 100))
                    telemetria_acumulada["brake"].append(int(tel[3] * 100))
                    telemetria_acumulada["gear"].append(int(tel[5]))
                    last_record_time = now

    except KeyboardInterrupt:
        print("\n[STOP] Puente detenido. ¡Nos vemos en boxes!")
    except Exception as e:
        print(f"\n[FATAL] Error en el puente: {e}")

if __name__ == "__main__":
    run_bridge()
