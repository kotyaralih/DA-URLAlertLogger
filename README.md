# DA-URLAlertLogger
Простенькая CLI (Command Line Interface, или же не имеющая графического интерфейса) программа на PHP, которая сохраняет сообщения из донатов, содержащих ссылки (для DonationAlerts).

Использует [DonationAlerts API](https://www.donationalerts.com/apidoc), что вполне логично.

Перед использованием **обязательно полностью ознакомьтесь с README (описанием), чтобы у вас не возникало проблем при использовании!**

Для работы потребуется [получить токен от своего DonationAlerts аккаунта](https://github.com/kotyaralih/DA-URLAlertLogger?tab=readme-ov-file#%D0%BF%D0%BE%D0%BB%D1%83%D1%87%D0%B0%D0%B5%D0%BC-%D1%82%D0%BE%D0%BA%D0%B5%D0%BD-%D0%BE%D1%82-%D0%B0%D0%BA%D0%BA%D0%B0%D1%83%D0%BD%D1%82%D0%B0-donationalerts).

# Функционал
Каждые 20 секунд происходит проверка на наличие новых донатов, а так же проверка на наличие ссылок в них.

Если замечается хоть одна ссылка, то донат будет выведен в саму командную строку, а так же сохранен в файлик `urlDons.log`, который появится автоматически в той папке, в которой была запущена программа, в формате: `год-месяц-день час:минута:секунда ник_донатера (сумма_доната валюта) - сообщение_со_ссылкой`

# Как запустить?
Всё довольно-таки просто. Вы можете либо скачать репозиторий и напрямую запустить Main.php, открыв командную строку в папке с этим файлом, используя команду `php Main.php` (если у вас установлен [PHP](https://www.php.net/) со включенной поддержкой CURL), либо же, **вы можете загрузить архив с уже собранным приложением под Windows в [Releases](https://github.com/kotyaralih/DA-URLAlertLogger/releases).**

Для запуска собранного приложения, понадобится установить [.NET 5.0 Runtime](https://dotnet.microsoft.com/en-us/download/dotnet/thank-you/runtime-5.0.17-windows-x64-installer), т.к. для компиляции используется [Peachpie](https://peachpie.io/), встроенный в [.NET SDK](https://dotnet.microsoft.com/ru-ru/download), который позволяет выполнять PHP код в рамках .NET

Вы так же можете [собрать приложение из исходного кода самостоятельно!](https://github.com/kotyaralih/DA-URLAlertLogger?tab=readme-ov-file#%D0%BA%D0%B0%D0%BA-%D1%81%D0%BE%D0%B1%D1%80%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5)

# Возможные проблемы и способы их решения

### Программа почему-то зависла или не отображает новые донаты!!!!!!
- Скорее всего, вы кликнули мышкой внутри окна командной строки.
  
  Это заставляет зависнуть _абсолютно любую_ CLI (Command Line Interface, или же не имеющую графического интерфейса) программу, до тех пор, пока вы не _**нажмете ENTER**_.
  
  Живите теперь с этим.

### Вопросительные знаки вместо букв
- Скорее всего, вы используете операционную систему Windows на английском языке.
   
   Это происходит, скорее, не из-за языка, а из-за криворукости Microsoft.
   
   Для исправления, убедитесь, что у вас установлен языковой пакет русского языка, а затем _**сделайте следующие действия, как показано на видео: https://youtu.be/8i0pytHxw3Y**_

# Как собрать приложение?
1) Устанавливаем [.NET SDK](https://dotnet.microsoft.com/ru-ru/download)
   
2) Клонируем или скачиваем репозиторий.
   
3) Открываем командную строку в папке с репозиторием.
   
4) Вводим команду `dotnet run`
   
5) Немного ждем...
    
6) Приложение будет собрано по пути `папка_репозитория/bin/Debug/net5.0`

# Получаем токен от аккаунта DonationAlerts
> [!CAUTION]
> Ни при каких обстоятельствах не делитесь своим токеном с посторонними лицами!
> 
> Хоть мы и создаем в данном примере токен с минимальными правами для работы приложения, но вы всеравно должны понимать, что токен - очень важная штука.

1) Для начала, вам потребуется создать свое приложение внутри DA - https://www.donationalerts.com/application/clients
   
   Имя приложения вводим абсолютно любое, URL перенаправления тоже - можно даже несуществующую ссылку (обязательно с https:// в начале), но главное запомнить её, т.к. она нам позже пригодится.
2) После создания приложения, запоминаем его ID.
3) Теперь нужно скопировать данную ссылку: `https://www.donationalerts.com/oauth/authorize?client_id=АЙДИ_ПРИЛОЖЕНИЯ&redirect_uri=ССЫЛКА_ПЕРЕНАПРАВЛЕНИЯ&response_type=token&scope=oauth-user-show+oauth-donation-index`
   
   И в этой ссылке нужно заменить "АЙДИ_ПРИЛОЖЕНИЯ" на ID вашего приложения, и "ССЫЛКА_ПЕРЕНАПРАВЛЕНИЯ" на ссылку, которую вы указали при создании приложения.

4) Вставляем получившуюся ссылку в адресную строку браузера.
   
5) Разрешаем приложению доступ к списку донатов и к имени пользователя, после чего у вас в браузере откроется ссылка, которую вы вводили.
   
   Все, что будет указано в этой ссылке после access_token= и прямиком до &token_type необходимо скопировать - это и есть ваш токен.


   **Срок годности полученного токена - 1 год, если вы сами не захотите удалить приложение с DA.**
   
   **После истечения года, токен придется получать заново тем-же способом.**
