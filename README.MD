# Requirements

- wkhtmltopdf для преобразования html в pdf (для отчётов).

      Mac:
      brew install wkhtmltopdf
    
      Debian:
        Instruction here: https://github.com/wkhtmltopdf/wkhtmltopdf/issues/2037
        Releases here: https://github.com/wkhtmltopdf/packaging/releases/

        Download
        wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.1.4-2/wkhtmltox_0.12.1.4-2.stretch_amd64.deb
        
        Install
        sudo dpkg -i wkhtmltox_0.12.1.4-2.stretch_amd64.deb
        If you run into an error with missing dependencies, install the dependencies with sudo apt install -f.

        wkhtmltopdf --version

# Сервер

    symfony server:start

# Тест

    php bin/phpunit

или

    make tests
    Скрипт очищает базу, выполняет миграции, создаёт фикстуры

## Применение фикстур

    symfony console doctrine:fixtures:load
    APP_ENV=test symfony console doctrine:fixtures:load 

# Миграция

## Создание файлов миграции из моделей

    php bin/console doctrine:migrations:diff

## Применение миграции

    php bin/console doctrine:migrations:migrate
    APP_ENV=test php bin/console doctrine:migrations:migrate

## Откат миграций

    php bin/console doctrine:migrations:migrate prev
    APP_ENV=test php bin/console doctrine:migrations:migrate prev

# Frontend

    yarn watch

    yarn build

## testonomica_api

В директории testonomica_api выполнить:

    yarn link

В директории testonomica выполнить:

    yarn link testonomica_api

# Окружение

Чтобы сгенерировать .env.local.php на продакте:

    symfony composer dump-env prod

# PHPSTORM

Xdebug 3 в консоли работает только с портом 9003. Поэтому в PhpStorm нужно установить порядок портов 9003,9000

# Upgrading a Minor Version

    https://symfony.com/doc/current/setup/upgrade_minor.html#upgrading-other-packages

---

# API

Текущая версия клиентского API: 3, [читать документацию](https://testonomica.com/doc/).

---

# Инструкция по занесению тестов

## Описание

Описание теста делается в панели администратора. Там заполняется название и описание теста, его продолжительность,
видимость и настройки именования файлов.

## Инструкция к тесту

Инструкция заносится в файле quiz.xml:

```xml

<quiz>
    <instruction>
        <ru>
            <p>Инструкция на русском языке</p>
        </ru>
        <en>
            <p>Instruction on English</p>
        </en>
    </instruction>
    <items>
        <item>
            ...
        </item>
    </items>
</quiz>
```

## Опросник

Опросник заносится в файле quiz.xml:

## Калькулятор

Калькулятор работает с сырыми ответами, считает правильные ответы и составляет карту, подготовленную для отображения.
По умолчанию работает AutoCalculator, если отсутствует калькулятор для теста.
Результат работы калькулятора передаётся в сервис вывода.

## Работа с config.xml

Цель config.xml призвана заменить view. config.xml может:

- интерпретировать различные сценарии с отображением шкалы и текста
- выводить текст до и после сценариев (intro/outro)

Конфиг может заменить view, или как минимум может содержать переводы для view.
Тексты размещаются в теге <texts>, а сценарии в <scenarios>.

Тексты могут быть использованы для перевода текстов view (как в тесте политических ориентаций), а так же могут содержать
outro и intro к тексту (будет отбражён перед и после сценариями).

### Outro & Intro

Intro и Outro - это тексты, которые распологаются до и после сценарных выводов. Например, intro может содержать
информацию о том, как нужно интерпретировать результаты, а outro может содержать общие рекомендации и полезные статьи.
Заполняется в секции texts:

```xml

<texts>
    <intro>
        <ru>
            <p>
                <i>Обратите внимание.</i>Чем выше шкала, тем...
            </p>
        </ru>
    </intro>
    <outro>
        <ru>
            <p>Чтобы лучше разобраться в своих чувствах, рекомендуем обратиться к...</p>
        </ru>
    </outro>
</texts>
```

### Сценарии

Сценарий - это блок с условиями (не обзяталельно), шкалой (не обязательно) и текстом (не обязательно).
Возможны варианты отображения шкалы без условий и без текста (как в тесте на сексуальные ориентации).

```xml

<scenarios>
    <scenaio>...</scenaio>
    <scenaio>...</scenaio>
</scenarios>
```

#### Условия

Условия не обязательны. Их наличие только определяет условие вывода текста или шкалы сценария. Если условие не задано,
содержимое всегда будет отображаться. В каких случаях не требуются условия? Например, когда нужно просто отобразить
шкалу, как в тесте на сексульную ориентацию.

Доступные операторы:

- больше
- больше_или_равно
- меньше
- меньше_или_равно

```xml

<scenarios>
    <conditions>
        <condition var="SUM" operator="больше_или_равно" value="10"/>
        <condition var="SUM" operator="меньше" value="30"/>
    </conditions>
    <scale>...</scale>
    <text>...</text>
</scenarios>
```

#### Шкала

Шкалы добавляются в сценариях.

```xml

<scenarios>
    <scenario>
        <scale percentVar="SCALE" showVar="SUM" showMaxVal="80">
            <label>
                <ru>Ваш уровень альтруизма</ru>
                <en>Your level of altruism</en>
            </label>
            <levels>
                <level up="25" color="danger"/>
                <level up="75" color="warning"/>
                <level up="100" color="success"/>
            </levels>
        </scale>
    </scenario>
</scenarios>
```

**Scale.**
Обязательный атритут scale только percentVar. Если указан только он, то вывод будет выглядеть так: 59%. Когда нужно
отобразить не процент, а точный балл и максимальный балл, используйте showVar и showMaxVal. С ними текст будет выгядеть
так: 20 из 40.

**Label.**
Наличие label добавляет текст перед значением. Например: уровень гнева: 59%, или: уровень гнева: 20 из 40.

**Levels.**
Чтобы задать условия раскраски шкалы используйте levels. Level задаётся в процентах. Атрибут up - процент, до которого
закрашивать указанным цветом. Обязательно только наличие стопроцентного случая (если указан только он, то шкала будет
всегда закрашена указанным цветом).

#### Текст

Для текста существует два тега: name и text.
Если требуется заголовок, то лучше использовать <name>, т.к. он будет преобразован в html сущность со стилями, которыми
можно управлять в соответствующих местах.

```xml

<scenarios>
    <scenario>
        <conditions>...</conditions>
        <name>
            <ru>Застенчивость: {{ SCALE }}%</ru>
            <en>Shyness: {{ SCALE }}%</en>
        </name>
        <text>
            <ru>
                <p>Причина застенчивости может быть в низкой уверенности...</p>
            </ru>
            <en>
                <p>Self assurance might be the main reason of shynes...</p>
            </en>
        </text>
    </scenario>
</scenarios>
```