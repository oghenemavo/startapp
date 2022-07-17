<form method="post" id="admin-form">
    <div>
        <label for="name">Name</label>
        <input id="name" name="full_name" value="<?php echo htmlspecialchars($user->full_name); ?>" />
        <div class="errors"> <?php echo isset($errors) ? $errors->first('full_name') : ''; ?> </div>
    </div>

    <div>
        <label for="username">username</label>
        <input id="username" name="username" value="<?php echo htmlspecialchars($user->username); ?>" />
        <div class="errors"> <?php echo isset($errors) ? $errors->first('username') : ''; ?> </div>
    </div>

    <div>
        <label for="email">email address</label>
        <input id="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" />
        <div class="errors"> <?php echo isset($errors) ? $errors->first('email') : ''; ?> </div>
    </div>

    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" />
        <?php if (isset($user->id)): ?><p>Leave blank to keep current password</p><?php endif; ?>
        <div class="errors"> <?php echo isset($errors) ? $errors->first('password') : ''; ?> </div>
    </div>

    <?php $is_same_user = $user->id == $auth->get_user()->id; ?>

    <div>
        <label for="is_active">
            <?php if ($is_same_user): ?>
                <input type="hidden" name="is_active" value="1" />
                <input type="checkbox" disabled="disabled" checked="checked" /> active

            <?php else: ?>
                <input id="is_active" name="is_active" type="checkbox" value="1"
                       <?php if ($user->is_active): ?>checked="checked"<?php endif; ?>/> active

            <?php endif; ?>
        </label>
    </div>

    <div>
        <label for="is_admin">
            <?php if ($is_same_user): ?>
                <input type="hidden" name="is_admin"  value="1" />
                <input type="checkbox" disabled="disabled" checked="checked" /> administrator

            <?php else: ?>
                <input id="is_admin" name="is_admin" type="checkbox" value="1"
                       <?php if ($user->is_admin): ?>checked="checked"<?php endif; ?>/> administrator

            <?php endif; ?>
        </label>
    </div>

    <div>
        <label for="is_group">Role</label>
        <select name="is_group" id="is_group" disabled>
            <?php $groups = ['Super Admin', 'Admin', 'Editor']; ?>

            <?php foreach ($groups as $group): ?>
                <option value="<?php echo $group; ?>" <?php echo $group == $user->is_group ? 'selected' : ''; ?>><?php echo $group; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <input type="submit" value="Save" />
    <a href="/admin/users<?php if (isset($user->id)) { echo '/show.php?id=' . $user->id; } ?>">Cancel</a>
</form>

<script>
    const adminForm = document.forms['admin-form'];
    const isAdmin = adminForm['is_admin'];
    const role = adminForm['is_group'];

    document.addEventListener('DOMContentLoaded', () => {
        if (isAdmin.checked) {
            role.disabled = false;
        } else {
            role.disabled = true;
        }

        isAdmin.addEventListener('change', () => {
            // console.log(this)
            if (isAdmin.checked) {
                role.disabled = false;
            } else {
                role.disabled = true;
            }
        });
    });

</script>