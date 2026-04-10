import socket
import struct
import requests
import time


IP_LARAVEL = "192.168.0.27"
API_BASE_URL = f"http://{IP_LARAVEL}:8000/api"
UDP_IP = "0.0.0.0" 
UDP_PORT = 20777

HEADERS = {
    "Authorization": "Bearer 2|JGiAHQz6MVYJszctDArOfqVWvVDPFonBEWAnds18cff4fb43",
    "Accept": "application/json",
    "Content-Type": "application/json"
}


HEADER_FORMAT = "<HBBBBBQfIIBB"
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
    
    
    
    telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": [], "distance": []}
    last_record_time = 0

    try:
        lap_dist = 0
        s1_dist = 0
        s2_dist = 0
        s1_done = False
        s2_done = False
        final_s1_time = 0
        final_s2_time = 0
        while True:
            data, addr = sock.recvfrom(2048)
            packet_count += 1
            
            
            if packet_count % 200 == 0:
                print(f"[LIVE] Recibiendo datos... (Pqts: {packet_count})", end="\r")

            header = struct.unpack(HEADER_FORMAT, data[:HEADER_SIZE])
            packet_id = header[5]
            player_idx = header[10] 

            if player_idx == 255: continue

            if packet_id == 2:

                offset = HEADER_SIZE + (player_idx * 113)
                s1_ms = struct.unpack_from("<H", data, offset + 8)[0]
                s1_min = struct.unpack_from("<B", data, offset + 10)[0]
                s2_ms = struct.unpack_from("<H", data, offset + 11)[0]
                s2_min = struct.unpack_from("<B", data, offset + 13)[0]
                lap_num = struct.unpack_from("<B", data, offset + 33)[0]
                lap_dist = struct.unpack_from("<f", data, offset + 18)[0]

                if s1_ms > 0 and not s1_done: 
                    s1_dist = lap_dist
                    s1_done = True
                    final_s1_time = (s1_min * 60) + (s1_ms / 1000.0)
                    print(f"[SECTOR] S1: {final_s1_time:.3f}s en el metro {s1_dist:.1f}m")
                if s2_ms > 0 and not s2_done:
                    s2_dist = lap_dist
                    s2_done = True
                    final_s2_time = (s2_min * 60) + (s2_ms / 1000.0)
                    print(f"[SECTOR] S2: {final_s2_time:.3f}s en el metro {s2_dist:.1f}m")

                offset = HEADER_SIZE + (player_idx * 113)
                lap_num = struct.unpack_from("<B", data, offset + 33)[0] 
                lap_dist = struct.unpack_from("<f", data, offset + 20)[0]
                
                if current_lap_num == -1:
                    current_lap_num = lap_num
                    print(f"\n[START] ¡Pista detectada! Empezando Vuelta {current_lap_num}")

                
                if lap_num > current_lap_num and current_lap_num > 0:
                    last_lap_ms = struct.unpack_from("<I", data, offset)[0]
                    s1_ms = struct.unpack_from("<H", data, offset + 8)[0]
                    s1_min = struct.unpack_from("<B", data, offset + 10)[0]
                    s2_ms = struct.unpack_from("<H", data, offset + 11)[0]
                    s2_min = struct.unpack_from("<B", data, offset + 13)[0]

                    
                    s1_total = (s1_min * 60) + (s1_ms / 1000.0)
                    s2_total = (s2_min * 60) + (s2_ms / 1000.0)
                    lap_total = last_lap_ms / 1000.0
                    s3_total = lap_total - s1_total - s2_total

                    print(f"\n[META] Vuelta {current_lap_num}: {lap_total:.3f}s (S1: {s1_total:.3f}, S2: {s2_total:.3f}, S3: {s3_total:.3f})")
                    
                    payload = {
                        "session_id": session_id,
                        "lap_number": current_lap_num,
                        "lap_time": lap_total,
                        "sector_1": final_s1_time,
                        "sector_2": final_s2_time,
                        "sector_3": lap_total - final_s1_time - final_s2_time,
                        "telemetry": telemetria_acumulada,
                        "s1_dist": s1_dist,
                        "s2_dist": s2_dist
                    }
                    
                    try:
                        res = requests.post(f"{API_BASE_URL}/telemetry/lap", json=payload, headers=HEADERS, timeout=5)
                        if res.status_code == 201:
                            print(f"[OK] ¡Datos guardados en el Dashboard!")
                        else:
                            print(f"[ERROR API] {res.text}")
                    except Exception as e:
                        print(f"[ERR] No se pudo enviar a la web: {e}")

                    # === ZONA DE RESET (Limpieza para la siguiente vuelta) ===
                    print(f"[RESET] Preparando sensores para Vuelta {lap_num}...")
                    current_lap_num = lap_num
                    telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": [], "distance": []}
                    s1_done = False 
                    s2_done = False
                    final_s1_time = 0
                    final_s2_time = 0
                    s1_dist = 0
                    s2_dist = 0

          
            elif packet_id == 6:
                offset = HEADER_SIZE + (player_idx * 60)
                tel = struct.unpack_from("<HfffBb", data, offset)
                
                now = time.time()
                if now - last_record_time > 0.1: 
                    telemetria_acumulada["speed"].append(int(tel[0]))
                    telemetria_acumulada["throttle"].append(int(tel[1] * 100))
                    telemetria_acumulada["brake"].append(int(tel[3] * 100))
                    telemetria_acumulada["gear"].append(int(tel[5]))
                    telemetria_acumulada["distance"].append(float(lap_dist))
                    last_record_time = now

    except KeyboardInterrupt:
        print("\n[STOP] Puente detenido. ¡Nos vemos en boxes!")
    except Exception as e:
        print(f"\n[FATAL] Error en el puente: {e}")

if __name__ == "__main__":
    run_bridge()
