#!/usr/bin/env python
# Script to watch for signal from the car to shutdown
# Second timed relay will actually cut power to the pi

import RPi.GPIO as GPIO
import os
import time
from signal import SIGKILL

pin=27 #GPIO pin, not physical pin number
GPIO.setmode(GPIO.BCM)
GPIO.setup(pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
if GPIO.input(pin) == 0: # Car has turned off before script starts so shutdown pi
    os.system("shutdown -h now")
    quit()

# Wait for event
while True:
    GPIO.wait_for_edge(pin, GPIO.FALLING)
    time.sleep(5)
    if GPIO.input(pin) == 0:
        break # Abort shutdown if power restored

def get_pids():
    print("called get_pids")
    pids = [pid for pid in os.listdir('/proc') if pid.isdigit()]
    return pids

for pid in get_pids():
    if 'record-cam.py' in open(os.path.join('/proc', pid, 'cmdline'), 'r').read():
        print("killing {}".format(pid))
        os.kill(int(pid), SIGKILL)

time.sleep(1)
a = 1
while a == 1:
    for pid in get_pids():
        if 'avconv' in open(os.path.join('/proc', pid, 'cmdline'), 'r').read():
            print("Waiting for avconv to finish")
            time.sleep(2)
            a = 1
        else:
            a = 0

os.system("shutdown -h now")
quit()
