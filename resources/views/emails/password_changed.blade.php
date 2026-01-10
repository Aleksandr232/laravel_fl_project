<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение данных в личном кабинете</title>
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
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #ff6600;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #333333;
            font-size: 16px;
        }
        .info-box p {
            margin: 10px 0;
            color: #666666;
            font-size: 14px;
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
        .field-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        .field-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eeeeee;
        }
        .field-list li:last-child {
            border-bottom: none;
        }
        .field-label {
            font-weight: bold;
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Изменение данных в личном кабинете</h1>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                Здравствуйте, {{ $client->name ?? 'Уважаемый клиент' }}!
            </div>
            
            <div class="message">
                Мы уведомляем вас о том, что в вашем личном кабинете были изменены следующие данные:
            </div>
            
            <div class="info-box">
                <h3>Измененные данные:</h3>
                <ul class="field-list">
                    @if(in_array('password', $changedFields))
                    <li>
                        <span class="field-label">Пароль:</span> был успешно изменен
                    </li>
                    @endif
                    @if(in_array('email', $changedFields))
                    <li>
                        <span class="field-label">Email:</span> был изменен на {{ $client->email }}
                    </li>
                    @endif
                    @if(in_array('name', $changedFields))
                    <li>
                        <span class="field-label">Имя:</span> было изменено на {{ $client->name }}
                    </li>
                    @endif
                </ul>
            </div>
            
            @if(in_array('password', $changedFields))
            <div class="warning-box">
                <p><strong>Важно!</strong></p>
                <p>Если вы не совершали это действие, пожалуйста, немедленно свяжитесь с нашей службой поддержки.</p>
            </div>
            @endif
            
            <div class="message">
                Если вы не совершали эти изменения, пожалуйста, немедленно свяжитесь с нашей службой поддержки.
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('frontend.profile.index') }}" class="button">Перейти в личный кабинет</a>
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