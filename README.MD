# Сервер
    symfony server:start

# Тест
    make tests
    Скрипт очищает базу, выполняет миграции, создаёт фикстуры

## Применение фикстур
    symfony console doctrine:fixtures:load
    APP_ENV=test symfony console doctrine:fixtures:load 

#Миграция

## Создание файлов миграции из моделей
    php bin/console doctrine:migrations:diff
    
## Применение миграции
    php bin/console doctrine:migrations:migrate
    APP_ENV=test php bin/console doctrine:migrations:migrate

## Откат миграций
    php bin/console doctrine:migrations:migrate prev
    APP_ENV=test php bin/console doctrine:migrations:migrate prev
    
# TODO
- Версионирование теста. 
Так, чтобы сохраненный результат не конфликтовал с версией xml теста или калькулятора 
Возможно, нужно указывать в результате версию пройденного теста, а в калькуляторе 
делать как минимум проверку и юзер-френдли исключения а-ля "Конфликт версий", 
или даже указывать диапазон допустимых версий или даже написать полифил.
  
# PHPSTORM
Xdebug 3 в консоли работает только с портом 9003.
Поэтому в PhpStorm нужно поменять порядок портов с 9000,9003 на 9003,9000

# Интеграция

Принцип остался прежний - 
1. получить публичный токен для пользователя 
2. сгенерировать ссылку для пользователя
Поменялись урлы и параметры.
   
Теперь подробно:

1. Сначала делается скрытый запрос (backend, curl) и получается публичный токен:
https://testonomica.com/partner/api/token/?token=JFeBtqLY3Tgw3uGG6b29hpdvfLlglGBE&user={ID_ПОЛЬЗОВАТЕЛЯ_ТЕСТОМЕТРИКИ}&service=proftest
где {ID_ПОЛЬЗОВАТЕЛЯ_ТЕСТОМЕТРИКИ} - уникальный идентификатор пользователя в вашей системе.
   Дополнительно: чтобы протестировать интеграцию без оплаты нужно добавить в параметр &isTest=1, и тогда робокасса будет запущена в тестовом режиме, средства не списываются.

Ответом будет публичный токен в формате JSON:
{"token":"cf738dd7-a138-4fa6-b3fd-52eef8ae2a92"}

2. Нужно распарсить JSON и сгенерировать URL для пользователя:
https://testonomica.com/partner/provide/?token={ПОЛУЧЕННЫЙ_ТОКЕН}
Дальше этот URL навесить на кнопку "Пройти тест".