#!/bin/sh

DEPLOYFOLDER=/root/server/plastitodo-prod.backhub.net.ar
BACKUPFOLDER=~/Dropbox/backup-backhub.net.ar/plastitodo-prod/images
FILE=`date +"%Y-%m-%d-%H-%M"`-plastitodo_images

mkdir -p $BACKUPFOLDER

tar -zcf ${BACKUPFOLDER}/${FILE}.tar.gz ${DEPLOYFOLDER}/public/images

ls -1t ${BACKUPFOLDER}/*.tar.gz | tail -n +4 | xargs -r rm --

