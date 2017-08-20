#!/bin/sh
t=0
while [ 1 ]
do
  ps -fe|grep firefox/firefox|grep -v grep
  if [ $? -ne 0 ]
  then
  echo "start firefox ..."
  firefox --private-window http://www.ebesucher.de/surfbar/nbgls  >/dev/null 2>&1 & 
  #firefox --new-window http://www.ebesucher.de/surfbar/nbgls  >/dev/null 2>&1 &
  else
  echo "firing"
  fi
  sleep 30
  t=$(($t+1))

  if [ $t -gt 120 ]
  then
  killall -HUP firefox
  t=0
  fi
done
###keep working