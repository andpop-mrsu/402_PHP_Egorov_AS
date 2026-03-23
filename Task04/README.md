# Task04: SPA + REST API на Laravel

Игра «Арифметическая прогрессия» из Task03 перенесена на фреймворк Laravel.

## Что реализовано

- `GET /` — SPA интерфейс игры;
- `GET /games` — список всех игр;
- `GET /games/{id}` — информация об игре и её ходах;
- `POST /games` — создать новую игру;
- `POST /step/{id}` — отправить ответ для игры.

База данных: SQLite-файл `database/database.sqlite`.

## Требования

- PHP 8.2+
- Composer
- SQLite
- PHP-расширения `pdo_sqlite` и `fileinfo`
- GNU Make (для `make install`)

## Установка

В Linux установка выполняется одной командой:

```bash
cd Task04
make install
```

Команда `make install` автоматически:

- устанавливает зависимости (`composer install`);
- создаёт `.env` из `.env.example` (если нет файла);
- создаёт `database/database.sqlite`;
- генерирует `APP_KEY`;
- выполняет миграции.

## Запуск

```bash
cd Task04
php artisan serve
```

Открыть в браузере:

- `http://localhost:8000/`

## Быстрая проверка API (PowerShell)

```powershell
# Создать новую игру
$game = Invoke-RestMethod -Uri "http://localhost:8000/games" -Method Post -ContentType "application/json" -Body '{"player_name":"Anton"}'
$game

# Отправить ход
Invoke-RestMethod -Uri ("http://localhost:8000/step/" + $game.id) -Method Post -ContentType "application/json" -Body '{"answer":"10"}'

# Получить список игр
Invoke-RestMethod -Uri "http://localhost:8000/games" -Method Get

# Получить ходы конкретной игры
Invoke-RestMethod -Uri ("http://localhost:8000/games/" + $game.id) -Method Get
```
