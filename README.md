# Laravel Demo: Интеграция с 1С-Битрикс и RabbitMQ

## Особенности реализации
- **Многослойная архитектура**: Controller -> Service -> Repository
- **Асинхронная обработка**: Очереди через RabbitMQ
- **Интеграция с 1С-Битрикс**: REST API + кеширование
- **Поиск**: Elasticsearch через Laravel Scout
- **Тестирование**: Feature-тесты с моками внешних сервисов
- **Безопасность**: Валидация, подготовленные SQL-запросы

## Технологии
- Laravel 10
- RabbitMQ
- 1С-Битрикс API
- MySQL

## Запуск проекта
```bash
# С Docker
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan migrate

# Без Docker
composer install
php artisan serve

## Архитектура системы

### Диаграмма последовательности
```mermaid
sequenceDiagram
    participant Клиент
    participant Контроллер as TaskController
    participant Сервис as TaskService
    participant БД as Database
    participant Битрикс as BitrixService
    participant Очередь as RabbitMQ
    Клиент->>Контроллер: POST /tasks (JSON)
    Контроллер->>Сервис: createTask(data)
    Сервис->>БД: Сохранить задачу
    БД-->>Сервис: Task object
    Сервис->>Битрикс: createDeal()
    Битрикс-->>Сервис: bitrix_id
    Сервис-->>Контроллер: Результат
    Контроллер->>Очередь: dispatch(ProcessTaskNotification)
    Очередь-->>Контроллер: Принято
    Контроллер-->>Клиент: 201 Created (JSON)
```

### Структура классов
```mermaid
classDiagram
    class TaskController {
        - taskService: TaskService
        - bitrix: BitrixService
        + store(TaskCreateRequest)$ JsonResponse
    }
    class TaskService {
        - searchIndexer: TaskIndexer
        + createTask(data)$ Task
    }
    TaskController --> TaskService
```