<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <?php if($this->request->session()->read('Auth.User.id') != $user['id']) { ?>
            <li><?= $this->Html->link(__('New Post'), ['controller' => 'posts', 'action' => 'addPostForUser', $user['id']]) ?></li>
        <?php } else { ?>
            <li><?= $this->Html->link(__('New Post'), ['controller' => 'posts', 'action' => 'add']) ?></li>
        <?php } ?>
        <li><?= $this->Html->link(__('Main'), ['controller' => 'posts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?></li>
        <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?></li>
        <?php if($user['id'] == $this->request->session()->read('Auth.User.id')) { ?>
        <li><?= $this->Html->link(__('Edit Account'), ['action' => 'edit', $user['id']]) ?> </li>
        <?php } else if(!$user['isFollowing']) { ?>
        <li><?= $this->Html->link(__('Follow'), ['controller' => 'Follows', 'action' => 'add', $user['id']]); ?></li>
        <?php } else if($user['isFollowing']) { ?>
        <li><?= $this->Form->postLink(__('Unfollow'), ['controller' => 'Follows', 'action' => 'delete', $user['id']]); ?></li>
        <?php } ?>
    </ul>
</div>
<div class="users view large-10 medium-9 columns">
    <h2><?= h($user->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Email') ?></h6>
            <p><?= h($user['email']) ?></p>
            <h6 class="subheader"><?= __('First Name') ?></h6>
            <p><?= h($user['first_name']) ?></p>
            <h6 class="subheader"><?= __('Last Name') ?></h6>
            <p><?= h($user['last_name']) ?></p>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Posts') ?></h4>
    <?php if (!empty($posts)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Author') ?></th>
            <th><?= __('Content') ?></th>
            <th><?= __('Created') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= h($post['a']['first_name']) ?></td>
            <td><?= h($post['posts']['content']) ?></td>
            <td><?= h($post['posts']['created']) ?></td>

            <td class="actions">
                <?php if($user['id'] == $this->request->session()->read('Auth.User.id')) { ?>
                <?php } else if(!empty($post['likeId'])) { ?>
                <?= $this->Form->postLink(__('Unlike'), ['controller' => 'likes', 'action' => 'delete', $post['likeId']['id']]) ?>
                <?php } else { ?>
                <?= $this->Html->link(__('Like'), ['controller' => 'likes', 'action' => 'add', $post['posts']['id']]) ?>
                <?php } ?>
                <?= $this->Html->link(__('View'), ['controller' => 'Posts', 'action' => 'view', $post['posts']['id']]) ?>
                <?php if($user['id'] == $this->request->session()->read('Auth.User.id')) { ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Posts', 'action' => 'delete', $post['posts']['id']], ['confirm' => __('Are you sure you want to delete # {0}?', $post['posts']['id'])]) ?>
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
    <h4 class="subheader"><?= __('Following') ?></h4>
    <?php if (!empty($followings)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Name') ?></th>
        </tr>
        <?php foreach ($followings as $following): ?>
        <tr>
            <td><?= $this->Html->link(h($following['first_name'] . ' ' . $following['last_name']), ['controller' => 'Users', 'action' => 'view', $following['id']]) ?></td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Followers') ?></h4>
    <?php if (!empty($followers)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Name') ?></th>
        </tr>
        <?php foreach ($followers as $follower): ?>
        <tr>
            <td><?= $this->Html->link(h($follower['first_name'] . ' ' . $follower['last_name']), ['controller' => 'Users', 'action' => 'view', $follower['id']]) ?></td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
<div>
    <?= "<img src=\"" . $user->profile_pic_path . "\"/>" ?>
</div>