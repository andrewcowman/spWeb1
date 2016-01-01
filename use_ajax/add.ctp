<script>
function submitPost() {
    
    var data = {content: $('#post-content').val()};
    
    $.ajax({
        type: 'post',
        data: data,
        url: '/spweb-ajax/posts/add_ajax',
        success: function(result) {
            $('#test-id').html(result);
        }
    });
}
</script>

<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Main'), ['controller' => 'posts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?></li>
        <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?></li>
    </ul>
</div>
<div class="posts form large-10 medium-9 columns">
    <?= $this->Form->create($post, ['id' => 'test-id']) ?>
    <fieldset>
        <legend><?= __('Add Post') ?></legend>
        <?php
            echo $this->Form->input('post-content');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit'), ['type' => 'button', 'onclick' => 'submitPost();']) ?>
    <?= $this->Form->end() ?>
</div>
