<?php
/* @var $pager \App\Models\Paginator */
/* @var $notice \App\Notice */

$reverseSort = function ($attribute) use ($direction) {
    if ($direction === SORT_ASC) {
        return "$attribute-";
    } else {
        return $attribute;
    }
};
$getIcon = function ($attribute) use ($sort, $direction) {
    if ($attribute === $sort) {
        if ($direction === SORT_ASC) {
            return '<img src="/img/arrow-down.svg">';
        } elseif ($direction === SORT_DESC) {
            return '<img src="/img/arrow-up.svg">';
        }
    }
    return '';
};
$getSortUrl = function ($attribute, bool $reverse = true, bool $pagination = true) use ($sort, $reverseSort, $pager) {
    $name = $sort === $attribute && $reverse ? $reverseSort($sort) : $attribute;
    $pagination = $pager->getPage() === 1 || !$pagination ? '' : '/page/' . $pager->getPage();
    return sprintf('/sort/%s%s', $name, $pagination);
};
?>
    
<div class="d-flex p-2">
    <a href="/create" class="btn btn-primary js-form-modal">Добавить</a>
</div>
<table class="table">
    <thead>
        <tr>
            <th scope="col" width="20%"><a href="<?= $getSortUrl('name') ?>">имя пользователя <?= $getIcon('name') ?></a></th>
            <th scope="col" width="20%"><a href="<?= $getSortUrl('email') ?>">e-mail <?= $getIcon('email') ?></th>
            <th scope="col" width="40%">текст задачи</a></th>
            <th scope="col" width="10%"><a href="<?= $getSortUrl('status') ?>">статус <?= $getIcon('status') ?></a></th>
            <th scope="col" width="5%"></th>
            <th scope="col" width="5%"></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($jobs as $i => $job) : ?>
        <tr>
            <td scope="col"><?= $this->e($job['name']) ?></td>
            <td scope="col"><?= $this->e($job['email']) ?></td>
            <td scope="col"><?= $this->e($job['content']) ?></td>
            <td scope="col"><?= $job['status'] ? '<img src="/img/check.svg" alt="Выполнена">' : '' ?></td>
            <td scope="col"><?= $job['edited_by_admin'] ? '<img src="/img/edit-3.svg" alt="Редактировалось администратором">' : '' ?></td>
            <td scope="col">
                <?php if ($isAdmin) : ?>
                <a href="/update/<?=$job['id']?>" title="Редактировать"><img src="/img/edit.svg"></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php $this->insert(
    'widget/pager',
    [
        'pager' => $pager,
        'main_route' => '/',
        'route' => empty($sort) ?
            '/page/%s' :
            $getSortUrl($sort, false, false) . '/page/%s',
    ]
); ?>
