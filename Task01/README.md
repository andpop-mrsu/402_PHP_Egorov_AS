# Task01: Arithmetic Progression

Консольная игра на PHP: игроку показывается арифметическая прогрессия из 10 чисел, где один элемент заменен на `..`. Нужно ввести пропущенное число.

## Packagist

- https://packagist.org/packages/egorovas/progression

## Структура

- `Task01/progression/src` — модули приложения (`Controller`, `View`)
- `Task01/progression/bin/progression` — запускной скрипт игры
- `Task01/progression/composer.json` — зависимости, автозагрузка и исполняемый файл

## Локальный запуск

```bash
cd Task01/progression
composer install
php bin/progression
```

## Packagist запуск

Установка пакета глобально:

```bash
composer global require egorovas/progression
```

Запуск игры:

```bash
progression
```

Если команда `progression` не найдена, добавьте глобальный `vendor/bin` Composer в `PATH`.
