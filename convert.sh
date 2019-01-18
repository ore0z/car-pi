#!/usr/bin/env bash

# Find all h264 files and convert them

for i in $(find /video -type f -name "*.h264"); do
	file=$(echo $i|awk -F '.h264' '{print $1}')
	# Try to convert any left over files from last shutdown. If any fail to convert, move to /tmp
	avconv -y -loglevel 8 -r 30 -i ${file}.h264 -vcodec copy ${file}.mp4 && rm -rf ${file}.h264 || mv ${file} /video/failed/${file}
done
