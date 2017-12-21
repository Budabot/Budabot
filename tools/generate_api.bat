cd ..
.\win32\php -c php-win.ini ./tools/apigen.phar --version
.\win32\php -c php-win.ini ./tools/apigen.phar generate --access-levels=public,protected,private --title="Budabot 3.5_GA Docs" --source=./core,./modules -d ./docs/api --debug
pause