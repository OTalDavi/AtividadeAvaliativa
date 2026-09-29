<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$missoes = [
    ['id' => 1, 'nome' => 'Apollo 11', 'ano' => 1969, 'agencia' => 'NASA', 'status' => 'Concluída'],
    ['id' => 2, 'nome' => 'Mars Pathfinder', 'ano' => 1997, 'agencia' => 'JPL', 'status' => 'Em andamento'],
    ['id' => 3, 'nome' => 'Europa Clipper', 'ano' => 2024, 'agencia' => 'NASA', 'status' => 'Planejada'],
    ['id' => 4, 'nome' => 'James Webb Space Telescope', 'ano' => 2021, 'agencia' => 'NASA/ESA/CSA', 'status' => 'Em andamento'],
    ['id' => 5, 'nome' => 'Artemis I', 'ano' => 2022, 'agencia' => 'NASA', 'status' => 'Planejada'],
    ['id' => 6, 'nome' => 'Mars 2020 (Perseverance Rover)', 'ano' => 2020, 'agencia' => 'NASA', 'status' => 'Em andamento'],
    ['id' => 7, 'nome' => 'TESS (Transiting Exoplanet Survey Satellite)', 'ano' => 2018, 'agencia' => 'NASA', 'status' => 'Em andamento'],
    ['id' => 8, 'nome' => 'Rosetta', 'ano' => 2004, 'agencia' => 'ESA', 'status' => 'Concluída'],
    ['id' => 9, 'nome' => 'Hubble Space Telescope', 'ano' => 1990, 'agencia' => 'NASA/ESA', 'status' => 'Em andamento'],
    ['id' => 10, 'nome' => 'Voyager 1', 'ano' => 1977, 'agencia' => 'NASA', 'status' => 'Em andamento']
];

$app->get('/status', function ($request, $response) {
    $response->getBody()->write(
        json_encode(['status' => 'ok'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/status/xml', function ($request, $response) {
    $response->getBody()->write(
        '<status>ok</status>'
    );

    return $response
        ->withHeader('Content-Type', 'application/xml')
        ->withStatus(200);
});

$app->get('/status/plain', function ($request, $response) {
    $response->getBody()->write(
        'Status: ok'
    );

    return $response
        ->withHeader('Content-Type', 'text/plain')
        ->withStatus(200);
});

$app->get('/', function ($request, $response) {
    $response->getBody()->write(
        json_encode([
            'api' => 'Missões Espaciais',
            'rotas' => ['/status', '/status/xml', '/status/plain', '/missoes', '/missoes/{id}']
        ])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/missoes', function ($request, $response) use (&$missoes) {
    $response->getBody()->write(
        json_encode($missoes)
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/missoes/{id}', function ($request, $response, $args) use (&$missoes) {

    $id = (int) $args['id'];

    foreach ($missoes as $missao) {
        if ($missao['id'] === $id) {

            $response->getBody()->write(
                json_encode($missao)
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Missão não encontrada'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

$app->post('/missoes', function ($request, $response) use (&$missoes) {

    $dados = $request->getParsedBody();

    if (!isset($dados['nome']) || empty($dados['nome'])) {

        $response->getBody()->write(
            json_encode(['erro' => 'O nome da missão é obrigatório'])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    $novaMissao = [
        'id' => count($missoes) + 1,
        'nome' => $dados['nome'],
        'ano' => $dados['ano'] ?? null,
        'agencia' => $dados['agencia'] ?? null,
        'status' => $dados['status'] ?? null
    ];

    $missoes[] = $novaMissao;

    $response->getBody()->write(
        json_encode($novaMissao)
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201);
});

$app->put('/missoes/{id}', function ($request, $response, $args) use (&$missoes) {

    $id = (int) $args['id'];
    $dados = $request->getParsedBody();

    foreach ($missoes as &$missao) {

        if ($missao['id'] === $id) {

            if (isset($dados['nome']) && !empty($dados['nome'])) {
                $missao['nome'] = $dados['nome'];
            }

            if (isset($dados['ano']) && !empty($dados['ano'])) {
                $missao['ano'] = $dados['ano'];
            }

            if (isset($dados['agencia']) && !empty($dados['agencia'])) {
                $missao['agencia'] = $dados['agencia'];
            }

            if (isset($dados['status']) && !empty($dados['status'])) {
                $missao['status'] = $dados['status'];
            }

            $response->getBody()->write(
                json_encode($missao)
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Missão não encontrada'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

$app->delete('/missoes/{id}', function ($request, $response, $args) use (&$missoes) {

    $id = (int) $args['id'];

    foreach ($missoes as $indice => $missao) {

        if ($missao['id'] === $id) {

            unset($missoes[$indice]);

            return $response->withStatus(204);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Missão não encontrada'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

$app->run();