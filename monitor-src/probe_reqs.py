#!/usr/bin/env python3

import time
import json
import requests
import re
import threading
import subprocess
from scapy.all import *

# Configuration
PI_NUM = 1
INTERFACE_MON = "wlan1"

# Global list to store captured scan data and a counter for frames
captured_frames = []
frame_counter = 0
lock = threading.Lock()

def packet_handler(packet):
    """
    Callback function for each sniffed packet.
    It checks for 802.11 management frames (type=0) with subtypes
    corresponding to probe requests (subtype=4) and probe responses (subtype=5).
    """
    global frame_counter
    try:
        if packet.haslayer(Dot11):
            # grab us a probe request
            if packet.type == 0 and (packet.subtype == 4 or packet.subtype == 5):
                frame_counter += 1
                frame_type = "Probe Request" if packet.subtype == 4 else "Probe Response"
                timestamp = packet.time
                mac_address = packet.addr2
                ssid = None

                if packet.haslayer(Dot11Elt):
                    elt = packet.getlayer(Dot11Elt)
                    while elt:
                        if elt.ID == 0:  # SSID element
                            try:
                                ssid = elt.info.decode(errors="ignore")
                            except Exception:
                                ssid = str(elt.info)
                            break
                        elt = elt.payload.getlayer(Dot11Elt)
                
                scan_data = {
                    "mac_address": mac_address,
                    "timestamp_ms": int(packet.time * 1000),
                    "ssid": ssid
                }

                with lock:
                    captured_frames.append(scan_data)

                frame_time_str = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(timestamp))
                print(f"[{frame_counter}] {frame_time_str} - {frame_type}: {mac_address} | SSID: {ssid}")

    except Exception as e:
        print(f"Error in packet_handler: {e}")

def send_data(data, endpoint):
    """
    Sends the collected data as JSON to the specified endpoint.
    """
    headers = {'Content-Type': 'application/json'}
    try:
        response = requests.post(endpoint, json=data, headers=headers)
        if response.status_code in (200, 201, 204):
            print("Data sent successfully.")
        else:
            print(f"Failed to send data, status code: {response.status_code}")
            print(f"Failed to send data, status code: {response.status_code}")
    except Exception as e:
        print(f"Error sending data: {e}")


def get_wlan_ip(interface="wlan0"):
    """
    Gets IP for future ssh. If not connected will fail. Better practice would be
    to run this every 5 minutes or so with the batch of data
    """
    try:
        output = subprocess.check_output("sudo ip a", shell=True).decode()
        # Find the section that starts with the interface name
        interface_section = re.search(rf"{interface}:.*?(\n\s+inet\s+(\d+\.\d+\.\d+\.\d+))", output, re.S)
        if interface_section:
            return interface_section.group(2)
        return f"No IP found for {interface}"
    except subprocess.CalledProcessError:
        return f"Interface {interface} not found"
    except Exception as e:
        return f"Error getting IP: {e}"
    
def batch_sender():
    """
    Every 60 seconds, this function batches the captured frames, formats them
    into the required JSON structure, and sends them to the endpoint.
    """
    try:
        ip_address = get_wlan_ip()
        endpoint = f"http://istuftsbusy.com/api/ingest/{PI_NUM}"
    except Exception as e:
        print(f"Error getting public IP: {e}")
        ip_address = "Unknown"
    
    while True:
        time.sleep(60)

        with lock:
            if captured_frames:
                scans_to_send = captured_frames.copy()
                captured_frames.clear()
            else:
                scans_to_send = []

        if scans_to_send:
            payload = {
                "ip_address": ip_address,
                "scans": scans_to_send
            }
            print(f"Sending batch of {len(scans_to_send)} scans to {endpoint}")
            send_data(payload, endpoint)
        else:
            print("No new scans in this batch.")

def main():
    interface = INTERFACE_MON 
    print(f"Starting Scapy sniffing on interface {interface} for 802.11 probe messages...")

    sender_thread = threading.Thread(target=batch_sender, daemon=True)
    sender_thread.start()

    try:
        sniff(iface=interface, prn=packet_handler, store=False)
    except KeyboardInterrupt:
        print("\nSniffing stopped by user.")
    except Exception as e:
        print(f"An error occurred during sniffing: {e}")
        print(f"Error in main sniffing loop: {e}")

if __name__ == "__main__":
    main()
