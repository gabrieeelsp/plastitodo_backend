#!/bin/sh

DEPLOYFOLDER=/root/plastitodo/deploy-2023-12-08/plastitodo_backend
BACKUPFOLDER=~/Dropbox/backup/plastitodo-prod/images
FILE=`date +"%Y-%m-%d-%H-%M"`-plastitodo_images

mkdir -p $BACKUPFOLDER

tar -zcf ${BACKUPFOLDER}/${FILE}.tar.gz ${DEPLOYFOLDER}/public/images/
