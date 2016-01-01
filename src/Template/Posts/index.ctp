<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Post'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('New Group'), ['controller' => 'Discussiongroups', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('View Groups'), ['controller' => 'Discussiongroups', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?></li>
        <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?></li>
    </ul>
</div>
<div class="posts index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>For User</th>
            <th>Date</th>
            <th>Post</th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td>
                <?= $this->Html->link($post['author']['first_name'] . ' ' . $post['author']['last_name'], ['controller' => 'Users', 'action' => 'view', $post['author']['id']]) ?>
            </td>
            <td><?php if(!empty($post['f']['first_name'])) {echo h($post['f']['first_name'] . ' ' . $post['f']['last_name']);} ?></td>
            <td><?= h($post['posts']['created']) ?></td>
            <td><?= $this->Html->link(h($post['posts']['content']), ['action' => 'view', $post['posts']['id']]) ?></td>
            <td class="actions">
                <?php if($post['posts']['user_id'] == $this->request->session()->read('Auth.User.id')) { ?>
                <?php } else if(!empty($post['likeId'])) { ?>
                <?= $this->Form->postLink(__('Unlike'), ['controller' => 'likes', 'action' => 'delete', $post['likeId']['id']]) ?>
                <?php } else { ?>
                <?= $this->Html->link(__('Like'), ['controller' => 'likes', 'action' => 'add', $post['posts']['id']]) ?>
                <?php } ?>
                <?= $this->Html->link(__('Comment'), ['controller' => 'comments', 'action' => 'add', $post['posts']['id']]) ?>
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
<div class="posts index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Notification</th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($notifications as $notification): ?>
        <tr>
            <td><?= $notification['nl']['description'] ?></td>
            <td class="actions">
                <?= $this->Form->postLink(__('X'), ['controller' => 'notifications', 'action' => 'delete', $notification['id']]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
</div>