#!/bin/bash
set -e

# Ждём пока MySQL станет доступен (10 секунд)
sleep 10

# Запускаем миграцию
php /var/www/html/system/library/db_migration/run.php || echo "Миграция пропущена"

# Дальше запускаем стандартный процесс
exec php-fpm -F
