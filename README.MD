# Тест
    php ./bin/phpunit
Перед этим нужно применить миграции и фикустуры (см. ниж)

## Применение фикстур 
### Для .env.local:
    symfony console doctrine:fixtures:load
### Для .env.test.local:
    APP_ENV=test symfony console doctrine:fixtures:load 

#Миграция

## Создание файлов миграции из моделей
    php bin/console doctrine:migrations:diff
    
## Применение миграций для окружений
### Для .env.local:
    php bin/console doctrine:migrations:migrate
### Для .env.test.local:
    APP_ENV=test php bin/console doctrine:migrations:migrate