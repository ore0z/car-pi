#!/usr/bin/env python
# Script to watch for signal from the car to shutdown
# Second timed relay will actually cut power to the pi

import RPi.GPIO as GPIO
import os
import time

pin=27 #GPIO pin, not physical pin number
GPIO.setmode(GPIO.BCM)
GPIO.setup(pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
if GPIO.input(pin) == 0: # Car has turned off before script starts so shutdown pi
    os.system("shutdown -h now")
    quit()

# Wait for event
while True:
    GPIO.wait_for_edge(pin, GPIO.FALLING)
    time.sleep(10)
    if GPIO.input(pin) == 0:
        break

os.system("shutdown -h now")
quit()
