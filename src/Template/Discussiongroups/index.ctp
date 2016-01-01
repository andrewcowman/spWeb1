<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Discussion Group'), ['controller' => 'Discussiongroups', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('Main'), ['controller' => 'posts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?></li>
        <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?></li>
    </ul>
</div>
<div class="discussiongroups view large-10 medium-9 columns">
            <?= $this->Form->create(null, [
                'url' => [
                    'controller' => 'discussiongroups',
                    'action' => 'search'
                ],
                'type' => 'get'
            ]) ?>
            <?= $this->Form->input('search', ['type' => 'text', 'label' => 'Search Groups']) ?>
            <?= $this->Form->end() ?>
    <h4 class="subheader"><?= __('Your Groups') ?></h4>
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Name of Group</th>
            <th>Creator</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($joinedGroups as $discussiongroup): ?>
        <tr>
            <td><?= $this->Html->link(h($discussiongroup['name']), ['action' => 'view', $discussiongroup['id']]) ?></td>
            <td><?= h($discussiongroup['c']['first_name'] . ' ' . $discussiongroup['c']['last_name']) ?></td>
            <td><?= h($discussiongroup['created']) ?></td>
            <td class="actions">
                <?php if($discussiongroup['creater_id'] != $this->request->session()->read('Auth.User.id')) { ?>
                    <?= $this->Form->postLink(__('Leave'), ['controller' => 'discussiongroupsusers', 'action' => 'delete', $discussiongroup['id']]) ?>
                <?php } ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
<div class="discussiongroups view large-10 medium-9 columns">
    <h4 class="subheader"><?= __('Recommended Groups') ?></h4>
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Name of Group</th>
            <th>Creator</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($availableGroups as $discussiongroup): ?>
        <tr>
            <td><?= $this->Html->link(h($discussiongroup['name']), ['action' => 'view', $discussiongroup['id']]) ?></td>
            <td><?= h($discussiongroup['c']['first_name'] . ' ' . $discussiongroup['c']['last_name']) ?></td>
            <td><?= h($discussiongroup['created']) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('Join'), ['controller' => 'discussiongroupsusers', 'action' => 'add', $discussiongroup['id']]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
</div>
