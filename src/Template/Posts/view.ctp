<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <?php if($post->user_id == $this->request->session()->read('Auth.User.id')) { ?>
            <li><?= $this->Form->postLink(__('Delete Post'), ['action' => 'delete', $post->id], ['confirm' => __('Are you sure you want to delete this post?')]) ?> </li>
        <?php } ?>
        <li><?= $this->Html->link(__('Add Comment'), ['controller' => 'comments', 'action' => 'add', $post->id]) ?></li>
        <li><?= $this->Html->link(__('Main'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?></li>
        <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?></li>
    </ul>
</div>
<div class="posts view large-10 medium-9 columns">
    <h2><?= h($post->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $post->has('user') ? $this->Html->link($post->user->full_name, ['controller' => 'Users', 'action' => 'view', $post->user->id]) : '' ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($post->created) ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Content') ?></h6>
            <?= $this->Text->autoParagraph(h($post->content)) ?>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Comments') ?></h4>
    <?php if (!empty($post->comments)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Comment') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($post->comments as $comment): ?>
        <tr>
            <td><?= h($comment->content) ?></td>

            <td class="actions">
                <?php if($comment->user_id == $this->request->session()->read('Auth.User.id')) { ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Comments', 'action' => 'delete', $comment->id], ['confirm' => __('Are you sure you want to delete this comment?', $comment->id)]) ?>
                <?php } ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>