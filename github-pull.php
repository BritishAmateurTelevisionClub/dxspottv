<?php
if (isset($_REQUEST['pass'])) {
    if ($_REQUEST['pass']=="updateMeN0w") {
        $output = shell_exec('cd /srv/www.dxspot.tv/ && git pull 2>&1');
        echo "<pre>$output</pre>";
    }
}
?>
