#!/bin/bash

echo "----------------------------------------------"
echo "-> Start: $(date "+%Y/%m/%d %H:%M:%S.%3N")"
echo "----------------------------------------------"
echo "-> Git:   $(date "+%Y/%m/%d %H:%M:%S.%3N")"
echo "Current branch: $(git rev-parse --abbrev-ref HEAD)"
git pull
echo "----------------------------------------------"
echo "-> Gulp:  $(date "+%Y/%m/%d %H:%M:%S.%3N")"
gulp build
echo "----------------------------------------------"
echo "-> Build: $(date "+%Y/%m/%d %H:%M:%S.%3N")"
php yii build
echo "----------------------------------------------"
echo "-> Done:  $(date "+%Y/%m/%d %H:%M:%S.%3N")"
echo "----------------------------------------------"