#!/bin/sh
t=0
while [ 1 ]
do
  ps -fe|grep vagex.php|grep -v grep
  if [ $? -ne 0 ]
  then
  echo "start vagex ..."
  php5 vagex.php  >/dev/null 2>&1 & 
  else
  echo "vagex alive"
  fi
  ps -fe|grep vagex-ios.php|grep -v grep
  if [ $? -ne 0 ]
  then
  echo "start vagex-ios ..."
  php5 vagex-ios.php  >/dev/null 2>&1 & 
  else
  echo "vagex-ios alive"
  fi
  ps -fe|grep vagex-win.php|grep -v grep
  if [ $? -ne 0 ]
  then
  echo "start vagex-win ..."
  php5 vagex-win.php  >/dev/null 2>&1 & 
  else
  echo "vagex-win alive"
  fi
  ps -fe|grep vagex-android.php|grep -v grep
  if [ $? -ne 0 ]
  then
  echo "start vagex-android ..."
  php5 vagex-android.php  >/dev/null 2>&1 & 
  else
  echo "vagex-android alive"
  fi


  sleep 30
  t=$(($t+1))

  if [ $t -gt 120 ]
  then
  killall -HUP php5
  rm *log*.txt
  t=0
  fi
done
###keep working