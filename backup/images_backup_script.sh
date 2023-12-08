#!/bin/sh

BACKUPFOLDER=~/Dropbox/backup/
FILE=`date +"%Y-%m-%d-%H-%M"`-plastitodo_images

mkdir -p $BACKUPFOLDER

tar -zcf ${BACKUPFOLDER}${FILE}.tar.gz ../public/images/

