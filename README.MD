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
