#!/usr/bin/env bash
count=`ps -fe|grep "index.php"|grep -v "grep"|grep "task"|grep "start"|wc -l`

echo $count
if [ $count -lt 1 ]; then
ps -eaf|grep "index.php"|grep -v "grep"|grep "task"|grep "start"|awk '{print $2}'|xargs kill -9
sleep 2
ulimit -c unlimited
/usr/bin/php /home/wwwroot/voyage_music/public/index.php task/ser/start
echo "restart";
echo $(date +%Y-%m-%d_%H:%M:%S) "restart" >>/data/log/swoole_restart.log
fi

