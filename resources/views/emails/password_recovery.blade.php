<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #ff6600;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333333;
        }
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #666666;
        }
        .password-box {
            background-color: #f9f9f9;
            border: 2px solid #ff6600;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .password-label {
            font-size: 14px;
            color: #666666;
            margin-bottom: 10px;
            display: block;
        }
        .password-value {
            font-size: 24px;
            font-weight: bold;
            color: #ff6600;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            padding: 15px;
            background-color: #ffffff;
            border-radius: 4px;
            display: inline-block;
            min-width: 200px;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 5px 0;
            color: #856404;
            font-size: 14px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
        .footer p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ff6600;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Восстановление пароля</h1>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                Здравствуйте, {{ $client->name ?? 'Уважаемый клиент' }}!
            </div>
            
            <div class="message">
                Вы запросили восстановление пароля для вашей учетной записи. Мы сгенерировали для вас новый пароль.
            </div>
            
            <div class="password-box">
                <span class="password-label">Ваш новый пароль:</span>
                <div class="password-value">{{ $newPassword }}</div>
            </div>
            
            <div class="warning-box">
                <p><strong>Важно!</strong></p>
                <p>Рекомендуем вам войти в личный кабинет и изменить пароль на более удобный для вас.</p>
                <p>Если вы не запрашивали восстановление пароля, пожалуйста, немедленно свяжитесь с нашей службой поддержки.</p>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('frontend.profile.index') }}" class="button">Войти в личный кабинет</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>АСТ Компонентс</strong></p>
            <p>Это автоматическое уведомление. Пожалуйста, не отвечайте на это письмо.</p>
            <p>Если у вас есть вопросы, обратитесь в нашу службу поддержки.</p>
        </div>
    </div>
</body>
</html>
