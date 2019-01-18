#!/usr/bin/env bash

sleep 5
used=$(df --output=pcent /video | tail -1 | tr -d ' %')
if [[ ${used} > 95 ]]; then
	find /video -type f | head -5 | xargs rm
fi
