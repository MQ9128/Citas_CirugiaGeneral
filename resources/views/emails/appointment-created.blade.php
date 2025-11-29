<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #4F46E5; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cita Médica Agendada</h1>
        </div>
        
        <div class="content">
            <p>Estimado/a <strong>{{ $appointment->patient_name }}</strong>,</p>
            <p><strong>Cédula:</strong> {{ $appointment->patient_cedula }}</p>
            
            <p>Su cita médica ha sido agendada exitosamente. Los detalles son:</p>
            
            <div class="info-box">
                <p><strong>Especialidad:</strong> Cirugía General</p>
                <p><strong>Médico:</strong> {{ $appointment->doctor->name }}</p>
                <p><strong>Fecha y Hora:</strong> {{ $appointment->appointment_date->format('l, d/m/Y - h:i A') }}</p>
                <p><strong>Duración:</strong> {{ $appointment->duration_minutes }} minutos</p>
                @if($appointment->reason)
                    <p><strong>Motivo:</strong> {{ $appointment->reason }}</p>
                @endif
            </div>
            
            <p><strong>Estado:</strong> Pendiente de confirmación</p>
            
            <p>Recibirá un correo de confirmación una vez el médico apruebe su cita.</p>
        </div>
        
        <div class="footer">
            <p>Sistema de Citas - Cirugía General</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>