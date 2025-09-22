# Настройка Nginx для загрузки файлов

## Проблема
Ошибка 413 (Content Too Large) при загрузке видео файлов из-за ограничений Nginx.

## Решение

### 1. Обновить конфигурацию Nginx для фронтенда (chat.ap.kz)

Скопировать файл `chat.ap.kz/nginx.conf` в директорию конфигурации Nginx:

```bash
sudo cp /var/www/user/data/www/chat.ap.kz/nginx.conf /etc/nginx/sites-available/chat.ap.kz
sudo ln -sf /etc/nginx/sites-available/chat.ap.kz /etc/nginx/sites-enabled/chat.ap.kz
```

### 2. Создать конфигурацию Nginx для бэкенда (back-chat.ap.kz)

Скопировать файл `back-chat.ap.kz/nginx.conf` в директорию конфигурации Nginx:

```bash
sudo cp /var/www/user/data/www/back-chat.ap.kz/nginx.conf /etc/nginx/sites-available/back-chat.ap.kz
sudo ln -sf /etc/nginx/sites-available/back-chat.ap.kz /etc/nginx/sites-enabled/back-chat.ap.kz
```

### 3. Проверить версию PHP-FPM

Убедиться, что в конфигурации бэкенда указана правильная версия PHP-FPM:

```bash
# Проверить доступные версии
ls /var/run/php/

# Обновить в nginx.conf если нужно
fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
```

### 4. Проверить конфигурацию Nginx

```bash
sudo nginx -t
```

Если есть ошибки - исправить их.

### 5. Перезагрузить Nginx

```bash
sudo systemctl reload nginx
# или
sudo nginx -s reload
```

### 6. Перезапустить PHP-FPM (если нужно)

```bash
sudo systemctl restart php8.2-fpm
```

### 7. Проверить статус сервисов

```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
```

## Проверка настроек

### Проверить текущие настройки PHP:
```bash
php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"
```

### Проверить работу через curl:
```bash
# Создать тестовый файл 15MB
dd if=/dev/zero of=/tmp/test_15mb.bin bs=1M count=15

# Тест загрузки
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@/tmp/test_15mb.bin" \
  -F "message=Test 15MB file" \
  https://chat.ap.kz/api/chats/1/send
```

## Настройки в конфигурациях

### Nginx (фронтенд chat.ap.kz):
- `client_max_body_size 20M` - глобально и в location /api/
- `client_body_timeout 120s`
- `proxy_request_buffering off` - для передачи больших файлов
- Увеличенные таймауты прокси

### Nginx (бэкенд back-chat.ap.kz):
- `client_max_body_size 20M` - глобально
- `fastcgi_request_buffering off` - отключение буферизации для PHP
- CORS заголовки для API
- Увеличенные таймауты FastCGI

### PHP (.user.ini):
- `upload_max_filesize = 16M`
- `post_max_size = 20M`
- `memory_limit = 256M`
- `max_execution_time = 300`

### Laravel (ChatApiController):
- Валидация: `max:16384` (16MB в KB)

## Логи для отладки

```bash
# Nginx логи
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/chat.ap.kz.error.log
sudo tail -f /var/log/nginx/back-chat.ap.kz.error.log

# PHP логи
sudo tail -f /var/log/php8.2-fpm.log

# Laravel логи
tail -f /var/www/user/data/www/back-chat.ap.kz/storage/logs/laravel.log
```

## Возможные ошибки

| Ошибка | Причина | Решение |
|--------|---------|---------|
| 413 Request Entity Too Large | Недостаточный `client_max_body_size` | Увеличить в Nginx |
| 504 Gateway Timeout | Недостаточные таймауты | Увеличить `proxy_read_timeout`, `fastcgi_read_timeout` |
| 502 Bad Gateway | PHP-FPM не работает | Запустить/перезапустить PHP-FPM |
| CORS error | Отсутствуют CORS заголовки | Добавить в конфигурацию бэкенда |

## После применения

1. ✅ Обновлена конфигурация Nginx для фронтенда
2. ✅ Создана конфигурация Nginx для бэкенда
3. ⏳ Скопировать конфигурации в /etc/nginx/sites-available/
4. ⏳ Проверить и перезагрузить Nginx
5. ⏳ Протестировать загрузку файлов
