<?php

namespace App\Http\Controllers;

use App\Notifications\RecibirEmailNotificacion;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\EmailRequest;

class EmailController extends Controller
{

    public function recibirEmail(EmailRequest $request)
    {
        $data = $request->validated();

        try {
            $emailData = $data;
            Notification::route('mail', config('mail.from.address'))->notify(new RecibirEmailNotificacion($emailData));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al enviar el correo: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => '¡Correo enviado con éxito!',
        ]);
    }
}
