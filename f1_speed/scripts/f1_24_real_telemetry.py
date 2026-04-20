import socket
import struct
import requests
import time
import math
import sys

# CONFIGURACION API
IP_LARAVEL = "localhost"
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

TRACK_MAP = {
    0: "Melbourne", 1: "Paul Ricard", 2: "Shanghai", 3: "Sakhir (Bahrain)", 
    4: "Catalunya", 5: "Monaco", 6: "Montreal", 7: "Silverstone", 
    8: "Hockenheim", 9: "Hungaroring", 10: "Spa", 11: "Monza", 
    12: "Singapore", 13: "Suzuka", 14: "Abu Dhabi", 15: "Texas", 
    16: "Brazil", 17: "Austria", 18: "Sochi", 19: "Mexico", 
    20: "Baku", 21: "Sakhir Short", 22: "Silverstone Short", 
    23: "Texas Short", 24: "Suzuka Short", 25: "Hanoi", 
    26: "Zandvoort", 27: "Imola", 28: "Portimao", 29: "Jeddah", 
    30: "Miami", 31: "Las Vegas", 32: "Losail"
}

def get_active_session_id():
    try:
        url = f"{API_BASE_URL}/active-session"
        response = requests.get(url, headers=HEADERS, timeout=3)
        if response.status_code == 200:
            return response.json()['id']
        else:
            print(f"[ERR] El servidor respondió con codigo {response.status_code}")
    except requests.exceptions.ConnectionError:
         print(f"[ERR] No se pudo conectar al servidor en {API_BASE_URL}. ¿Está encendido?")
    except Exception as e:
        print(f"[ERR] Error inesperado: {e}")
    return None

def sanitize(val, default=0):
    if isinstance(val, (float, int)) and not math.isfinite(val):
        return default
    return float(val)

def procesar_vueltas(data, player_idx, current_lap_num, session_id, telemetria_acumulada):
    offset = HEADER_SIZE + (player_idx * 57)
    
    last_lap_time_ms = struct.unpack_from("<I", data, offset + 0)[0]
    current_lap_time_ms = struct.unpack_from("<I", data, offset + 4)[0]
    
    lap_dist = sanitize(struct.unpack_from("<f", data, offset + 20)[0])
    lap_num = struct.unpack_from("<B", data, offset + 33)[0]
    sector = struct.unpack_from("<B", data, offset + 36)[0]
    result_status = struct.unpack_from("<B", data, offset + 45)[0]
    
    return lap_num, lap_dist, result_status, sector, current_lap_time_ms, last_lap_time_ms
def run_bridge():
    print("="*50)
    print("      F1 SPEED Pro - F1 24 TOTAL SYNC V7")
    print("="*50)
    
    session_id = get_active_session_id()
    if not session_id:
        print("[ERR] Sin sesion activa en la WEB.")
        return

    print(f"[API] Sesion ID: {session_id}")
    
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.bind((UDP_IP, UDP_PORT))

    current_lap_num = -1
    current_x, current_z = 0, 0
    current_speed, current_gear = 0, 0
    current_throttle, current_brake = 0, 0
    current_lap_dist = 0
    
    telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": [], "distance": [], "world_x": [], "world_z": []}
    
    last_recorded_dist = -1.0
    last_gps_time = 0
    curr_track = "Unknown"
    last_status_time = 0
    s1_done, s2_done = False, False
    final_s1_time, final_s2_time = 0.0, 0.0

    try:
        while True:
            data, addr = sock.recvfrom(2048)
            if len(data) < HEADER_SIZE: continue

            try:
                header = struct.unpack(HEADER_FORMAT, data[:HEADER_SIZE])
            except:
                continue
                
            packet_id = header[5]
            player_idx = header[10]

            if player_idx == 255: continue

            if packet_id == 0:
                offset = HEADER_SIZE + (player_idx * 60)
                coords = struct.unpack_from("<fff", data, offset)
                current_x = round(sanitize(coords[0]), 2)
                current_z = round(sanitize(coords[2]), 2)
                now = time.time()
                if now - last_gps_time > 0.02:
                    telemetria_acumulada["world_x"].append(current_x)
                    telemetria_acumulada["world_z"].append(current_z)
                    last_gps_time = now

            elif packet_id == 1:
                track_id_num = struct.unpack_from("<b", data, HEADER_SIZE + 7)[0]
                if track_id_num != -1:
                    track_name = TRACK_MAP.get(track_id_num, f"Circuit_{track_id_num}")
                    if curr_track != track_name:
                        curr_track = track_name
                        try:
                            requests.post(f"{API_BASE_URL}/telemetry/metadata", json={"session_id": session_id, "track_id": curr_track}, headers=HEADERS, timeout=1)
                        except: pass

            elif packet_id == 2:
               lap_num, lap_dist, result_status, sector, curr_time_ms, last_time_ms = procesar_vueltas(data, player_idx, current_lap_num, session_id, telemetria_acumulada)
               current_lap_dist = lap_dist
               
               # Lógica de detección de Sectores
               if sector == 1 and not s1_done:
                   final_s1_time = curr_time_ms / 1000.0
                   s1_done = True
               elif sector == 2 and not s2_done:
                   final_s2_time = (curr_time_ms / 1000.0) - final_s1_time
                   s2_done = True

               if current_lap_num == -1:
                current_lap_num = lap_num
                print(f"\n[START] Vuelta {current_lap_num}")
               elif lap_num > current_lap_num:
                print(f"\n[END] Vuelta {current_lap_num} completada. Guardando datos...")
                try:
                    lap_time_seconds = last_time_ms / 1000.0
                    final_s3_time = lap_time_seconds - final_s1_time - final_s2_time if (s1_done and s2_done) else 0.0
                    
                    payload = {
                        "session_id": session_id,
                        "lap_number": current_lap_num,
                        "lap_time": lap_time_seconds,
                        "sector_1": final_s1_time,
                        "sector_2": final_s2_time,
                        "sector_3": final_s3_time,
                        "telemetry": telemetria_acumulada
                    }
                    response = requests.post(f"{API_BASE_URL}/telemetry/lap", json=payload, headers=HEADERS, timeout=2)
                    response.raise_for_status() # Esto mostrará el error si Laravel falla (404, 422, etc)
                    print(f"[API] Vuelta {current_lap_num} subida con éxito (T: {lap_time_seconds:.3f}s)")
                except requests.exceptions.HTTPError as e:
                    print(f"[ERR] Laravel rechazó los datos: {e.response.text}")
                except Exception as e:
                    print(f"[ERR] Error al subir la vuelta a la API: {e}")
                
                # Resetear para la nueva vuelta
                current_lap_num = lap_num
                last_recorded_dist = -1.0
                s1_done, s2_done = False, False
                final_s1_time, final_s2_time = 0.0, 0.0
                telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": [], "distance": [], "world_x": [], "world_z": []}
                print(f"\n[START] Vuelta {current_lap_num}")

            
            elif packet_id == 6:
                car_offset = HEADER_SIZE + (player_idx * 60)
                
                spd = struct.unpack_from("<H", data, car_offset)[0]
                current_speed = int(spd) if spd < 1000 else 0
                current_throttle = round(struct.unpack_from("<f", data, car_offset + 2)[0] * 100, 1)
                current_brake = round(struct.unpack_from("<f", data, car_offset + 10)[0] * 100, 1)
                current_gear = int(struct.unpack_from("<b", data, car_offset + 15)[0])
                
                if current_lap_dist > (last_recorded_dist + 0.5):
                    telemetria_acumulada["speed"].append(current_speed)
                    telemetria_acumulada["throttle"].append(sanitize(current_throttle))
                    telemetria_acumulada["brake"].append(sanitize(current_brake))
                    telemetria_acumulada["gear"].append(current_gear)
                    telemetria_acumulada["distance"].append(round(current_lap_dist, 1))
                    last_recorded_dist = current_lap_dist
            now = time.time()
            if now - last_status_time > 0.5:
                if current_lap_num != -1:
                    sys.stdout.write(f"\r [LIVE] Speed: {current_speed} km/h | vuelta: {current_lap_num} | Distancia: {current_lap_dist:.0f}m")
                    sys.stdout.flush()

                    last_status_time = now

    except KeyboardInterrupt:
        print("\n[STOP] Puente detenido.")
    except Exception as e:
        print(f"\n[ERR] {e}")

if __name__ == "__main__":
    run_bridge()
