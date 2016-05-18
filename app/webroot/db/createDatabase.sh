#!/bin/bash
echo Removing old Database
rm -v ./Passmanager.db
echo Create new Database
sqlite3 ./Passmanager.db < ./schema.sql
chmod ug+w ./Passmanager.db
echo To create the User admin with the password q1w2e3! run firstUser.sql