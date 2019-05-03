<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo isset($title) ? $title : "DBO Translation Tool"; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo URL; ?>/css/style.css" rel="stylesheet">
</head>
<body>
<div class="page-wrapper">
<div class="navigation">
    <a href="<?php echo URL; ?>" class="button"><span>Home</span></a>
    <a href="<?php echo URL; ?>/download/approved" class="button"><span>Download translations file</span></a>
    <a href="<?php echo URL; ?>/download/personal" class="button"><span>Download your pending translations file</span></a>
    <?php if($this->model->userIsAdmin()): ?>
        <a href="<?php echo URL; ?>/admin" class="button"><span>Manage Translations</span></a>
        <a href="<?php echo URL; ?>/admin/users" class="button"><span>Manage Users</span></a>
    <?php endif; ?>
    <div class="user">
        Signed in as <?php echo htmlspecialchars($this->model->getUserName()); ?> (<a href="<?php echo URL; ?>/?logout">Log out</a>)
    </div>
</div>
