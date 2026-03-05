<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Game.php';
require_once __DIR__ . '/../src/Database.php';

use EgorovAS\Task03\Database;
use EgorovAS\Task03\Game;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$frontendHandler = static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $file = __DIR__ . '/index.html';
    if (!is_file($file)) {
        return jsonResponse($response, ['error' => 'Frontend file index.html not found'], 500);
    }

    $content = file_get_contents($file);
    if ($content === false) {
        return jsonResponse($response, ['error' => 'Failed to read index.html'], 500);
    }

    $response->getBody()->write($content);
    return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
};

$listGamesHandler = static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    try {
        $games = Database\fetchGames();
    } catch (Throwable) {
        return jsonResponse($response, ['error' => 'Database error while fetching games'], 500);
    }

    return jsonResponse($response, $games);
};

$getGameHandler = static function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    $gameId = (int) ($args['id'] ?? 0);

    try {
        $game = Database\fetchGameById($gameId);
        if ($game === null) {
            return jsonResponse($response, ['error' => 'Game not found'], 404);
        }

        $steps = Database\fetchStepsByGameId($gameId);
    } catch (Throwable) {
        return jsonResponse($response, ['error' => 'Database error while fetching game steps'], 500);
    }

    return jsonResponse($response, [
        'game' => [
            'id' => $game['id'],
            'player_name' => $game['player_name'],
            'started_at' => $game['started_at'],
            'finished_at' => $game['finished_at'],
            'status' => $game['status'],
            'result' => $game['result'],
        ],
        'steps' => $steps,
    ]);
};

$createGameHandler = static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    try {
        $payload = parseJsonObject($request);
    } catch (InvalidArgumentException $exception) {
        return jsonResponse($response, ['error' => $exception->getMessage()], 400);
    }

    $playerName = trim((string) ($payload['player_name'] ?? ''));
    if ($playerName === '') {
        return jsonResponse($response, ['error' => 'Field "player_name" is required'], 422);
    }

    $round = Game\generateRound();
    $startedAt = date('Y-m-d H:i:s');

    try {
        $gameId = Database\createGame($playerName, $round, $startedAt);
    } catch (Throwable) {
        return jsonResponse($response, ['error' => 'Database error while creating game'], 500);
    }

    return jsonResponse($response, [
        'id' => $gameId,
        'player_name' => $playerName,
        'started_at' => $startedAt,
        'progression' => $round['masked'],
    ], 201);
};

$createStepHandler = static function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    $gameId = (int) ($args['id'] ?? 0);

    try {
        $game = Database\fetchGameById($gameId);
    } catch (Throwable) {
        return jsonResponse($response, ['error' => 'Database error while reading game'], 500);
    }

    if ($game === null) {
        return jsonResponse($response, ['error' => 'Game not found'], 404);
    }

    if ((string) $game['status'] === 'finished') {
        return jsonResponse($response, ['error' => 'Game is already finished'], 409);
    }

    try {
        $payload = parseJsonObject($request);
    } catch (InvalidArgumentException $exception) {
        return jsonResponse($response, ['error' => $exception->getMessage()], 400);
    }

    $answerRaw = trim((string) ($payload['answer'] ?? ''));
    $answerAsInt = filter_var($answerRaw, FILTER_VALIDATE_INT);
    if ($answerAsInt === false) {
        return jsonResponse($response, ['error' => 'Field "answer" must be an integer'], 422);
    }

    $correctAnswer = (int) $game['current_missing_number'];
    $isCorrect = ((int) $answerAsInt) === $correctAnswer;
    $answeredAt = date('Y-m-d H:i:s');

    try {
        $stepNumber = Database\nextStepNumber($gameId);
        Database\saveStep(
            $gameId,
            $stepNumber,
            $answeredAt,
            (string) $game['current_progression_with_gap'],
            (string) $game['current_progression_full'],
            $correctAnswer,
            $answerRaw,
            $isCorrect
        );
        Database\finishGame($gameId, $isCorrect, $answeredAt);
    } catch (Throwable) {
        return jsonResponse($response, ['error' => 'Database error while saving step'], 500);
    }

    return jsonResponse($response, [
        'game_id' => $gameId,
        'step_number' => $stepNumber,
        'answered_at' => $answeredAt,
        'user_answer' => $answerRaw,
        'correct_answer' => $correctAnswer,
        'is_correct' => $isCorrect,
        'result' => $isCorrect ? 'Верно' : 'Неверно',
        'progression_with_gap' => (string) $game['current_progression_with_gap'],
        'progression_full' => (string) $game['current_progression_full'],
    ]);
};

$app->get('/', $frontendHandler);
$app->get('/index.php', $frontendHandler);
$app->get('/index.php/', $frontendHandler);

$app->get('/games', $listGamesHandler);
$app->get('/index.php/games', $listGamesHandler);

$app->get('/games/{id:[0-9]+}', $getGameHandler);
$app->get('/index.php/games/{id:[0-9]+}', $getGameHandler);

$app->post('/games', $createGameHandler);
$app->post('/index.php/games', $createGameHandler);

$app->post('/step/{id:[0-9]+}', $createStepHandler);
$app->post('/index.php/step/{id:[0-9]+}', $createStepHandler);

$app->run();

function parseJsonObject(ServerRequestInterface $request): array
{
    $rawBody = (string) $request->getBody();
    if ($rawBody === '') {
        return [];
    }

    try {
        $decoded = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        throw new InvalidArgumentException('Invalid JSON body');
    }

    if (!is_array($decoded)) {
        throw new InvalidArgumentException('JSON body must be an object');
    }

    return $decoded;
}

function jsonResponse(ResponseInterface $response, mixed $payload, int $status = 200): ResponseInterface
{
    $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        $response->getBody()->write('{"error":"Failed to encode JSON"}');
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    $response->getBody()->write($encoded);
    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json; charset=utf-8');
}
