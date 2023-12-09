#!/bin/sh

BACKUPFOLDER=~/Dropbox/backup/
FILE=`date +"%Y-%m-%d-%H-%M"`-plastitodo_db.sql
DBSERVER=localhost
DBNAME=plastitodo_back_v5_db
USER=root
PASS=12345

unalias rm     2> /dev/null
rm ${FILE}     2> /dev/null
rm ${FILE}.gz  2> /dev/null

#docker compose exec db /usr/bin/mysqldump --user=${USER} --password=${PASS} ${DATABASE} > ${FILE}
docker compose exec db /usr/bin/mysqldump -u root --password=12345 ${DBNAME} > ${FILE}
gzip $FILE

mkdir -p $BACKUPFOLDER
mv ${FILE}.gz ${BACKUPFOLDER}${FILE}.gz
echo "${FILE}.gz was created:"
ls -l ${FILE}.gz