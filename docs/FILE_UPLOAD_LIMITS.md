# Ограничения загрузки файлов

## Обзор

Данный документ описывает все ограничения на загрузку файлов в системе чата, включая настройки Nginx, PHP, Laravel и операционной системы.

## Текущие настройки проекта

### Приложение (Laravel)
- **Максимальный размер файла:** 50 МБ
- **Разрешенные типы:**
  - Изображения: JPG, JPEG, PNG, GIF, WebP
  - Видео: MP4, MOV, AVI, MKV, WebM
  - Аудио: MP3, WAV, M4A, OGG
  - Документы: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, CSV, JSON, XML, ZIP, RAR, 7Z

## Ограничения веб-сервера

### 1. Nginx

#### Основные директивы:
```nginx
# Максимальный размер тела запроса (по умолчанию: 1M)
client_max_body_size 55M;

# Таймауты для загрузки
client_body_timeout 60s;
client_header_timeout 60s;

# Размер буфера для тела запроса
client_body_buffer_size 128k;

# Временная директория для больших файлов
client_body_temp_path /tmp/nginx_uploads;
```

#### Рекомендуемые значения для нашего проекта:
```nginx
server {
    # Разрешаем загрузку до 55MB (чуть больше чем лимит приложения)
    client_max_body_size 55M;
    
    # Увеличиваем таймауты для медленных соединений
    client_body_timeout 180s;
    client_header_timeout 60s;
    
    # Оптимизируем буферы
    client_body_buffer_size 2M;
    
    # Прокси настройки (если используется)
    proxy_read_timeout 300s;
    proxy_send_timeout 300s;
}
```

#### Возможные ошибки:
- `413 Request Entity Too Large` - превышен `client_max_body_size`
- `408 Request Time-out` - превышен `client_body_timeout`

### 2. Apache (если используется)

#### Основные директивы:
```apache
# Максимальный размер POST запроса
LimitRequestBody 57671680  # 55MB в байтах

# Таймауты
Timeout 300
```

## Ограничения PHP

### 1. Основные настройки

#### Критически важные параметры:
```ini
# Максимальный размер загружаемого файла
upload_max_filesize = 50M

# Максимальный размер POST данных (должен быть больше upload_max_filesize)
post_max_size = 55M

# Максимальное количество файлов для загрузки одновременно
max_file_uploads = 20

# Лимит памяти (должен быть больше post_max_size)
memory_limit = 256M

# Максимальное время выполнения скрипта
max_execution_time = 300

# Максимальное время ожидания входных данных
max_input_time = 300

# Максимальное количество переменных ввода
max_input_vars = 3000
```

### 2. Способы настройки PHP

#### В файле php.ini:
```ini
upload_max_filesize = 50M
post_max_size = 55M
memory_limit = 256M
max_execution_time = 300
```

#### В .htaccess (Apache):
```apache
php_value upload_max_filesize 50M
php_value post_max_size 20M
php_value memory_limit 256M
php_value max_execution_time 300
```

#### В .user.ini (PHP-FPM):
```ini
upload_max_filesize = 50M
post_max_size = 55M
memory_limit = 256M
max_execution_time = 300
```

### 3. PHP-FPM дополнительные настройки

#### В pool конфигурации:
```ini
# Максимальное время выполнения запроса
request_terminate_timeout = 300s

# Лимиты памяти на процесс
php_admin_value[memory_limit] = 256M
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 55M
```

### 4. Возможные ошибки PHP

| Ошибка | Описание | Решение |
|--------|----------|---------|
| `upload_max_filesize` | Файл превышает лимит | Увеличить `upload_max_filesize` |
| `post_max_size` | POST данные превышают лимит | Увеличить `post_max_size` |
| `memory_limit` | Недостаточно памяти | Увеличить `memory_limit` |
| `max_execution_time` | Скрипт выполняется слишком долго | Увеличить `max_execution_time` |
| `max_file_uploads` | Слишком много файлов | Увеличить `max_file_uploads` |

## Ограничения Laravel

### 1. Валидация
```php
// В контроллере
$request->validate([
    'file' => 'nullable|file|max:51200' // 50MB в KB
]);
```

### 2. Конфигурация filesystems.php
```php
// Нет прямых ограничений размера в Laravel
// Ограничения устанавливаются на уровне валидации
```

## Ограничения операционной системы

### 1. Linux

#### Временные директории:
```bash
# Проверка доступного места в /tmp
df -h /tmp

# Права доступа
chmod 755 /tmp
chown www-data:www-data /tmp/uploads
```

#### Лимиты процессов:
```bash
# Проверка лимитов для пользователя www-data
su - www-data -c 'ulimit -a'

# Настройка в /etc/security/limits.conf
www-data soft nofile 65536
www-data hard nofile 65536
```

### 2. Файловая система

#### Лимиты файловой системы:
- **ext4**: максимальный размер файла ~16TB
- **NTFS**: максимальный размер файла ~16TB
- **FAT32**: максимальный размер файла 4GB (не рекомендуется)

## Рекомендуемая конфигурация

### Для файлов до 50MB (текущая настройка)

#### Nginx:
```nginx
client_max_body_size 55M;
client_body_timeout 120s;
client_body_buffer_size 1M;
```

#### PHP:
```ini
upload_max_filesize = 50M
post_max_size = 55M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
max_file_uploads = 20
```

#### Laravel:
```php
'file' => 'nullable|file|max:51200' // 50MB
```

### Для больших файлов (если потребуется)

#### Для файлов до 100MB:

**Nginx:**
```nginx
client_max_body_size 120M;  # Оставляем для больших файлов
client_body_timeout 300s;
```

**PHP:**
```ini
upload_max_filesize = 100M
post_max_size = 120M
memory_limit = 512M
max_execution_time = 600
```

#### Для файлов до 500MB:

**Nginx:**
```nginx
client_max_body_size 600M;
client_body_timeout 600s;
```

**PHP:**
```ini
upload_max_filesize = 500M
post_max_size = 600M
memory_limit = 1024M
max_execution_time = 1200
```

## Мониторинг и отладка

### 1. Проверка текущих настроек PHP
```php
<?php
phpinfo();
// Или
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . "\n";
echo 'post_max_size: ' . ini_get('post_max_size') . "\n";
echo 'memory_limit: ' . ini_get('memory_limit') . "\n";
echo 'max_execution_time: ' . ini_get('max_execution_time') . "\n";
?>
```

### 2. Логи ошибок

#### Nginx логи:
```bash
tail -f /var/log/nginx/error.log
```

#### PHP логи:
```bash
tail -f /var/log/php/error.log
# или
tail -f /var/log/php-fpm/www-error.log
```

#### Laravel логи:
```bash
tail -f storage/logs/laravel.log
```

### 3. Тестирование загрузки
```bash
# Создание тестового файла 15MB
dd if=/dev/zero of=test_15mb.bin bs=1M count=15

# Загрузка через curl
curl -X POST \
  -F "file=@test_15mb.bin" \
  -F "message=Test upload" \
  https://back-chat.ap.kz/api/chats/1/send
```

## Производительность

### Рекомендации:

1. **Память**: `memory_limit` должен быть в 2-3 раза больше максимального размера файла
2. **Время выполнения**: `max_execution_time` ~20-30 секунд на каждый MB файла
3. **Nginx буферы**: используйте буферизацию для оптимизации
4. **PHP-FPM**: настройте достаточное количество worker процессов

### Оптимизация:
```nginx
# Включение прогрессивной загрузки
client_body_in_file_only clean;
client_body_buffer_size 32K;

# Оптимизация прокси (если используется)
proxy_buffering off;
proxy_request_buffering off;
```

## Безопасность

### Рекомендации:

1. **Валидация типов файлов** на уровне приложения
2. **Антивирусная проверка** загруженных файлов
3. **Ограничение директорий** для загрузки
4. **Проверка содержимого** файлов
5. **Лимиты по пользователям** и времени

### Пример безопасной валидации:
```php
$request->validate([
    'file' => [
        'required',
        'file',
        'max:51200', // 50MB
        'mimes:jpeg,jpg,png,gif,mp4,mov,pdf,doc,docx',
        function ($attribute, $value, $fail) {
            // Дополнительная проверка типа файла
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $value->getPathname());
            finfo_close($finfo);
            
            $allowedTypes = ['image/jpeg', 'image/png', 'video/mp4'];
            if (!in_array($mimeType, $allowedTypes)) {
                $fail('Недопустимый тип файла.');
            }
        }
    ]
]);
```

## Заключение

Для стабильной работы с файлами до 50MB в нашем проекте необходимо:

1. ✅ Nginx: `client_max_body_size 55M`
2. ✅ PHP: `upload_max_filesize = 50M`, `post_max_size = 55M`
3. ✅ Laravel: валидация `max:16384`
4. ✅ Мониторинг логов и производительности

Все настройки должны быть согласованы между собой, где каждый следующий уровень должен иметь лимит больше или равный предыдущему.
