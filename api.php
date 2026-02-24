<?php
// Устанавливаем заголовок для JSON ответа
header("Content-Type: application/json");

// Разрешаем запросы с любого источника
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Обрабатываем preflight запросы OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ПУСТОЙ СПИСОК пользователей (изначально нет данных)
$users = [];

// Для демонстрации можно добавить тестовые данные, раскомментировав следующую строку:
// $users = [["id" => 1, "name" => "Тестовый пользователь"]];

// Функция для сохранения данных (простая имитация БД)
function saveUsers($users) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Функция для загрузки данных
function loadUsers() {
    if (file_exists('users.json')) {
        $content = file_get_contents('users.json');
        return json_decode($content, true) ?: [];
    }
    return [];
}

// Загружаем сохраненных пользователей
$users = loadUsers();

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // GET: возвращаем список пользователей
    echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} 
elseif ($method === 'POST') {
    // POST: получаем данные из тела запроса
    $json = file_get_contents("php://input");
    $input = json_decode($json, true);
    
    // Проверяем, что данные получены
    if ($input === null) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Неверный формат JSON"
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // Проверяем наличие имени
    if (isset($input['name']) && !empty(trim($input['name']))) {
        $name = trim($input['name']);
        
        // Создаем нового пользователя
        $newUser = [
            "id" => count($users) + 1,
            "name" => htmlspecialchars($name, ENT_QUOTES, 'UTF-8')
        ];
        
        // Добавляем в массив
        $users[] = $newUser;
        
        // Сохраняем в файл
        saveUsers($users);
        
        // Успешный ответ
        echo json_encode([
            "status" => "success",
            "message" => "Данные добавлены",
            "user" => $newUser
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Имя не может быть пустым"
        ], JSON_UNESCAPED_UNICODE);
    }
}
else {
    http_response_code(405);
    echo json_encode([
        "error" => "Method not allowed"
    ], JSON_UNESCAPED_UNICODE);
}
?>