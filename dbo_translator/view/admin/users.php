<div class="container">
    <h1>Users</h1>
    <ul>
    <?php foreach($users as $user): ?>
        <li>
            <?php echo htmlspecialchars($user->user_name); ?>
            <?php if($user->user_id == HEAD_ADMIN): ?>
                (<b>Head Admin</b>)
            <?php else: ?>
                <?php if($user->isAdmin): ?>
                    (<b>Admin</b>)
                    <?php if($this->model->userIsHeadAdmin()): ?>
                        (<a href="<?php echo URL . "/admin/revokeadmin/{$user->user_id}"; ?>">Revoke Admin</a>)
                    <?php endif; ?>
                <?php else: ?>
                    <?php if($user->hasAccess): ?>
                        (<b>Has access</b>)
                        (<a href="<?php echo URL . "/admin/revokeaccess/{$user->user_id}"; ?>">Revoke Access</a>)
                    <?php else: ?>
                        (<a href="<?php echo URL . "/admin/grantaccess/{$user->user_id}"; ?>">Grant Access</a>)
                    <?php endif; ?>
                    <?php if($this->model->userIsHeadAdmin()): ?>
                        (<a href="<?php echo URL . "/admin/setadmin/{$user->user_id}"; ?>">Make Admin</a>)
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</div>