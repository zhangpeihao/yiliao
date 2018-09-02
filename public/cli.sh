#!/usr/bin/env bash
original=$1
echo $original
# check whether file is exist
#   if $original de chang du wei 0  huo bu $original bu shi chang gui wenjian
if [ -z $original ] || [ ! -f $original ]; then
	echo "file $original not exist!"
	exit
fi
# check whether file is end of '.mp4' or whether it is h264 encodeing.
# 显示 以 .mp4结尾的文件名字或者 h264编码的文件名字  不需要转换的啊
if [ -n "`echo $1 | sed -n /.mp4\$/p`" ] && [ `ffprobe -show_streams $1 | grep "codec_name=h264"` ]; then
	echo "Don't need convert!"
else
	target=${original%.*}.mp4   #截取文件最长的字符串，比如 文件的名字是  a.html.dds.dd.ddedeers.sd.date.mp4,  在这里值截取   .mp4 以前的字符为target
	tmp=${original%.*}`date +%N`.mp4   # 文件的临时名字
	ffmpeg -i $original -vcodec libx264 -acodec libfaac  -vpre slow  -vpre baseline  -qscale 4 -y  $tmp
	rm $original
	mv $tmp $target
fi