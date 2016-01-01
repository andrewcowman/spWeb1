<div class="actions columns large-2 medium-3">
    <h3><?=  __('Actions') ?></h3>
    <ul class="side-nav">
    </ul>
</div>
<div class="users index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($discussionGroups as $group): ?>
        <tr>
            <td><?= $this->Html->link($group->name, ['controller' => 'discussiongroups', 'action' => 'view', $group->id]) ?></td>
            <td><?= $this->Html->link(__('Join'), ['controller' => 'discussiongroupsusers', 'action' => 'add', $group->id]) ?></td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>

</div>
