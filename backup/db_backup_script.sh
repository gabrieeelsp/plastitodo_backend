#!/bin/sh

DEPLOYFOLDER=/root/server/plastitodo-prod.backhub.net.ar
BACKUPFOLDER=~/Dropbox/backup/plastitodo-prod/db
FILE=`date +"%Y-%m-%d-%H-%M"`-plastitodo_db.sql
DBSERVER=localhost
DBNAME=plastitodo_back_v5_db
USER=root
PASS=12345

#unalias rm     2> /dev/null
#rm ${FILE}     2> /dev/null
#rm ${FILE}.gz  2> /dev/null

#docker compose exec db /usr/bin/mysqldump --user=${USER} --password=${PASS} ${DATABASE} > ${FILE}
cd ${DEPLOYFOLDER}/backup
docker compose exec db /usr/bin/mysqldump -u root -p12345 ${DBNAME} > ${FILE} 2>&1
gzip $FILE

mkdir -p $BACKUPFOLDER
rm ${BACKUPFOLDER}/*
#rm *db.sql.gz
mv ${FILE}.gz ${BACKUPFOLDER}/${FILE}.gz
echo "${FILE}.gz was created:"
#ls -l ${FILE}.gz
