<div class="container">
    <?php foreach($groups as $group): ?>
        <h1 class="<?php echo $group["color"]; ?>"><?php echo $group["title"]; ?></h1>
        <p class="group-options <?php echo $group["color"]; ?>">
            <?php foreach($group["members"] as $member): ?>
                <a href="<?php echo URL . "/translations/view/1/{$member[0]}/{$member[1]}/"; ?>">
                    <?php echo $group_names[$member[0]][$member[1]]; ?>
                    <span class="complete">
                        <?php if(isset($complete[$member[0]][$member[1]])): ?>
                            <?php echo "{$complete[$member[0]][$member[1]]["translated"]} / " .
                                       "{$complete[$member[0]][$member[1]]["total"]} " .
                                       "({$complete[$member[0]][$member[1]]["percent"]}% finished)"; ?>
                        <?php else: ?>
                            Not started yet
                        <?php endif; ?>
                    </span>
                </a>
            <?php endforeach; ?>
            <br>
            <br>
        </p>
    <?php endforeach; ?>
</div>
