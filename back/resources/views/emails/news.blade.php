<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #007BFF; padding: 20px; color: #ffffff; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px; text-align: center;">
                            <h3 style="margin-top: 0; font-weight: normal; color: #333;">{{ $subtitle }}</h3>

                            @if(!empty($image))
                            <p style="margin: 20px 0;">
                                <img src="data:image/jpeg;base64,{{ $image }}" 
                                     alt="News Image" 
                                     style="max-width: 100%; border-radius: 10px; border: 1px solid #ddd;" />
                            </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>