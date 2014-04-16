<?php

function showMenuItem($item) {
    ?>
    <li>
        <?php if ($item->link) { ?>
            <a href="<?php echo $item->link; ?>">
                <?php echo $item->text; ?>
            </a>
        <?php } else { ?>
            <span>
                <?php echo $item->text; ?>
            </span>
        <?php } ?>
        <?php if (!empty($item->children)) { ?>
            <ul class="submenu navbar-inverse">
                <?php foreach ($item->children as $submenu) { ?>
                    <?php showMenuItem($submenu); ?>
                <?php } ?>
            </ul>
        <?php } ?>
    </li>
<?php } ?>
