<?php
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\SuscripcionesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CambiarPerfilController;
use App\Http\Controllers\InformacionPersonalController;

//USUARIOS VERIFICADOS
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/cambiar-contraseña', [AuthController::class, 'cambiarContraseña']);
    Route::delete('/eliminar-cuenta', [AuthController::class, 'eliminarCuenta']);
    Route::post('/enviar_formulario', [PromptController::class, 'enviarFormulario']);
    Route::get('/prompts', [PromptController::class, 'conseguirPrompts']);
    Route::get('/ver-prompts', [PromptController::class, 'todosLosPrompts']);
    Route::post('/cambiar-perfil', [CambiarPerfilController::class, 'subirImagen']);
    Route::get('/imagen-perfil', [CambiarPerfilController::class, 'obtenerImagenPerfil']);
    Route::post('/informacion-personal', [InformacionPersonalController::class, 'store']);
    Route::get('/informacion-personal', [InformacionPersonalController::class, 'show']);
    Route::post('/comprar-suscripcion', [SuscripcionesController::class, 'comprar']);
    Route::delete('/cancelar-suscripcion', [SuscripcionesController::class, 'eliminar']);
    Route::get('/ver-suscripcion', [SuscripcionesController::class, 'getAll']);
    Route::post('/ultima-sesion-usuario', [AuthController::class, 'ultimaSesionUsuario']);
    Route::post('/cambiar-estado-usuario', [AuthController::class, 'cambiarEstadoUsuario']);

});

//ADMINISTRADORES
Route::middleware(['auth:sanctum', 'verified', 'role:1'])->group(function () {
    Route::get('/suscripcion/beneficio', [AdministradorController::class, 'getCosteTotalUltimaSemana']);
    Route::get('/suscripcion/total', [AdministradorController::class, 'getCosteTotal']);
    Route::get('/ver-informacion-usuario', [AdministradorController::class, 'informacionUsuarioPanel']);
    Route::get('/informacion-usuario-panel/{id}', [AdministradorController::class, 'informacionUserId']);
    Route::put('/informacion-usuario-panel/{id}', [AdministradorController::class, 'informacionUserIdActualizar']);
    Route::delete('/eliminar-cuenta-usuario', [AdministradorController::class, 'eliminarCuentasUsuarios']);
    Route::get('/usuarios-ultima-semana', [AdministradorController::class, 'usuariosUltimaSemana']);
});

//RUTAS PUBLICAS

Route::post('email-notificacion', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware(['auth:sanctum']);
Route::get('verificar/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware(['auth:sanctum']);
Route::post('/reset', [AuthController::class, 'reset']);
Route::get('/reset', [AuthController::class, 'reset']);
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/olvide', [AuthController::class, 'olvide']);
Route::get('/comprobar-token', [AuthController::class, 'comprobarToken']);
Route::post('/recibir-email', [EmailController::class, 'recibirEmail']);