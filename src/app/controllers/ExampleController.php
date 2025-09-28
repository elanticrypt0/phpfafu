<?php

require_once __DIR__."/BaseController.php";
require_once __DIR__."/../helpers/FlashMessages.php";
require_once __DIR__."/../helpers/FormValidator.php";

class ExampleController extends BaseController
{
    public function showRegistrationForm(): void
    {
        FlashMessages::addToViews();

        Flight::view()->render('examples/registration.latte', [
            'title' => 'Ejemplo de Registro',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function processRegistration(): void
    {
        $request = Flight::request();
        $data = [
            'username' => $request->data['username'] ?? '',
            'email' => $request->data['email'] ?? '',
            'password' => $request->data['password'] ?? '',
            'confirm_password' => $request->data['confirm_password'] ?? '',
            'age' => $request->data['age'] ?? '',
            'website' => $request->data['website'] ?? ''
        ];

        // Crear validador personalizado
        $validator = FormValidator::make($data);

        // Validar campos individuales
        $validator
            ->validate('username', FormValidator::rules()->username(),
                'El usuario debe tener entre 3 y 20 caracteres alfanuméricos')
            ->validate('email', FormValidator::rules()->email(),
                'Debe ser un email válido')
            ->validate('password', FormValidator::rules()->strongPassword(),
                'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número')
            ->validate('age', FormValidator::rules()->numeric(),
                'La edad debe ser numérica');

        // Validación personalizada para confirmar contraseña
        if ($data['password'] !== $data['confirm_password']) {
            $validator->addError('confirm_password', 'Las contraseñas no coinciden');
        }

        // Validación opcional para website
        if (!empty($data['website'])) {
            $validator->validate('website', \Respect\Validation\Validator::url(),
                'El sitio web debe ser una URL válida');
        }

        // Verificar si hay errores
        if ($validator->fails()) {
            // Agregar mensaje flash de error general
            FlashMessages::error('Por favor corrige los errores en el formulario');

            // También podríamos agregar cada error individual
            foreach ($validator->getErrors() as $field => $error) {
                FlashMessages::warning("Campo {$field}: {$error}");
            }

            Flight::redirect('/examples/registration');
            return;
        }

        // Si la validación pasa, mostrar éxito
        FlashMessages::success('¡Registro completado exitosamente!');
        FlashMessages::info('Los datos validados han sido procesados correctamente');

        // En una aplicación real, aquí guardarías los datos
        // $this->saveUser($validator->getValidatedData());

        Flight::redirect('/examples/registration');
    }

    public function showContactForm(): void
    {
        FlashMessages::addToViews();

        Flight::view()->render('examples/contact.latte', [
            'title' => 'Formulario de Contacto',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function processContact(): void
    {
        $request = Flight::request();
        $data = [
            'name' => $request->data['name'] ?? '',
            'email' => $request->data['email'] ?? '',
            'subject' => $request->data['subject'] ?? '',
            'message' => $request->data['message'] ?? ''
        ];

        // Usar validateMultiple para múltiples validaciones
        $validator = FormValidator::make($data)->validateMultiple([
            'name' => [
                'rules' => \Respect\Validation\Validator::length(2, 50)->alpha(' '),
                'message' => 'El nombre debe tener entre 2 y 50 caracteres (solo letras y espacios)'
            ],
            'email' => [
                'rules' => FormValidator::rules()->email(),
                'message' => 'Debe ser un email válido'
            ],
            'subject' => [
                'rules' => \Respect\Validation\Validator::length(5, 100),
                'message' => 'El asunto debe tener entre 5 y 100 caracteres'
            ],
            'message' => [
                'rules' => \Respect\Validation\Validator::length(10, 1000),
                'message' => 'El mensaje debe tener entre 10 y 1000 caracteres'
            ]
        ]);

        if ($validator->fails()) {
            FlashMessages::error('Hay errores en el formulario de contacto');

            // Mostrar solo el primer error para mantenerlo simple
            FlashMessages::warning($validator->getFirstError());

            Flight::redirect('/examples/contact');
            return;
        }

        // Simular envío exitoso
        FlashMessages::success('¡Mensaje enviado correctamente!');
        FlashMessages::info('Te responderemos pronto a ' . $data['email']);

        Flight::redirect('/examples/contact');
    }

    public function showValidationDemo(): void
    {
        FlashMessages::addToViews();

        Flight::view()->render('examples/validation-demo.latte', [
            'title' => 'Demo de Validaciones',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function processValidationDemo(): void
    {
        $request = Flight::request();
        $action = $request->data['action'] ?? '';

        switch ($action) {
            case 'success':
                FlashMessages::success('¡Operación exitosa! Todo funcionó correctamente.');
                break;

            case 'error':
                FlashMessages::error('Error: Algo salió mal en la operación.');
                break;

            case 'warning':
                FlashMessages::warning('Advertencia: Revisa los datos antes de continuar.');
                break;

            case 'info':
                FlashMessages::info('Información: Esta es una notificación informativa.');
                break;

            case 'multiple':
                FlashMessages::success('Primer mensaje de éxito');
                FlashMessages::info('Mensaje informativo adicional');
                FlashMessages::warning('Una pequeña advertencia');
                break;

            default:
                FlashMessages::error('Acción no válida');
        }

        Flight::redirect('/examples/validation-demo');
    }
}