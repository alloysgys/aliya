<?php
// Файл: my_applications.php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключаем функции
require_once 'php/application_functions.php';
require_once 'php/user_functions.php';

// Данные пользователя
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'user';

// Получаем заявки
$applications = getUserApplications($user_id);

// Сообщения
$success = $_SESSION['application_success'] ?? '';
$error = $_SESSION['application_error'] ?? '';
unset($_SESSION['application_success'], $_SESSION['application_error']);

include 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-10 col-lg-10">
        <div class="card">
            <h1 class="text-center mb-4">Мои заявки на обучение</h1>

            <?php if (!empty($success)): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($user_role == 'admin'): ?>
            <p class="text-center mb-3">
                <a href="admin/dashboard.php" class="btn btn-danger">Перейти в панель администратора</a>
            </p>
            <?php endif; ?>

            <?php if (empty($applications)): ?>
            <div class="text-center p-4">
                <p>У вас пока нет ни одной заявки на обучение</p>
                <a href="new_application.php" class="btn btn-success">Создать первую заявку</a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Курс</th>
                            <th>Дата начала</th>
                            <th>Способ оплаты</th>
                            <th>Статус</th>
                            <th>Дата подачи</th>
                            <th>Отзыв</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app):
                                $status_class = match($app['status']) {
                                    'Новая' => 'status-new',
                                    'Идет обучение' => 'status-in-progress',
                                    'Обучение завершено' => 'status-completed',
                                    default => ''
                                };
                            ?>
                        <tr>
                            <td><?= $app['id'] ?></td>
                            <td><?= htmlspecialchars($app['course_name']) ?></td>
                            <td><?= date('d.m.Y', strtotime($app['desired_start_date'])) ?></td>
                            <td><?= htmlspecialchars($app['payment_method_name']) ?></td>
                            <td><span class="status <?= $status_class ?>"><?= htmlspecialchars($app['status']) ?></span>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($app['created_at'])) ?></td>
                            <td><?= !empty($app['review']) ? htmlspecialchars($app['review']) : '—' ?></td>
                            <td>
                                <?php if ($app['status'] == 'Обучение завершено' && empty($app['review'])): ?>
                                <button class="btn btn-success btn-sm"
                                    onclick="showReviewForm(<?= $app['id'] ?>)">Оставить отзыв</button>
                                <div id="review-form-<?= $app['id'] ?>" style="display:none;" class="mt-2">
                                    <form action="review_handler.php" method="POST" class="d-flex gap-2 flex-wrap">
                                        <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                        <input type="text" name="review" class="form-control form-control-sm"
                                            placeholder="Ваш отзыв..." maxlength="500" required>
                                        <button type="submit" class="btn btn-success btn-sm">Отправить</button>
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            onclick="hideReviewForm(<?= $app['id'] ?>)">Отмена</button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showReviewForm(id) {
    document.getElementById('review-form-' + id).style.display = 'block';
}

function hideReviewForm(id) {
    document.getElementById('review-form-' + id).style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>