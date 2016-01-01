<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        
        <?php if($discussionGroup['creater_id'] == $this->request->session()->read('Auth.User.id')) { ?>
            <li><?= $this->Html->link(__('Edit Group'), ['controller' => 'discussiongroups', 'action' => 'edit', $discussionGroup['id']]) ?> </li>
            <li><?= $this->Form->postLink(__('Delete Group'), ['controller' => 'discussiongroups', 'action' => 'delete', $discussionGroup['id']], ['confirm' => __('Are you sure you want to delete {0}?', $discussionGroup['name'])]) ?> </li>
        <?php } ?>
        <li><?= $this->Html->link(__('Main'), ['controller' => 'posts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?></li>
        <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?></li>
    </ul>
</div>
<div class="discussiongroups view large-10 medium-9 columns">
    <h2><?= h($discussionGroup['name']) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Title of Group') ?></h6>
            <p><?= h($discussionGroup['name']) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Creator') ?></h6>
            <p><?= h($discussionGroup['c']['first_name'] . ' ' . $discussionGroup['c']['last_name']) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($discussionGroup['created']) ?></p>
        </div>
    </div>
</div>
<div class="discussiongroups form large-10 medium-9 columns">
<?= $this->Form->create(null, ['url' => ['controller' => 'posts', 'action' => 'addGroupPost', $discussionGroup['id']]]) ?>
<?= $this->Form->input('content', ['label' => false]) ?>
<?= $this->Form->end() ?>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Members') ?></h4>
    <?php if (!empty($members)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Name') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($members as $member): ?>
        <tr>
            <td><?= $this->Html->link(h($member['first_name'] . ' ' . $member['last_name']), ['controller' => 'users', 'action' => 'view', $member['id']]) ?></td>
            <td class="actions">
            <?php if($discussionGroup['creater_id'] == $this->request->session()->read('Auth.User.id') && $member['id'] != $this->request->session()->read('Auth.User.id')) { ?>
                <?= $this->Form->postLink(__('Remove'), ['controller' => 'DiscussiongroupsUsers', 'action' => 'delete', $discussionGroup['id'], $member['id']], ['confirm' => __('Are you sure you want to remove this member?')]) ?>
            <?php } ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Posts') ?></h4>
    <?php if (!empty($posts)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Post') ?></th>
            <th><?= __('Author') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= $this->Html->link(h($post['content']), ['controller' => 'posts', 'action' => 'view', $post['id']]) ?></td>
            <td><?= $this->Html->link(h($post['c']['first_name'] . ' ' . $post['c']['last_name']), ['controller' => 'users', 'action' => 'view', $post['c']['id']]) ?></td>
            <td class="actions">
                <?php if($post['c']['id'] == $this->request->session()->read('Auth.User.id')) { ?>
                <?php } else if($post['likeId']['id'] > -1) { ?>
                <?= $this->Form->postLink(__('Unlike'), ['controller' => 'likes', 'action' => 'delete', $post['likeId']['id']]) ?>
                <?php } else { ?>
                <?= $this->Html->link(__('Like'), ['controller' => 'likes', 'action' => 'add', $post['id']]) ?>
                <?php } ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>