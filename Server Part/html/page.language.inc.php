<?php

if (isset($_GET['action'])) {

    ?>

    <div class="box-body">
        <?php

        foreach ($LANGS as $name => $val) {

            echo "<a onclick=\"App.setLanguage('$val'); return false;\" class=\"box-menu-item \" href=\"javascript:void(0)\">$name</a>";
        }

        ?>
    </div>

    <div class="box-footer">
        <div class="controls">
            <button onclick="$.colorbox.close(); return false;" class="primary_btn blue"><?php echo $LANG['action-close']; ?></button>
        </div>
    </div>

<?php
}
