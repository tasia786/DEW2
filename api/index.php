<?php
require_once __DIR__ . '/../src/controller/CampaignController.php';
require_once __DIR__ . '/../src/controller/CrimeController.php';
require_once __DIR__ . '/../src/controller/EmergencyController.php';
require_once __DIR__ . '/../src/controller/SeizureController.php';
require_once __DIR__ . '/../src/util/Response.php';

$routes = [
    'crimes-general'          => [CrimesController::class, 'crimes-general'],
    'crimes-sex'              => [CrimesController::class, 'crimes-sex'],
    'crimes-law'              => [CrimesController::class, 'crimes-law'],
    'crimes-sentences'        => [CrimesController::class, 'crimes-sentences'],
    'criminal-groups'         => [CrimesController::class, 'criminal-groups'],
    'seizures'                => [SeizuresController::class, null],
    'emergencies'             => [EmergencyController::class, null],
    'campaigns-projects'      => [CampaignController::class, 'campaigns-projects'],
    'prevention-activities'   => [CampaignController::class, 'prevention-activities'],
];

$segments = routeSegments();
$table = $segments[0] ?? null;
$id = $segments[1] ?? null;

if ($table === null || $table === '') {
    Response::badRequest('table is required');
}

$routeKey = strtolower($table);
if (!isset($routes[$routeKey])) {
    Response::badRequest('Invalid table; accepted: crimes-general, crimes-sex, crimes-law, crimes-sentences, criminal-groups, seizures, emergencies, campaigns-projects, prevention-activities', 404);
}

if (isset($segments[2])) {
    Response::badRequest('Invalid path; expected api/name-of-table or api/name-of-table/id');
}

[$controllerClass, $resource] = $routes[$routeKey];
$controller = new $controllerClass();

match ($_SERVER['REQUEST_METHOD']) {
    'GET' => $id === null
        ? (isset($_GET['column'])
            ? selectOptions($controller, $resource)
            : executeFilter($controller, $resource))
        : Response::badRequest('Invalid path for filtering; expected api/name-of-table'),
    'DELETE' => $id !== null
        ? deleteResource($controller, $resource, $id)
        : Response::badRequest('id is required in the path; expected api/name-of-table/id'),
    'PATCH' => $id !== null
        ? patchResource($controller, $resource, $id)
        : Response::badRequest('id is required in the path; expected api/name-of-table/id'),
    default => Response::json(['error' => 'Method not allowed'], 405),
};

function executeFilter(object $controller, ?string $resource): void
{
    $resource === null
        ? $controller->executeFilter()
        : $controller->executeFilter($resource);
}

function selectOptions(object $controller, ?string $resource): void
{
    $resource === null
        ? $controller->selectOptions()
        : $controller->selectOptions($resource);
}

function deleteResource(object $controller, ?string $resource, string $id): void
{
    $resource === null
        ? $controller->delete($id)
        : $controller->delete($resource, $id);
}

function patchResource(object $controller, ?string $resource, string $id): void
{
    $resource === null
        ? $controller->patch($id)
        : $controller->patch($resource, $id);
}

function routeSegments(): array
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    if ($scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
        $path = substr($path, strlen($scriptDir));
    }

    if (str_starts_with($path, '/index.php')) {
        $path = substr($path, strlen('/index.php'));
    }

    return array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
}
