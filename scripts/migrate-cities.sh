#!/usr/bin/env bash

if [ $# -eq 0 ]
then
    echo "No arguments supplied";
    echo "Please add argument (-all or file_name)";
    exit 1;
fi

echo "=====Migration Started=====";
if [ -z "$1" ]
then
    echo "Please add argument (-all or file_name)";
    exit 1;
elif [ $1 = "-all" ]
then
    for file in ./sql/*.sql
    do
        mysql -uroot -proot holiday_wish < $file;
        echo "Migration Success for $file";
    done
    exit 0;
else
    mysql -uroot -proot holiday_wish < ./sql/$1.sql;
    echo "Migration Success for $1";
fi
echo "=====Migration Finished=====";
