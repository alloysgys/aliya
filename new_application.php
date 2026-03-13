<?php
// Файл: new_application.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'php/application_functions.php';

$payment_methods = getPaymentMethods();
$error = $_SESSION['application_error'] ?? '';
$old_data = $_SESSION['old_application_data'] ?? [];
unset($_SESSION['application_error'], $_SESSION['old_application_data']);

include 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-7">
        <div class="card">
            <h1 class="text-center mb-4">Создание новой заявки на обучение</h1>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form action="application_handler.php" method="POST">
                <div class="form-group mb-3">
                    <label for="course_name">Название курса *</label>
                    <input type="text" class="form-control" id="course_name" name="course_name" required
                        placeholder="Введите название курса"
                        value="<?= htmlspecialchars($old_data['course_name'] ?? '') ?>">
                </div>

                <div class="form-group mb-3">
                    <label for="start_date">Желаемая дата начала *</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required
                        value="<?= htmlspecialchars($old_data['start_date'] ?? '') ?>">
                </div>

                <div class="form-group mb-4">
                    <label>Способ оплаты *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <?php foreach ($payment_methods as $method): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="payment_<?= $method['id'] ?>"
                                name="payment_method" value="<?= $method['id'] ?>"
                                <?= (isset($old_data['payment_method']) && $old_data['payment_method'] == $method['id']) ? 'checked' : '' ?>
                                required>
                            <label class="form-check-label" for="payment_<?= $method['id'] ?>">
                                <?= htmlspecialchars($method['name']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">Отправить заявку</button>
            </form>

            <p class="text-center mt-3">
                <a href="my_applications.php">Вернуться к списку моих заявок</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>