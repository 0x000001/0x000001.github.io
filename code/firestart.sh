#!/bin/sh
while [ 1 ]
do
  killall -HUP firefox
  firefox --private-window >/dev/null 2>&1 &
  sleep 60m
done
#####
#firestart:firefox with vagex restart per hour 
