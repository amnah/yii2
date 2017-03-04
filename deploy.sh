#!/bin/bash

echo "----------------------------------------------"
echo "-> Start: $(date "+%Y%m%d-%H%M%S")"
echo "----------------------------------------------"
echo "-> Git branch:"
git rev-parse --abbrev-ref HEAD
git pull
echo "----------------------------------------------"
echo "-> Gulp:"
gulp build
echo "----------------------------------------------"
echo "-> Yii build:"
php yii build
echo "----------------------------------------------"
echo "-> Done: $(date "+%Y%m%d-%H%M%S")"
echo "----------------------------------------------"