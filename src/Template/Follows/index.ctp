<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Follow'), ['action' => 'add']) ?></li>
    </ul>
</div>
<div class="follows index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('follower_id') ?></th>
            <th><?= $this->Paginator->sort('following_id') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($follows as $follow): ?>
        <tr>
            <td><?= $this->Number->format($follow->follower_id) ?></td>
            <td><?= $this->Number->format($follow->following_id) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $follow->follower_id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $follow->follower_id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $follow->follower_id], ['confirm' => __('Are you sure you want to delete # {0}?', $follow->follower_id)]) ?>
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
