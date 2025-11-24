<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { padding: 20px; text-align: center; color: white; }
        .header.accepted { background: #10B981; }
        .header.rejected { background: #EF4444; }
        .content { background: #f9f9f9; padding: 20px; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #4F46E5; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $action === 'aceptada' ? 'accepted' : 'rejected' }}">
            <h1>Cita {{ ucfirst($action) }}</h1>
        </div>
        
        <div class="content">
            <p>Estimado/a <strong>{{ $appointment->patient_name }}</strong>,</p>
            
            @if($action === 'aceptada')
                <p>¡Buenas noticias! Su cita médica ha sido <strong>confirmada</strong>.</p>
            @else
                <p>Lamentamos informarle que su cita médica ha sido <strong>rechazada</strong>.</p>
            @endif
            
            <div class="info-box">
                <p><strong>Médico:</strong> {{ $appointment->doctor->name }}</p>
                <p><strong>Fecha y Hora:</strong> {{ $appointment->appointment_date->format('l, d/m/Y - h:i A') }}</p>
                <p><strong>Duración:</strong> {{ $appointment->duration_minutes }} minutos</p>
                @if($appointment->notes)
                    <p><strong>Nota:</strong> {{ $appointment->notes }}</p>
                @endif
            </div>
            
            @if($action === 'aceptada')
                <p>Por favor llegue 15 minutos antes de su cita. Si necesita cancelar, contáctenos con anticipación.</p>
            @else
                <p>Por favor contacte con nosotros para agendar una nueva cita en otro horario disponible.</p>
            @endif
        </div>
        
        <div class="footer">
            <p>Sistema de Citas - Cirugía General</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>