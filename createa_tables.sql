-- Файл: create_tables.sql
-- Создание базы данных для портала "Корочки.есть"
-- Создание таблицы users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы payment_methods
CREATE TABLE payment_methods (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Создание таблицы applications
CREATE TABLE applications (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    course_name VARCHAR(200) NOT NULL,
    desired_start_date DATE NOT NULL,
    payment_method_id INTEGER NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Новая',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    review TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT
);

-- Заполнение справочника способов оплаты
INSERT INTO payment_methods (name) VALUES
('Наличные'),
('Перевод по номеру телефона');

-- Добавление тестовых пользователей
INSERT INTO users (login, password, full_name, phone, email, role) VALUES
('Admin', 'KorokNET', 'Администратор Системы', '8(999)111-22-33', 'admin@korochki.ru', 'admin'),
('ivanov', 'password123', 'Иванов Иван Иванович', '8(999)222-33-44', 'ivanov@mail.ru', 'user'),
('petrova', 'qwerty123', 'Петрова Анна Сергеевна', '8(999)333-44-55', 'petrova@yandex.ru', 'user');

-- Добавление тестовых заявок
INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status) VALUES
(2, 'Основы программирования на Python', '2024-10-01', 1, 'Новая'),
(2, 'Веб-разработка для начинающих', '2024-09-15', 2, 'Идет обучение'),
(3, 'Английский для IT-специалистов', '2024-10-10', 1, 'Новая');

INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status, review) VALUES
(3, 'Excel для работы с данными', '2024-08-01', 2, 'Обучение завершено', 'Отличный курс, все понятно и доступно!');