<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura Generada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f4f8;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #4f46e5;
            color: white;
            text-align: center;
            padding: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #4f46e5;
            margin-top: 0;
        }

        .facture-info {
            margin: 20px 0;
        }

        .facture-info p {
            margin: 8px 0;
            font-size: 16px;
        }

        .highlight {
            text-transform: capitalize;
            font-weight: bold;
            color: #111827;
        }

        .footer {
            background-color: #f9fafb;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #6b7280;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Factura Generada</h1>
        </div>
        <div class="content">
            <h2>¡Hola!</h2>
            <p>Se ha generado una nueva factura correspondiente al mes de <span class="highlight">{{ $number_month }}</span>.</p>

            <div class="facture-info">
                <p><strong>Fecha de emisión:</strong> {{ $fecha }}</p>
                <p><strong>Código de factura:</strong> {{ $code }}</p>
                <p><strong>Porcentaje recargo (5 primeros días):</strong> {{ $porcent_first_five_days }}%</p>
            </div>

            <p class="text-center">Por favor, revisa el sistema para ver más detalles.</p>
        </div>
    </div>
</body>

</html>