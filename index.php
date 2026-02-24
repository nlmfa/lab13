<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа №13 - AJAX и Fetch</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        
        h2 {
            color: #555;
            margin: 25px 0 15px;
            font-size: 20px;
        }
        
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 20px;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        input:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .btn-submit {
            background: #28a745;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        
        .btn-submit:hover {
            background: #218838;
        }
        
        #output {
            margin-top: 30px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            background: #fafafa;
            min-height: 100px;
        }
        
        .user-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 10px 15px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-id {
            background: #007bff;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }
        
        .user-name {
            font-size: 16px;
            color: #333;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .info-message {
            background: #e7f3ff;
            color: #004085;
            border: 1px solid #b8daff;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            color: #666;
        }
        
        .loading {
            text-align: center;
            color: #007bff;
            font-style: italic;
        }
        
        .header-info {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Лабораторная работа №13</h1>
        <div class="header-info">
            Основы AJAX и взаимодействие с сервером через fetch
        </div>

        <!-- Кнопка загрузки данных -->
        <button class="btn" id="loadBtn">
            Загрузить данные
        </button>

        <!-- Форма добавления записи -->
        <h2>Добавить новую запись</h2>
        <form id="addForm">
            <div class="form-group">
                <input 
                    type="text" 
                    id="nameInput" 
                    placeholder="Введите имя" 
                    required
                    autocomplete="off"
                >
            </div>
            <button type="submit" class="btn btn-submit">
                Отправить
            </button>
        </form>

        <!-- Блок для вывода результатов -->
        <h2>Результат:</h2>
        <div id="output">
            <div class="info-message">
                Нажмите кнопку "Загрузить данные" для получения списка
            </div>
        </div>
    </div>

    <script>
        // Находим элементы DOM
        const loadBtn = document.getElementById('loadBtn');
        const addForm = document.getElementById('addForm');
        const output = document.getElementById('output');
        const nameInput = document.getElementById('nameInput');

        // Функция для отображения загрузки
        function showLoading() {
            output.innerHTML = '<p class="loading">Загрузка...</p>';
        }

        // Функция для отображения ошибок
        function showError(message) {
            output.innerHTML = `<div class="error-message">Ошибка: ${message}</div>`;
        }

        // --- Загрузка данных (GET) ---
        loadBtn.addEventListener('click', () => {
            showLoading();
            
            fetch('api.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка сервера');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        showError(data.error);
                        return;
                    }
                    
                    // Очищаем output и выводим данные
                    let html = '<h3 style="margin-bottom: 15px;">Список пользователей:</h3>';
                    
                    if (data.length === 0) {
                        html += '<div class="info-message">Список пуст. Добавьте первую запись.</div>';
                    } else {
                        data.forEach(user => {
                            html += `
                                <div class="user-card">
                                    <div class="user-id">${user.id}</div>
                                    <div class="user-name">${user.name}</div>
                                </div>
                            `;
                        });
                    }
                    
                    output.innerHTML = html;
                })
                .catch(error => {
                    showError(error.message);
                });
        });

        // --- Добавление записи (POST) ---
        addForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const name = nameInput.value.trim();

            if (!name) {
                alert('Пожалуйста, введите имя');
                return;
            }

            // Показываем сообщение о отправке
            const addMessage = document.createElement('div');
            addMessage.className = 'loading';
            addMessage.textContent = 'Отправка данных...';
            output.appendChild(addMessage);

            fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name: name })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сервера');
                }
                return response.json();
            })
            .then(data => {
                // Убираем сообщение о загрузке
                if (addMessage.parentNode) {
                    addMessage.remove();
                }
                
                if (data.status === 'success') {
                    // Показываем сообщение об успехе
                    const successMsg = document.createElement('div');
                    successMsg.className = 'success-message';
                    successMsg.innerHTML = `${data.message}<br><small>Добавлен: ${data.user.name}</small>`;
                    
                    // Вставляем сообщение в начало output
                    if (output.children.length > 0) {
                        output.insertBefore(successMsg, output.firstChild);
                    } else {
                        output.appendChild(successMsg);
                    }
                    
                    // Очищаем поле ввода
                    nameInput.value = '';
                    
                    // НЕ загружаем данные автоматически!
                    // Пользователь сам нажмет кнопку "Загрузить данные"
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                if (addMessage.parentNode) {
                    addMessage.remove();
                }
                showError(error.message);
            });
        });

        // Нажатие Enter в поле ввода
        nameInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addForm.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>