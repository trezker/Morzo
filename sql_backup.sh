mysqldump --no-data -u morzo -p morzo > database_structure.sql
mysqldump --skip-triggers --compact --no-create-info -u morzo -p morzo > database_data.sql
