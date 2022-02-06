<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/common_res.php'; ?>

<div class="container">
    <div class="row h-100 align-items-center">
        <div class="col-4 offset-4">
            <div class="d-flex justify-content-center">
            <h2>Sign in</h2>
            </div>
            <form action="<?= URLROOT; ?>/users_auth/login" method="POST">
                <div class="form-group">
                    <div class="col-8 offset-2 mb-2">
                        <label for="username">Username</label>
                        <input type="text" class="form-control <?php if($data['usernameError']) { ?>is-invalid<?php } ?>" 
                                id="username" name="username" placeholder="Enter username"
                                value="<?php echo ($_POST['username'] ? trim($_POST['username']) : $data['username']); ?>">
                        <div class="invalid-feedback"><?= $data['usernameError']; ?></div>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <div class="col-8 offset-2">
                        <label for="password">Password</label>
                        <input type="password" class="form-control <?php if($data['passwordError']) { ?>is-invalid<?php } ?>" 
                                id="password" name="password" placeholder="Password" 
                                value="<?php echo ($_POST['pasword'] ? trim($_POST['password']) : $data['password']); ?>">
                        <div class="invalid-feedback"><?= $data['passwordError']; ?></div>
                    </div>
                </div>
                <div class="col-8 offset-2 mb-2">
                    <button type="submit" class="btn btn-primary col-12">Sign in</button>
                </div>
                <div class="col-8 offset-2">
                    <p class="text-center">First time here?<br/><a href="<?= URLROOT ?>/users_auth/register">Let`s register!</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
