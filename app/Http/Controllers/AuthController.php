<?php
namespace App\Http\Controllers;
use App\Http\Requests\EliminarCuentaRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Notifications\PasswordResetNotification;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SuscripcionesController;
use Illuminate\Support\Facades\Cookie; 

class AuthController extends Controller
{
    protected $suscripcionesController;
    public function __construct(SuscripcionesController $suscripcionesController)
    {
        $this->suscripcionesController = $suscripcionesController;
    }
    public function logout(Request $request)
    {
        $user = auth()->user(); // Accede al usuario autenticado actualmente
    
        // Elimina todos los tokens asociados con el usuario
        $user->tokens()->delete();
        Cookie::queue(Cookie::forget('forstai_session'));

        return response()->json([
            'message' => 'Cierre de sesión exitoso'
        ]);
    }
    public function registro(RegistroRequest $request)
    {
        try {
            // Crear un nuevo usuario
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password'))
            ]);
    
            // Obtener la dirección IP del usuario desde el encabezado 'X-Forwarded-For' o directamente desde la solicitud
            $clientIp = $request->header('X-Forwarded-For') ?? $request->ip();
    
            // Guardar la dirección IP del usuario
            $user->ip_address = $clientIp;
            $user->save();
    
            // Retornar el token de autenticación y la información del usuario
            return [
                'token' => $user->createToken('token')->plainTextToken,
                'user' => $user
            ];
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante el proceso de registro
            return response()->json(['error' => 'Error al registrar el usuario'], 500);
        }
    }
    
    public function login(LoginRequest $request)
    {
        try {
            // Validar la solicitud (la validación debería ocurrir dentro del objeto LoginRequest)
    
            // Intentar autenticar al usuario
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['incorrecto' => 'El email o la contraseña son incorrectos'], 422);
            }
    
            // Obtener al usuario autenticado
            $user = Auth::user();
    
            // Obtener la dirección IP del usuario desde el encabezado 'X-Forwarded-For' o directamente desde la solicitud
            $clientIp = $request->header('X-Forwarded-For') ?? $request->ip();
    
            // Verificar si la dirección IP ha cambiado y actualizarla si es necesario
            if ($user->ip_address !== $clientIp) {
                $user->ip_address = $clientIp;
                $user->save();
            }
    
            // Retornar el token de autenticación y la información del usuario
            return [
                'token' => $user->createToken('token')->plainTextToken,
                'user' => $user
            ];
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante el proceso de autenticación
            return response()->json(['error' => 'Error al iniciar sesión'], 500);
        }
    }

    //Forget password api method
    public function cambiarContraseña(ChangePasswordRequest $request)
{
    $user = $request->user();

    // Verificar si la contraseña actual es correcta
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'incorrecto' => ['La contraseña actual es incorrecta']
        ], 401);
    }

    // Verificar si la nueva contraseña está vacía
    if (empty($request->new_password)) {
        return response()->json([
            'error' => 'La nueva contraseña no puede estar vacía'
        ], 422);
    }

    // Cambiar la contraseña del usuario
    $user->password = bcrypt($request->new_password);

        if ($user->save()) {
            return response()->json([
                'message' => '¡Contraseña cambiada correctamente!'
            ], 200);
        }
    }




    public function olvide(Hasher $hasher, Request $request): JsonResponse
    {
        // Definir mensajes personalizados
        $messages = [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Por favor, proporciona un email válido',
        ];

        // Validar el request
        $request->validate([
            'email' => 'required|email',
        ], $messages);

    // Buscar al usuario por su correo electrónico
    $user = User::where('email', $request->input('email'))->first();

    // Verificar si el usuario no existe
    if (!$user) {
        return response()->json(['errors' => 'Usuario no encontrado'], 404);
    }

    // Generar un token de restablecimiento de contraseña
    $resetPasswordToken = bin2hex(random_bytes(16));

    // Codificar el token como una cadena base64 segura para URL
    $resetPasswordToken = rtrim(strtr(base64_encode($resetPasswordToken), '+/', '-_'), '=');

    // Establecer la fecha de vencimiento del token (1 día)
    $expiresAt = now()->addDay();

    // Crear o actualizar la entrada de restablecimiento de contraseña para el usuario
    PasswordReset::updateOrCreate(
        ['email' => $user->email],
        ['token' => $resetPasswordToken, 'expires_at' => $expiresAt]
    );

    // Notificar al usuario sobre el restablecimiento de contraseña
    $user->notify(new PasswordResetNotification($user, $resetPasswordToken));

    // Retornar una respuesta JSON
    return response()->json(['status' => 'Un código se ha enviado a tu correo electrónico']);
}

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $token = $request->query('token'); // Obtener el token desde la URL

        if (!$token) {
            return response()->json(['errors' => ['Token not provided']], 400);
        }

        // Buscar la solicitud de restablecimiento de contraseña
        $resetRequest = PasswordReset::where('token', $token)->first();

        if (!$resetRequest) {
            return response()->json(['errors' => ['tokens' => ['Token inválido']]], 400);
        }

        // Obtener el usuario asociado al token
        $user = User::where('email', $resetRequest->email)->first();

        if (!$user) {
            return response()->json(['errors' => ['User not found']], 404);
        }

        // Obtener los datos validados
        $data = $request->validated();

        // Actualizar la contraseña del usuario
        $user->fill([
            'password' => Hash::make($data['password']),
        ]);
        $user->save();

        // Eliminar la solicitud de restablecimiento de contraseña
        $resetRequest->delete();

        // Eliminar todos los tokens de acceso del usuario
        $user->tokens()->delete();

        // Crear un nuevo token de acceso
        $token = $user->createToken('authToken')->plainTextToken;

        $response = [
            'status' => 'Contraseña cambiada correctamente'
        ];

        return response()->json($response, 201);
    }



    public function comprobarToken(Request $request): JsonResponse
    {
        // Obtener el token enviado por el cliente
        $token = $request->query('token');

        // Verificar si el token existe en la base de datos
        $passwordReset = PasswordReset::where('token', $token)->first();

        // Si el token existe, devolver un código de estado 200
        if ($passwordReset) {
            return response()->json(['valido' => 'Token válido'], 200);
        } else {
            // Si el token no existe, devolver un código de estado 404
            return response()->json(['errors' => ['tokens' => ['Token inválido']]], 404);

        }
    }

    public function eliminarCuenta(EliminarCuentaRequest $request): JsonResponse
    {
        $request->validated();


        $user = $request->user();
        // Verificar si la contraseña actual es correcta
        if (Hash::check($request->current_password, $user->password)) {
            // La contraseña coincide, eliminar la cuenta
            $user->delete(); // Esto eliminará el usuario de la base de datos
            return response()->json([
                'message' => 'La cuenta ha sido eliminada correctamente'
            ], 200);
        } else {
            // La contraseña no coincide
            return response()->json([
                'errors' => ['La contraseña actual es incorrecta']
            ], 401);
        }
    }
    public function ultimaSesionUsuario(Request $request)
    {
        // Verificar que la solicitud sea de tipo POST
        if ($request->isMethod('post')) {
            // Obtener el usuario autenticado
            $user = auth()->user();
            
            // Validar el valor del estado recibido
            $request->validate([
                'ultima_sesion' => 'required',
            ]);
            
            // Actualizar el estado del usuario autenticado
            $user->ultima_sesion = $request->input('ultima_sesion');
            
            // Guardar los cambios en la base de datos
            if ($user->save()) {
                // Retornar una respuesta de éxito
                return response()->json(['message' => 'Sesión actualizado correctamente.'], 200);
            } else {
                // Retornar una respuesta de error
                return response()->json(['message' => 'Error al actualizar la sesión.'], 500);
            }
        } else {
            // Retornar una respuesta de método no permitido
            return response()->json(['message' => 'Método no permitido.'], 405);
        }
    }
    



public function cambiarEstadoUsuario(Request $request)
{
    // Verificar que la solicitud sea de tipo POST
    if ($request->isMethod('post')) {
        // Obtener el usuario autenticado
        $user = auth()->user();
        
        // Validar el valor del estado recibido
        $request->validate([
            'estado' => 'required|in:Conectado,Desconectado',
        ]);
        
        // Actualizar el estado del usuario autenticado
        $user->estado = $request->input('estado');
        
        // Guardar los cambios en la base de datos
        if ($user->save()) {
            // Retornar una respuesta de éxito
            return response()->json(['message' => 'Estado actualizado correctamente.'], 200);
        } else {
            // Retornar una respuesta de error
            return response()->json(['message' => 'Error al actualizar el estado.'], 500);
        }
    } else {
        // Retornar una respuesta de método no permitido
        return response()->json(['message' => 'Método no permitido.'], 405);
    }
}
}