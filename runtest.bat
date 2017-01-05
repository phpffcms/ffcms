@echo off
REM === This script will make autodump database and run selenium server
echo === Add new test1 and test2 users
php console.php main:adduser --login="test1" --email="test1@gmail.com" --password="test1" --role="2"
php console.php main:adduser --login="test2" --email="test2@gmail.com" --password="test2" --role="2"
echo Users is ready!
if NOT EXIST "tests/_data/dump.sql" (
echo === Make database dump
if NOT EXIST "tests/_data" (
mkdir "tests/_data"
)
mysqldump.exe --host="127.0.0.1" --user="mysql" --password="mysql" ffcms > tests/_data/dump.sql
echo Database is dumped into tests/_data/dump.sql
)
echo === Run selenium server
java -Xmx512m -jar selenium.jar