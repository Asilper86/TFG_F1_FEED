import socket
import struct

UDP_IP = "0.0.0.0"
UDP_PORT = 20777

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind((UDP_IP, UDP_PORT))

print("-" * 50)
print("F1 24 LAP HUNTER (ID 2)")
print("INDICACIONES: Cruza la meta despacio y observa los cambios.")
print("-" * 50)

last_data = None

try:
    while True:
        data, addr = sock.recvfrom(2048)
        packet_id = data[6]
        
        if packet_id == 2:
            # Seleccionamos el bloque del jugador (primeros 57 bytes tras el header de 29)
            player_data = data[29:29+57]
            
            if last_data is not None:
                # Comparamos bytes para ver que ha cambiado al cruzar la meta
                changes = []
                for i in range(len(player_data)):
                    if player_data[i] != last_data[i]:
                        changes.append((i, last_data[i], player_data[i]))
                
                if changes:
                    print(f"\n[CAMBIO DETECTADO] Bytes modificados:")
                    print(f"{'Offset':<8} | {'Viejo':<8} | {'Nuevo':<8}")
                    for offset, old, new in changes:
                        # Buscamos especificamente el cambio de 1 a 2
                        current_status = ""
                        if old == 1 and new == 2: current_status = " <--- ¿ES ESTA LA VUELTA?"
                        print(f"{offset:<8} | {old:<8} | {new:<8} {current_status}")
            
            last_data = player_data
            time_now = struct.unpack_from("<f", player_data, 4)[0]
            print(f"Tiempo Vuelta Actual: {time_now:.2f}s", end="\r")

except KeyboardInterrupt:
    print("\nDetenido.")
except Exception as e:
    print(f"Error: {e}")
