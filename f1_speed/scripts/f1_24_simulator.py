import requests
import time
import random

API_URL = "http://localhost:8000/api/telemetry/lap"
SESSION_ID = 1  # Ajusta esto si tu base de datos tiene otro ID



def generar_vuelta_sim(lap_num):
    puntos = 100

    telemetria = {
        "speed": [],
        "throttle": [],
        "brake": [],
        "gear": [],
    }

    # Generamos una ligera variación entre vueltas para que la gráfica no sea 
    variacion_aleatoria = random.randint(-15, 15)

    for i in range(puntos):
        # Simulación de curvas y rectas
        if i < 40 or i > 70:
            speed = 200 + (i % 120) + variacion_aleatoria
            throttle = 100
            brake = 0
            gear = 7
        else:
            speed = 300 - (i * 2) + variacion_aleatoria
            throttle = 0
            brake = 80
            gear = 3

        telemetria["speed"].append(speed)
        telemetria["throttle"].append(throttle)
        telemetria["brake"].append(brake)
        telemetria["gear"].append(gear)

    return {
        "session_id": SESSION_ID,
        "lap_number": lap_num,
        "lap_time": round(random.uniform(80.0, 95.0), 3),
        "sector_1": round(random.uniform(25.0, 29.0), 3),
        "sector_2": round(random.uniform(30.0, 33.0), 3),
        "sector_3": round(random.uniform(24.0, 26.0), 3),
        "telemetry": telemetria,
    }


def enviar_datos(numero_vueltas=5):
    print(f"\n[INFO] Arrancando simulador F1 24... Simulanndo {numero_vueltas} vueltas!\n")
    


    for vuelta in range(1, numero_vueltas + 1):
        datos = generar_vuelta_sim(vuelta)

        try:
            print(f"[RED] [Vuelta {vuelta}/{numero_vueltas}] Enviando tiempo de {datos['lap_time']}s...")
            
            cabeceras_vip = {
                "Authorization": "Bearer 2|JGiAHQz6MVYJszctDArOfqVWvVDPFonBEWAnds18cff4fb43",
                "Accept": "application/json"
            }
            response = requests.post(API_URL, json=datos, headers=cabeceras_vip)

            if response.status_code == 201:
                print("[OK] Exito! Valido en Box.")
            else:
                print(f"[ERROR] Error {response.status_code}: {response.text}")

        except requests.exceptions.ConnectionError:
            print("[FATAL] Error: No se pudo conectar con Laravel. Esta 'php artisan serve' activo?")
            break

        # Simulamos que tarda 3 segundos en dar cada vuelta para que veas el Dashboard cambiar en vivo 
        if vuelta < numero_vueltas:
            print("[WAIT] Acelerando en pista...\n")
            time.sleep(3) 

    print("\n[FIN] Sesion finalizada. Coches a Box.")


if __name__ == "__main__":
    # Ajusta aquí cuántas vueltas vuoi simular de golpe (ej: 10)
    enviar_datos(numero_vueltas=7)
