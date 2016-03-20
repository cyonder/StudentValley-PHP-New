<?php
require_once("./include/header.php");
?>
<div id="container" class="clearfix">
    <?php require_once("./include/sidebar.php"); ?>
    <div id="content">
        <?php $id = isset($_GET['SendTo']) ? (int)$_GET['SendTo']: 0; echo $id?>
        <?php //MAKE SURE OTHER USERS CANNOT SEND MESSAGE IF ACCOUNT IS FROZEN ?>
    </div>
</div>
<?php
require_once("./include/footer.php");
?>
