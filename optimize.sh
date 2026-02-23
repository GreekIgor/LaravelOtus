#!/bin/bash

# Скрипт для оптимизации Laravel приложения в Docker
# Использование: ./vendor/bin/sail exec laravel.test bash optimize.sh

echo "🚀 Начинаем оптимизацию Laravel..."

cd /var/www/html || exit

# Кэшируем конфигурацию
echo "📦 Кэшируем конфигурацию..."
php artisan config:cache

# Кэшируем маршруты
echo "🛣️  Кэшируем маршруты..."
php artisan route:cache

# Кэшируем события
echo "📅 Кэшируем события..."
php artisan event:cache

# Оптимизируем автозагрузку Composer
echo "📚 Оптимизируем автозагрузку Composer..."
composer dump-autoload --optimize --classmap-authoritative

# Кэшируем представления
echo "👁️  Кэшируем представления..."
php artisan view:cache

# Оптимизируем приложение
echo "⚡ Оптимизируем приложение..."
php artisan optimize

echo "✅ Оптимизация завершена!"

