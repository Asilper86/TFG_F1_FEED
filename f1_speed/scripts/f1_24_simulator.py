import requests
import time
import random
import json

API_URL = "http://localhost:8000/api/telemetry/lap"
SESSION_ID = 2


def generar_vuelta_sim():
    print("Simulando vuelta...")
    puntos = 100

    telemetria = {
        "speed": [],
        "throttle": [],
        "brake": [],
        "gear": [],
    }

    for i in range(puntos):
        if i < 40 or i > 70:
            speed = 200 + (i % 120)
            throttle = 100
            brake = 0
            gear = 7
        else:
            speed = 300 - (i * 2)
            throttle = 0
            brake = 80
            gear = 3

        telemetria["speed"].append(speed)
        telemetria["throttle"].append(throttle)
        telemetria["brake"].append(brake)
        telemetria["gear"].append(gear)

    return {
        "session_id": SESSION_ID,
        "lap_time": round(random.uniform(80.0, 95.0), 3),
        "sector_1": 28.5,
        "sector_2": 32.1,
        "sector_3": 25.4,
        "telemetry": telemetria,
    }


def enviar_datos():
    datos = generar_vuelta_sim()

    try:
        print(f"📡 Enviando vuelta de {datos['lap_time']}s a Laravel...")
        response = requests.post(API_URL, json=datos)

        if response.status_code == 201:
            print("✅ ¡Éxito! Datos guardados en la base de datos.")
        else:
            print(f"❌ Error {response.status_code}: {response.text}")

    except requests.exceptions.ConnectionError:
        print(
            "🚫 Error: No se pudo conectar con Laravel. ¿Está 'php artisan serve' activo?"
        )


if __name__ == "__main__":
    enviar_datos()
