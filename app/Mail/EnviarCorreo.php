<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnviarCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje; // Propiedad para el mensaje

    /**
     * Create a new message instance.
     */
    public function __construct($mensaje)  // Recibir el mensaje como parámetro
    {

      
        $this->mensaje = $mensaje;  // Asignar el mensaje a la propiedad
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enviar Correo',
            from: config('mail.from.address'), // Usa solo la dirección de correo
        );
    }
    

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.mensaje',
            
            with: ['mensaje' => $this->mensaje],  // Pasar el mensaje a la plantilla
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

