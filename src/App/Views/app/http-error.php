<?php
/* @var $this \League\Plates\Template\Template */
/* @var $e \League\Route\Http\Exception\HttpExceptionInterface */
?>
<div class="text-center">
    <h1>Ошибка <?= $this->e($e->getStatusCode()) ?></h1>
    <p><?= $this->e($e->getMessage()) ?></p>
</div>
