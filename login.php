<?php
// Файл: login.php
session_start();
$page_title = 'Вход - Корочки.есть';

// Получаем сообщения из сессии
$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['registration_success'] ?? '';

// Очищаем данные сессии
unset($_SESSION['login_error']);
unset($_SESSION['registration_success']);

include 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <h1 class="text-center mb-4">Вход в систему</h1>

            <?php if (!empty($success)): ?>
            <div class="alert alert-success text-center">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form action="login_handler.php" method="POST" id="login-form">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" class="form-control" id="login" name="login" required
                        placeholder="Введите ваш логин">
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required
                        placeholder="Введите ваш пароль">
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3">
                    <i class="fas fa-sign-in-alt"></i> Войти
                </button>
            </form>

            <p class="text-center mt-3">
                Еще не зарегистрированы? <a href="register.php">Создать аккаунт</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>