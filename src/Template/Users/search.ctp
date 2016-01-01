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
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $this->Html->link($user->full_name, ['controller' => 'users', 'action' => 'view', $user->id]) ?></td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>

</div>
