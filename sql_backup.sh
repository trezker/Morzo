#!/bin/sh


if [ $# -eq 0 ]
then
	echo "Specify what to backup."
	echo "structure, initdata or alldata."
else
	if [ $1 = "structure" ]
	then
		echo "This backup requires root access";
		mysqldump --no-data --events -u root -p morzo > database_structure.sql
	elif [ $1 = "initdata" ]
	then
		echo "This backup requires game database access";
		mysqldump --skip-triggers --compact --no-create-info -u morzo -p morzo Access Count Language Translation > database_initial_data.sql
	elif [ $1 = "alldata" ]
	then
		echo "This backup requires game database access";
		mysqldump --skip-triggers --compact --no-create-info -u morzo -p morzo > database_all_data.sql
	else
		echo "Not a valid backup name."
		echo "structure, initdata or alldata."
	fi
fi
