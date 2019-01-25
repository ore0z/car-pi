#!/usr/bin/env python
import picamera
import datetime as dt
import time
import subprocess
import os

#time.sleep(10) # Wait a bit for system to finish booting

wait = 300 #Duration (seconds) of each video file
ext = '.h264' #RAW video file extention

with picamera.PiCamera() as camera:
    #camera.start_preview() # Testing only
    camera.resolution = (1640, 922) # Best resolution for pi cam v2 at full FOV
    #camera.resolution = (1280, 720) # Not full FOV
    camera.framerate = 30
    camera.iso = 800
    camera.exposure_mode = 'auto'
    camera.rotation = 180
    camera.annotate_text_size = 10
    camera.annotate_background = picamera.Color('black')
    ts = dt.datetime.now().strftime('%Y%m%d-%H%M%S')
    filename = ts + ext
    camera.start_recording('/video/' + filename, bitrate=5000000)
    start = dt.datetime.now()
    while (dt.datetime.now() - start).seconds < wait:
        camera.annotate_text = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    camera.wait_recording(0.2)
    # Convert video to mp4
    cmd = ("avconv -loglevel 8 -r 30 \
            -i /video/{} -vcodec copy \
            /video/{}.mp4 && rm /video/{}".format(filename, ts, filename))
    subprocess.Popen(["bash", "-c", cmd])
    while True:
        ts = dt.datetime.now().strftime('%Y%m%d-%H%M%S')
        filename = ts + ext
        camera.split_recording('/video/' + filename)
        start = dt.datetime.now()
        while (dt.datetime.now() - start).seconds < wait:
            camera.annotate_text = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        camera.wait_recording(0.2)
        cmd = ("avconv -loglevel 8 -r 30 \
                -i /video/{} -vcodec copy \
                /video/{}.mp4 && rm /video/{}".format(filename, ts, filename))
        subprocess.Popen(["bash", "-c", cmd])
    camera.stop_recording()
