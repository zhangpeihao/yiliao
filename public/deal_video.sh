#!/usr/bin/env bash
#cmd="docker run -v=`pwd`:/tmp/ffmpeg opencoconut/ffmpeg  -i  $1 -c:v libx264 -strict -2 $2 -y 1>$3 2>&1"
which "ffmpeg" >/dev/null
if [ $? -eq 0 ]
then
ffmpeg -i $1 -c:v libx264 -s 720x1280 $2 1>$3 -y 2>&1
else
docker run -v=`pwd`:/tmp/ffmpeg opencoconut/ffmpeg -i $1 -c:v libx264 -s 1280x720 $2 1>$3 -y 2>&1
fi
#echo $cmd
#result=`$cmd`
#echo $result
#sh -c $cmd
