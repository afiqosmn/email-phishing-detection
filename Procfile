web: php artisan migrate --force && php artisan optimize && php -S 0.0.0.0:$PORT -t public
worker: php artisan queue:work -q emails -q analysis -q default --verbose --tries=3 --timeout=90 --max-time=3600
scheduler: php artisan schedule:work
