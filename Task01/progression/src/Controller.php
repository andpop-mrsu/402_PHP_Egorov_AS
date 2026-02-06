<?php

declare(strict_types=1);

namespace EgorovAS\Progression\Controller;

use function EgorovAS\Progression\View\askAnswer;
use function EgorovAS\Progression\View\askName;
use function EgorovAS\Progression\View\prepareConsole;
use function EgorovAS\Progression\View\showFailure;
use function EgorovAS\Progression\View\showGreeting;
use function EgorovAS\Progression\View\showQuestion;
use function EgorovAS\Progression\View\showSuccess;
use function EgorovAS\Progression\View\showWelcome;

function startGame(): void
{
    prepareConsole();
    showWelcome();
    $name = askName();
    showGreeting($name);
    [$question, $correctAnswer, $fullProgression] = buildRound();

    showQuestion($question);
    $userAnswer = askAnswer();

    if ($userAnswer === (string) $correctAnswer) {
        showSuccess($name);
        return;
    }

    showFailure($userAnswer, (string) $correctAnswer, $fullProgression);
}

function buildRound(): array
{
    $length = 10;
    $start = random_int(1, 30);
    $step = random_int(2, 12);
    $hiddenIndex = random_int(0, $length - 1);

    $progression = [];
    for ($i = 0; $i < $length; $i++) {
        $progression[] = $start + ($i * $step);
    }

    $correctAnswer = $progression[$hiddenIndex];
    $fullProgression = implode(' ', $progression);
    $progression[$hiddenIndex] = '..';

    return [implode(' ', $progression), $correctAnswer, $fullProgression];
}
