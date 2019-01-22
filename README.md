# Rasbperry pi "carputer" (car-pi) - WIP
My current dashcam has failed me and I can't find one for a reasonable price with features I want.

I decided to build one with a raspberrypi and a picamera. Added benefits of basically having a computer in my car are nice too.
My biggest issue was graceful shutdown and power managment.
Scripts for my raspberry pi 3 in my car. Includes dashcam and an access point for media playback via wifi.
Also has a simple webUI for viewing, converting, and downloading video files from the dashcam.

# Scripts
TODO

# Setup
To setup in an automated fashion:
```
sudo ./setup.sh
```

# Wiring
View or edit the schematic (.cddx file) on [Circuit Editor](https://www.circuit-diagram.org/)

This is a simple schematic to handle the delayed shutdown of the pi. It uses a constant 12V line and a switched 12V line to signal to the pi to shutdown, the timed relay then cuts power after however many seconds you set (I use 30 seconds). The `power-control.py` script is what watches for the GPIO pins for the shutdown signal.

![alt text](https://raw.githubusercontent.com/mjohnmadison/car-pi/master/circuit.png)

# Supplies needed/used:
TODO - Add links
* Raspberry Pi 3 and large Class 10 SD card for it.
* Raspberry Pi Camera Module V2-8 Megapixel,1080p
* Any 87a automotive relay (with connector for easier installation)
* DC to DC Converter Module 12V/24V to 5V 3A
* DS3231 RTC Real Time Clock Module
* DC 12V Programmable Multi-function Time Delay Relay
* CSI to HDMI Cable Extension Module (optional but very handy)
* A steady hand, some basic electrical knowledge and supplies (multimeter, nuts, wire, etc)
* Add-A-Circuit Fuse TAP Adapter Fuse Holder (Also optional but makes wiring easy in the car)
 
