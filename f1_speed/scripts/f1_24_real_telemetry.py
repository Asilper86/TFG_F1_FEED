import socket
import struct
import requests
import time
import math
import threading
import sys
import os
from pathlib import Path
from dotenv import load_dotenv

# Cargar .env desde la raíz del proyecto (un nivel arriba de /scripts)
env_path = Path(__file__).resolve().parent.parent / '.env'
load_dotenv(dotenv_path=env_path)
IP_LARAVEL = os.getenv("LARAVEL_IP", "127.0.0.1")
if os.name != 'nt': 
    IP_LARAVEL = "127.0.0.1"

API_BASE_URL = f"http://{IP_LARAVEL}/api"
print(f"[DEBUG] API URL: {API_BASE_URL}")
UDP_IP = "0.0.0.0" 
UDP_PORT = 20777

def log_debug(msg):
    log_path = Path(__file__).resolve().parent.parent / "storage" / "logs" / "telemetry_debug.log"
    with open(log_path, "a") as f:
        f.write(f"{time.ctime()}: {msg}\n")

log_debug("Script iniciado")

# Guardar PID para que la web sepa que este script exacto está corriendo
with open(Path(__file__).resolve().parent / "telemetry.pid", "w") as f:
    f.write(str(os.getpid()))

HEADERS = {
    "Authorization": f"Bearer {os.getenv('API_TOKEN')}",
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
    url = f"{API_BASE_URL}/active-session"
    print("[INFO] Buscando sesion activa en la WEB... (Crea una para empezar)")
    log_debug(f"Haciendo peticion a: {url}")
    while True:
        try:
            response = requests.get(url, headers=HEADERS, timeout=3)
            if response.status_code == 200:
                sid = response.json()['id']
                log_debug(f"Sesion encontrada: {sid}")
                return sid
        except Exception as e:
            log_debug(f"Error buscando sesion: {e}")
            pass
        time.sleep(3)

def sanitize(val, default=0):
    if isinstance(val, (float, int)) and not math.isfinite(val):
        return default
    return float(val)

def procesar_vueltas(data, player_idx, current_lap_num, session_id, telemetria_acumulada):
    offset = HEADER_SIZE + (player_idx * 63)
    
    last_lap_time_ms = struct.unpack_from("<I", data, offset + 0)[0]
    current_lap_time_ms = struct.unpack_from("<I", data, offset + 4)[0]
    
    lap_dist = sanitize(struct.unpack_from("<f", data, offset + 20)[0])
    lap_num = struct.unpack_from("<B", data, offset + 33)[0]
    sector = struct.unpack_from("<B", data, offset + 36)[0]
    result_status = struct.unpack_from("<B", data, offset + 45)[0]
    
    return lap_num, lap_dist, result_status, sector, current_lap_time_ms, last_lap_time_ms

def enviar_async_post(url, payload, headers, is_lap=False, lap_num=0):
    try:
        timeout = 3.0 if is_lap else 1.0
        response = requests.post(url, json=payload, headers=headers, timeout=timeout)
        if is_lap:
            if response.status_code in [200, 201]:
                print(f"\n[API] Vuelta {lap_num} subida con éxito.")
            else:
                print(f"\n[ERR] Laravel rechazó la vuelta: {response.text}")
    except Exception as e:
        if is_lap:
            print(f"\n[ERR] Error al subir la vuelta a la API: {e}")



def run_bridge():
    print("="*50)
    print("      F1 SPEED Pro - F1 24 TOTAL SYNC V7")
    print("="*50)
    
    session_id = get_active_session_id()

    print(f"[API] Sesion ID: {session_id}")
    
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.bind((UDP_IP, UDP_PORT))
    sock.settimeout(0.5)

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
    race_finished = False
    prev_curr_time_ms = 0
    telemetria_respaldo = None
    tiempo_respaldo = 0.0
    s1_respaldo, s2_respaldo = 0.0, 0.0

    try:
        while True:
            try:
                try:
                    data, addr = sock.recvfrom(2048)
                except socket.timeout:
                    continue

                if len(data) < HEADER_SIZE: continue

                try:
                    header = struct.unpack(HEADER_FORMAT, data[:HEADER_SIZE])
                except:
                    continue
                    
                packet_id = header[5]
                player_idx = header[10]

                if player_idx == 255: continue
                
                # Solo loguear packet_id 2 (Laps) para no saturar el log
                if packet_id == 2:
                    log_debug("Recibido paquete de vuelta (ID 2)")

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
                       
                   # Prevención de fallos por Flashback (rebobinado) o "Reiniciar Vuelta"
                   if curr_time_ms < prev_curr_time_ms - 1000 and result_status != 3:
                       if lap_num == current_lap_num:
                           # El tiempo retrocedió pero la vuelta es la misma -> Reinicio de vuelta o Fin de carrera
                           if curr_time_ms < 1000 or sector == 0:
                               telemetria_respaldo = telemetria_acumulada.copy()
                               tiempo_respaldo = prev_curr_time_ms
                               s1_respaldo = final_s1_time
                               s2_respaldo = final_s2_time
                               s1_done, s2_done = False, False
                               final_s1_time, final_s2_time = 0.0, 0.0
                               telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": [], "distance": [], "world_x": [], "world_z": []}
                           elif sector == 1:
                               s2_done = False
                               final_s2_time = 0.0
                   prev_curr_time_ms = curr_time_ms
                   
                   # Lógica de detección de Sectores
                   if sector == 1 and not s1_done:
                       final_s1_time = curr_time_ms / 1000.0
                       if final_s1_time > 0: s1_done = True
                   elif sector == 2 and not s2_done:
                       final_s2_time = (curr_time_ms / 1000.0) - final_s1_time
                       if final_s2_time > 0: s2_done = True

                   if current_lap_num == -1 or lap_num < current_lap_num:
                    current_lap_num = lap_num
                    s1_done, s2_done = False, False
                    final_s1_time, final_s2_time = 0.0, 0.0
                    telemetria_acumulada = {"speed": [], "throttle": [], "brake": [], "gear": [], "distance": [], "world_x": [], "world_z": []}
                    race_finished = False
                    print(f"\n[START/RESTART] Vuelta {current_lap_num} (Nueva sesión o reinicio detectado)")
                   elif lap_num > current_lap_num or (result_status == 3 and not race_finished):
                    if result_status == 3:
                        race_finished = True
                    
                    telemetria_a_guardar = telemetria_acumulada
                    s1_a_guardar = final_s1_time
                    s2_a_guardar = final_s2_time
                    
                    if result_status == 3 and len(telemetria_acumulada["speed"]) < 30 and telemetria_respaldo:
                        telemetria_a_guardar = telemetria_respaldo
                        s1_a_guardar = s1_respaldo
                        s2_a_guardar = s2_respaldo
                    
                    if len(telemetria_a_guardar["speed"]) > 30:
                        print(f"\n[END] Vuelta {current_lap_num} completada. Guardando datos...")
                        try:
                            lap_time_seconds = last_time_ms / 1000.0
                            
                            # Fallback por si el juego envía 0.0 al terminar la carrera
                            if lap_time_seconds < 1.0 and result_status == 3:
                                lap_time_seconds = tiempo_respaldo / 1000.0 if tiempo_respaldo > 0 else (curr_time_ms / 1000.0)

                            final_s3_time = lap_time_seconds - s1_a_guardar - s2_a_guardar
                            if final_s3_time < 0: final_s3_time = 0.0
                            
                            payload = {
                                "session_id": session_id,
                                "lap_number": current_lap_num,
                                "lap_time": lap_time_seconds,
                                "sector_1": s1_a_guardar,
                                "sector_2": s2_a_guardar,
                                "sector_3": final_s3_time,
                                "telemetry": telemetria_a_guardar
                            }
                            threading.Thread(
                                target=enviar_async_post,
                                args=(f"{API_BASE_URL}/telemetry/lap", payload, HEADERS, True, current_lap_num),
                                daemon=True
                            ).start()
                            print(f"[INFO] Enviando vuelta {current_lap_num} a Laravel en segundo plano...")
                        except Exception as e:
                            print(f"[ERR] Error al preparar el guardado de la vuelta: {e}")
                    else:
                        print(f"\n[SKIP] Vuelta {current_lap_num} ignorada (Vuelta vacía o lanzada corta).")
                    
                   
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

                        status_payload = {
                            "session_id": session_id,
                            "status" : {
                                "position": {"x": current_x, "z": current_z},
                                "speed": current_speed,
                                "gear": current_gear,
                                "lap": current_lap_num
                            }
                        }

                        threading.Thread(
                            target=enviar_async_post, 
                            args=(f"{API_BASE_URL}/telemetry/status", status_payload, HEADERS), 
                            daemon=True
                        ).start()
                        last_status_time = now
            except Exception as e:
                log_debug(f"Error en el bucle principal: {e}")
                continue
    except Exception as e:
        log_debug(f"Error FATAL: {e}")

    except KeyboardInterrupt:
        try:
            requests.post(f"{API_BASE_URL}/telemetry/cerrar-sesion", json={"session_id": session_id},  headers=HEADERS, timeout=2.0)
            print("[OK] Sesión cerrada correctamente en la base de datos.")
        except:
            print("[ERR] No se pudo cerrar la sesión en Laravel.")


if __name__ == "__main__":
    run_bridge()
