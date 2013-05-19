<?php
if (isset($_REQUEST['pass'])) {
    if ($_REQUEST['pass']=="updateMeN0w") {
        shell_exec('cd /srv/www.dxspot.tv/ && git pull');
    }
}
?>
