#!/bin/bash
div=5
minH=736
file=`ls appledaily_*.jpg | sed -n 's/.*\([0-9]\{4\}-[0-9]\{1,2\}-[0-9]\{1,2\}_[0-9]\{1,2\}-[0-9]\{2\}-[0-9]\{2\}-[0-9]\{1,3\}\).*/\1/p' | tail -n 1`
im="appledaily_${file}.jpg"

for i in {1..50};do
    identify ${im} && break;
done

w=`identify -format "%[w]" ${im}`
h=`identify -format "%[fx: int(h/${div})]" ${im}`

if [ ${h} -lt ${minH} ]; then
    h=${minH}
    div=$(expr $(identify -format "%[fx: int(h/${h})+1]" ${im}))
fi
echo ${h}, ${div}
for ((i=0; i<${div}; i++));do
    convert ${im} -crop ${w}x${h}+0+`expr ${i} \* ${h}` `printf "image_%s-split%02d.jpg" ${file} ${i}`
done

convert image_${file}-*.jpg -background white -gravity north +append appendApple_${file}.jpg

rm ${im} image_${file}-*.jpg
