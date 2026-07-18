#!/bin/bash

echo "=== Сброс кэша Yii2 ==="

# Очистка runtime-кэшей
rm -rf /var/www/it-sochi/frontend/runtime/cache/*
rm -rf /var/www/it-sochi/backend/runtime/cache/*
rm -rf /var/www/it-sochi/frontend/runtime/debug/*
rm -rf /var/www/it-sochi/backend/runtime/debug/*

# Очистка assets
rm -rf /var/www/it-sochi/frontend/web/assets/*
rm -rf /var/www/it-sochi/backend/web/assets/*

echo "Кэш Yii2 очищен"

# Перезапуск PHP-FPM
#PHP_FPM=$(systemctl list-units --type=service --state=running | grep -oE 'php[0-9.]+-fpm' | head -1)
#if [ -n "$PHP_FPM" ]; then
#    systemctl restart $PHP_FPM
#    echo "Перезапущен: $PHP_FPM"
#else
#    systemctl restart php-fpm 2>/dev/null || echo "PHP-FPM не найден"
#fi
sudo systemctl restart php8.4-fpm
echo "Перезапущен: php8.4-fpm"

# Перезапуск nginx
sudo systemctl restart nginx
echo "Nginx перезапущен"

echo "=== Готово! ==="
