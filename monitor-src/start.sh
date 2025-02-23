#!/usr/bin/env sh

# wait for start up
sleep 30

# removes wlan1 from managed Networks
nmcli device disconnect wlan1

# doesn't let it auto connect >:(
nmcli connection modify "wlan1" connection.autoconnect no

# starts monitor mode
sudo airmon-ng start wlan1

# CHANGE THIS PATH TO LOCATION OF probe_reqs.py
sudo python3 /home/user/istuftsbusy/probe_reqs.py

