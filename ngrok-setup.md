# Настройка Ngrok

## Установка Ngrok

1. Скачайте ngrok с официального сайта: https://ngrok.com/download
2. Распакуйте архив
3. Добавьте ngrok в PATH или переместите в `/usr/local/bin/`

## Запуск Ngrok для Laravel API

```bash
ngrok http 8000
```

Это создаст туннель для Laravel сервера, работающего на `127.0.0.1:8000`.

## Запуск Ngrok для Frontend

```bash
ngrok http 4040
```

Это создаст туннель для фронтенда, работающего на `localhost:5173`.

## Обновление Webhook URL

После запуска ngrok обновите следующие настройки в файле `.env`:

```
APP_URL=https://ваш-ngrok-url.ngrok-free.app
FORCE_HTTPS=true
WAZZUP24_WEBHOOK_URL=https://ваш-ngrok-url.ngrok-free.app/api/webhook
```

## Настройка HTTPS

Проект настроен для работы с HTTPS через ngrok:

1. Vite конфигурация обновлена для HTTPS
2. Laravel middleware ForceHttps добавлен
3. Конфигурация приложения настроена для принудительного HTTPS

## Полезные команды

- Просмотр активных туннелей: `ngrok status`
- Остановка ngrok: `Ctrl+C`
- Запуск с кастомным доменом: `ngrok http 8000 --subdomain=your-subdomain`
