<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task04: Арифметическая прогрессия (Laravel)</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<main class="page">
    <h1>Игра «Арифметическая прогрессия»</h1>
    <p class="subtitle">SPA + REST API на Laravel и SQLite</p>

    <section class="card">
        <h2>Новая игра</h2>
        <form id="start-form">
            <label for="player_name">Имя игрока</label>
            <input id="player_name" name="player_name" type="text" required>
            <button type="submit">Начать игру</button>
        </form>
    </section>

    <section class="card hidden" id="round-card">
        <h2>Текущий вопрос</h2>
        <p>Найдите пропущенное число:</p>
        <p class="question"><code id="progression-text">...</code></p>
        <form id="answer-form">
            <label for="answer">Ваш ответ</label>
            <input id="answer" name="answer" type="text" autocomplete="off" required>
            <button type="submit">Проверить</button>
        </form>
    </section>

    <section class="card hidden" id="result-card">
        <h2>Результат</h2>
        <div id="result-box"></div>
    </section>

    <section class="card">
        <div class="history-head">
            <h2>История игр</h2>
            <button id="refresh-history" type="button">Обновить</button>
        </div>
        <p class="hint">Кнопка «Ходы» использует маршрут GET /games/{id}</p>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Игрок</th>
                    <th>Старт</th>
                    <th>Завершение</th>
                    <th>Статус</th>
                    <th>Итог</th>
                    <th>Ходов</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="games-body">
                <tr>
                    <td colspan="8">Загрузка...</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card hidden" id="steps-card">
        <h2>Ходы выбранной игры</h2>
        <div id="steps-box"></div>
    </section>

    <section class="card hidden alert" id="error-card"></section>
</main>

<script src="/app.js"></script>
</body>
</html>
