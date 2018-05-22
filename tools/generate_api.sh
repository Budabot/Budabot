cd ..
php ./tools/apigen.phar --version
php ./tools/apigen.phar generate --access-levels=public,protected,private --title="Budabot 4.Beta Docs" --source=./core,./modules -d ./docs/api --debug
