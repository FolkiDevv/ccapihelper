# CCApiHelper
Библиотека для удобной работы с CatCoin API на PHP.

[![CCApiHelper](https://img.shields.io/badge/CCApiHelper-1.0-brightgreen)](https://github.com/Floory/ccapihelper)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D7.0-blue)](https://php.net/)
[![Беседа в Vk](https://img.shields.io/badge/%D0%91%D0%B5%D1%81%D0%B5%D0%B4%D0%B0%20%D0%B2-Vk-orange)](https://vk.me/join/AJQ1dzYf1BBBwChl3mcP8kvz)

### Оглавление
- [Подключение](#Подключение)
- [Перевод](#Перевод)
- [Получение баланса](#Получение-баланса)
- [Получение списка транзакций](#Получение-списка-транзакций)
- [Получение ссылки на оплату](#Получение-ссылки-на-оплату)
- [Получение пропущенных переводов](#Получение-пропущенных-переводов)
- [Настройка CallBack Api](#Настройка-CallBack-Api)
- [Проверить подлинность запроса](#Проверить-подлинность-запроса)
- [Ответ](#Ответ)


## Подключение
Пример:
```php
require_once "./ccapihelper.php";

$cc = new CCApiHelper(523325665, 'b84619xnb70919303e1bc');
```

| Параметр     | Тип    | Обязательный?     | Описание                                             |
|--------------|--------|-------------------|------------------------------------------------------|
| merchant_id  | int    | **yes**           | ID странички, для которой был получен платёжный ключ |
| apikey       | string | **yes**           | Платёжный ключ                                       |

## Получение списка транзакций
Пример:
```php
$cc->getTransactions();
```

| Параметр     | Тип    | Обязательный? | Описание                                                                                                                              |
|--------------|--------|---------------|---------------------------------------------------------------------------------------------------------------------------------------|
| tx           | int    | no            | Описано в [документации](https://documenter.getpostman.com/view/8482328/SVfGzCCM?version=latest#36eca604-3cef-4966-a336-a46b440bb981) |
| last_tx      | int    | no            | Номер последней транзакции.                                                                                                           |

Если Вам нужно получить все транзакции **на текущий аккаунт**, используйте `$cc->getTransactions(2);`. Если необходимо получить только транзакции **по ссылкам**, то следует использовать `$cc->getTransactions();`.

## Перевод
Пример:
```php
$cc->sendTransfer(523325665, 100);
```

| Параметр         | Тип    | Обязательный? | Описание                                                                             |
|------------------|--------|---------------|--------------------------------------------------------------------------------------|
| to_id            | int    | **yes**       | ID пользователя, которому будет отправлен перевод                                    |
| amount           | int    | **yes**       | Сумма перевода                                                                       |
| mark_as_merchant | bool   | no            | Пометить перевод как перевод от магазина? (по умолчанию `true`)                      |

## Получение баланса
Пример:
```php
$cc->getBalance([1, 2]); // получение баланса vk.com/id523325665 и vk.com/id2
$cc->getBalance(); // получения баланса пользователя, указанного при инициализации
```

| Параметр     | Тип    | Обязательный? | Описание                                                                                                                                                                                                                                                     |
|--------------|--------|---------------|----------------------------------------------------------------------------------------------------------------------------------------|
| user_ids     | array  | no            | Описано в [документации](https://documenter.getpostman.com/view/8482328/SVfGzCCM?version=latest#0f9637c9-48f1-43a8-8df8-d46cfc10f4c2). |

## Получение ссылки на оплату
Примеры:
```php
$cc->genPayLink(100);
$cc->genPayLink(100, false);
```

| Параметр     | Тип    | Обязательный?   | Описание                                                                                                             |
|--------------|--------|-----------------|----------------------------------------------------------------------------------------------------------------------|
| sum          | int    | **yes**         | Сумма перевода                                                                                                       |
| fixed        | bool   | no              | Сумма фиксирована или нет? [Документация](https://documenter.getpostman.com/view/8482328/SVfGzCCM?version=latest)    |

## Изменение названия магазина
Пример:
```php
$cc->setName('CCApiHelper');
```

| Параметр | Тип    | Обязательный? | Описание          |
|----------|--------|---------------|-------------------|
| name     | string | **yes**       | Название магазина |

## Настройка CallBack Api
### Добавить сервер
Пример:
```php
$cc->addCallBack('https://myhost.net/notify.php');
```

| Параметр | Тип    | Обязательный? | Описание                       |
|----------|--------|---------------|--------------------------------|
| url      | string | **yes**       | Адрес для отправки уведомлений |

### Удалить сервер
Пример:
```php
$cc->delCallBack();
```

### Получение пропущенных переводов
Пример:
```php
$cc->getLostTransactions();
```

Подробнее можно прочесть в [документации](https://documenter.getpostman.com/view/8482328/SVfGzCCM?version=latest#18360d31-0e9c-4925-8b67-9edffb5654c5)

### Проверить подлинность запроса
Пример:
```php
$data = json_decode(file_get_contents('php://input'), true);
echo $cc->isValidHook($data) ? 'Все ок.' : 'Доступ запрещен.';
```

| Параметр | Тип             | Обязательный? | Описание                                                                                                                                       |
|----------|-----------------|---------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| prms     | array or object | **yes**       | Данные запроса, декодированные через `json_decode(file_get_contents('php://input'), true)` или `json_decode(file_get_contents('php://input'))` |


## Ответ
При вызове любого метода API возвращается массив с двумя полями, либо false.

| Ключ         | Тип    |  Описание                                                                               |
|--------------|--------|-----------------------------------------------------------------------------------------|
| isOk         | bool   | `true`, если запрос выполнен успешно, или `false` если произошла ошибка при выполнении. |
| response     | array  | **Возвращается только если `isOk` == `true`.** Массив, содержащий ответ от API.         |
| error        | string | **Возвращается только если `isOk` == `false`.** Строка, описывающая ошибку CURL.        |

Если что-то пошло не так, вернётся значение `false`. Проверить можно так:
```php
$result = $cc->getTransactions();
if($result === false) {
	// что-то не так
} elseif($result['status']) {
	// запрос выполнен успешно
} else {
	// обработка ошибки CURL
}
```

**Внимание!** Ответ API можно будет получить через `$result['response']`, если ответ API примерно таков:
```json
{
  "isOk": true,
  "response": {
    "id": "65",
    "from_id": "72301104",
    "to_id": 166061157,
    "amount": 100,
    "payload": 123123125,
    "created_at": 1566233437
  }
}
```