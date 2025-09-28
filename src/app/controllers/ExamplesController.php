<?php

require_once __DIR__."/BaseController.php";
require_once __DIR__."/../helpers/FlashMessages.php";
require_once __DIR__."/../examples/EloquentExample.php";

class ExamplesController extends BaseController
{
    public function showIndex(): void
    {
        FlashMessages::addToViews();

        Flight::view()->render('examples/index.latte', [
            'title' => 'Ejemplos de Uso - Eloquent & Laravel Validator',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function runExample(): void
    {
        $request = Flight::request();
        $exampleType = $request->data['example'] ?? '';

        if (empty($exampleType)) {
            FlashMessages::error('Debe especificar un ejemplo a ejecutar');
            Flight::redirect('/examples');
            return;
        }

        try {
            $result = $this->executeExample($exampleType);

            if ($result['success']) {
                FlashMessages::success($result['message']);

                // Guardar resultado en sesión para mostrar en la vista
                $_SESSION['example_result'] = [
                    'type' => $exampleType,
                    'data' => $result,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            } else {
                FlashMessages::error($result['message']);
                if (isset($result['errors'])) {
                    foreach ($result['errors'] as $field => $errors) {
                        if (is_array($errors)) {
                            FlashMessages::warning("$field: " . implode(', ', $errors));
                        } else {
                            FlashMessages::warning("$field: $errors");
                        }
                    }
                }
            }

        } catch (Exception $e) {
            FlashMessages::error("Error ejecutando ejemplo: " . $e->getMessage());
        }

        Flight::redirect('/examples');
    }

    public function apiExample(): void
    {
        $request = Flight::request();
        $exampleType = $request->query['example'] ?? '';

        // Configurar respuesta JSON
        Flight::response()->header('Content-Type', 'application/json');

        if (empty($exampleType)) {
            Flight::json([
                'success' => false,
                'message' => 'Parámetro "example" requerido',
                'available_examples' => $this->getAvailableExamples()
            ], 400);
            return;
        }

        try {
            $result = $this->executeExample($exampleType);
            Flight::json($result);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error ejecutando ejemplo'
            ], 500);
        }
    }

    public function getAllExamples(): void
    {
        Flight::response()->header('Content-Type', 'application/json');

        try {
            $result = EloquentExample::runAllExamples();
            Flight::json($result);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error ejecutando todos los ejemplos'
            ], 500);
        }
    }

    private function executeExample(string $exampleType): array
    {
        switch ($exampleType) {
            case 'create_user':
                return EloquentExample::createUserExample();

            case 'authenticate':
                return EloquentExample::authenticateUserExample('demo', 'demo');

            case 'search_users':
                $searchTerm = Flight::request()->data['search_term'] ?? 'demo';
                $page = (int) (Flight::request()->data['page'] ?? 1);
                return EloquentExample::searchUsersExample($searchTerm, $page);

            case 'multiple_connections':
                return EloquentExample::multipleConnectionsExample();

            case 'transaction':
                return EloquentExample::transactionExample();

            case 'advanced_validation':
                return EloquentExample::advancedValidationExample();

            case 'system_stats':
                return EloquentExample::systemStatsExample();

            case 'all':
                return EloquentExample::runAllExamples();

            default:
                return [
                    'success' => false,
                    'message' => 'Ejemplo no válido',
                    'available_examples' => $this->getAvailableExamples()
                ];
        }
    }

    private function getAvailableExamples(): array
    {
        return [
            'create_user' => 'Crear usuario con validación',
            'authenticate' => 'Autenticar usuario',
            'search_users' => 'Buscar usuarios con filtros',
            'multiple_connections' => 'Múltiples conexiones de BD',
            'transaction' => 'Transacciones con Eloquent',
            'advanced_validation' => 'Validaciones avanzadas',
            'system_stats' => 'Estadísticas del sistema',
            'all' => 'Ejecutar todos los ejemplos'
        ];
    }

    public function documentation(): void
    {
        FlashMessages::addToViews();

        Flight::view()->render('examples/documentation.latte', [
            'title' => 'Documentación - Eloquent & Laravel Validator',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated'),
            'examples' => $this->getAvailableExamples()
        ]);
    }
}