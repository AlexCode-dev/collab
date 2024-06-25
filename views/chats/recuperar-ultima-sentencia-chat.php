<?php
$patron = '^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)^';
?>
<?php $username = ''; ?>
<?php foreach ($chat as $sentencia): ?>
    <?php if ($sentencia["username"] != $username): ?>
        <b><?php echo $sentencia["username"]; ?></b><br/>                      
        <?php $username = $sentencia["username"]; ?>
    <?php endif; ?>
    &nbsp;&nbsp;<?php echo preg_replace($patron, '<a href="$0" target="_blank">$0</a>', $sentencia["sentencia"]); ?>
    - <?php echo $sentencia["fecha_hora"]; ?>  
    <br/><br/>
<?php endforeach; ?>