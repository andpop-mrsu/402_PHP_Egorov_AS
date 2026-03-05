# Task03: SPA + REST API на Slim

В этом задании игра «Арифметическая прогрессия» реализована как Single Page Application.

- Frontend: `public/index.html` + `public/app.js` + `public/styles.css`
- Backend: `public/index.php` на Slim
- База данных: SQLite `db/progression.sqlite`

## Что реализовано

- `GET /games` — список всех игр из БД;
- `GET /games/{id}` — данные игры и ее ходов;
- `POST /games` — создание новой игры, возврат `id`;
- `POST /step/{id}` — сохранение хода по игре.

## Установка и запуск

1. Перейти в каталог задания:

```bash
cd Task03
```

2. Установить зависимости (Slim):

```bash
composer install
```

3. Запустить встроенный сервер PHP:

```bash
php -S localhost:3000 -t public
```

4. Открыть SPA:

- `http://localhost:3000/`

## Быстрая проверка REST API (PowerShell)

Примеры запросов (в новом окне PowerShell, пока сервер запущен):

```powershell
# Создать новую игру
$game = Invoke-RestMethod -Uri "http://localhost:3000/index.php/games" -Method Post -ContentType "application/json" -Body '{"player_name":"Anton"}'
$game

# Отправить ход
Invoke-RestMethod -Uri ("http://localhost:3000/index.php/step/" + $game.id) -Method Post -ContentType "application/json" -Body '{"answer":"10"}'

# Список игр
Invoke-RestMethod -Uri "http://localhost:3000/index.php/games" -Method Get

# Ходы конкретной игры
Invoke-RestMethod -Uri ("http://localhost:3000/index.php/games/" + $game.id) -Method Get
```

## Структура

- `public/` — публичные файлы сайта, включая единственную PHP-точку входа `index.php`;
- `src/` — бизнес-логика игры и работа с SQLite;
- `db/` — каталог базы данных.
