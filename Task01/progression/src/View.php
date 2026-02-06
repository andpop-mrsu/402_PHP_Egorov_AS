<?php

declare(strict_types=1);

namespace EgorovAS\Progression\View;

function prepareConsole(): void
{
    ini_set('default_charset', 'UTF-8');
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
    }

    if (PHP_OS_FAMILY === 'Windows') {
        if (function_exists('sapi_windows_cp_set')) {
            sapi_windows_cp_set(866);
        }

        if (function_exists('sapi_windows_vt100_support')) {
            sapi_windows_vt100_support(STDOUT, true);
        }
    }
}

function showWelcome(): void
{
    writeLine('Добро пожаловать в игру "Арифметическая прогрессия"!');
    writeLine('Найдите пропущенное число в прогрессии.');
}

function askName(): string
{
    while (true) {
        $name = readLine('Как вас зовут');
        if ($name !== '') {
            return $name;
        }
    }
}

function showGreeting(string $name): void
{
    writeLine('Привет, %s!', $name);
}

function showQuestion(string $question): void
{
    writeLine('Вопрос: %s', $question);
}

function askAnswer(): string
{
    return readLine('Ваш ответ');
}

function showSuccess(string $name): void
{
    writeLine('Верно!');
    writeLine('Поздравляем, %s!', $name);
}

function showFailure(string $userAnswer, string $correctAnswer, string $progression): void
{
    writeLine("'%s' - неверный ответ ;(. Правильный ответ: '%s'.", $userAnswer, $correctAnswer);
    writeLine('Полная прогрессия: %s', $progression);
}

function readLine(string $label): string
{
    echo toConsoleEncoding($label . ': ');

    $input = fgets(STDIN);

    if ($input === false) {
        return '';
    }

    return trim(fromConsoleEncoding($input));
}

function writeLine(string $format, ...$args): void
{
    $text = $args === [] ? $format : vsprintf($format, $args);
    echo toConsoleEncoding($text) . PHP_EOL;
}

function fromConsoleEncoding(string $input): string
{
    $encoding = getConsoleEncoding();
    if ($encoding === 'UTF-8') {
        return $input;
    }

    return (string) mb_convert_encoding($input, 'UTF-8', $encoding);
}

function toConsoleEncoding(string $text): string
{
    $encoding = getConsoleEncoding();
    if ($encoding === 'UTF-8') {
        return $text;
    }

    return (string) mb_convert_encoding($text, $encoding, 'UTF-8');
}

function getConsoleEncoding(): string
{
    if (PHP_OS_FAMILY !== 'Windows') {
        return 'UTF-8';
    }

    if (function_exists('sapi_windows_cp_get')) {
        $codePage = sapi_windows_cp_get();
        return $codePage === 65001 ? 'UTF-8' : 'CP' . $codePage;
    }

    return 'CP866';
}
