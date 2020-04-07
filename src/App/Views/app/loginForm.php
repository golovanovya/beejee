<?php
/* @var $model \App\Models\LoginForm */
?>

<div class="text-center">
    <form method="POST" action="<?= $loginUri ?>" class="form-signin">
        <input type="hidden" name="__csrf" value="<?= $this->e($csrf) ?>">
        <h1 class="h5 mb-3 font-weight-normal">Пожайлуйста авторизуйтесь</h1>
        <label for="login" class="sr-only">Логин</label>
        <input type="text" 
               name="login" 
               class="form-control" 
               placeholder="Логин" 
               required="" 
               autofocus=""
               value="<?= $this->e($model->login) ?>">
        <label for="password" class="sr-only">Пароль</label>
        <input type="password" 
               name="password" 
               class="form-control" 
               placeholder="Пароль"
               required=""
               value="<?= $this->e($model->password) ?>">
        <?php if (!empty($errors)) : ?>
            <div class="text-danger">
                Пара логин/пароль некорректна! Попробуйте снова.
            </div>
        <?php endif; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
    </form>
</div>