<div class="container">
    <?php if(sizeof($pending) == 0): ?>
        <h3>No pending translations</h3>
    <?php else: ?>
        <h1>Approve by User</h1>
            <ul>
                <?php foreach($pending_by_user as $user): ?>
                    <li>
                        <?php echo htmlspecialchars($user->user_name); ?> (<?php echo $user->count; ?> pending translation<?php echo $user->count == 1 ? "" : "s"; ?>)
                        (<a href="<?php echo URL . "/admin/acceptall/{$user->user_id}"; ?>" onclick="return confirm('Accept all of <?php echo htmlspecialchars($user->user_name); ?>\'s translations')">Accept all</a>)
                        (<a href="<?php echo URL . "/admin/denyall/{$user->user_id}"; ?>" onclick="return confirm('Deny all of <?php echo htmlspecialchars($user->user_name); ?>\'s translations')">Deny all</a>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <h1>Approve by String</h1>
        <div class="pending-translations">
            <?php foreach($pending as $file => $file_translations): ?>
                <?php foreach($file_translations as $type => $type_translations): ?>
                    <h1>Category: <?php echo $group_names[$file][$type]; ?></h1>
                        <div class="pending-translations-cat">
                            <?php foreach($type_translations as $id => $translation_data): ?>
                                <div>
                                    <h1>ID: <?php echo $id; ?>
                                        <form name="admin_form_<?php echo $id; ?>">
                                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="user_id" value="-1">
                                            <input type="submit" value="Deny all for this ID">
                                        </form>
                                    </h1>
                                    <table>
                                        <thead>
                                            <tr>
                                                <td width="1%">User</td>
                                                <td width="33%">New translation</td>
                                                <td width="33%">Current translation</td>
                                                <td width="33%">Original Text:
                                                    <a href="javascript:void(0)" class="kor-link active" onclick="swapOriginal.call(this, 'kor');">Korean</a> |
                                                    <a href="javascript:void(0)" class="tw-link" onclick="swapOriginal.call(this, 'tw');">Taiwan</a> |
                                                    <a href="javascript:void(0)" class="hk-link" onclick="swapOriginal.call(this, 'hk');">Hong Kong</a>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($translation_data["new_translations"] as $nt_index => $new_translation): ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $new_translation["user_name"]; ?>
                                                    </td>
                                                    <td>
                                                        <form name="admin_form_<?php echo "{$id}_{$nt_index}"; ?>">
                                                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                                                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                                                            <input type="hidden" name="id" value="<?php echo str_replace(".", "_", $id); ?>">
                                                            <input type="hidden" name="user_id" value="<?php echo $new_translation["user_id"]; ?>">
                                                            <textarea name="string"><?php echo htmlspecialchars($new_translation["translation"]); ?></textarea><br>
                                                            <input type="submit" value="Accept">
                                                        </form>
                                                    </td>
                                                    <td class="old-translation">
                                                        <?php echo htmlspecialchars(isset($translation_data["old_translation"]) ? $translation_data["old_translation"] : ""); ?>
                                                    </td>
                                                    <?php if($nt_index == 0): ?>
                                                        <td rowspan="<?php echo sizeof($translation_data["new_translations"]); ?>">
                                                            <span class="kor-text active">
                                                                <?php echo isset($translation_data["kor"]) ? mb_convert_encoding($translation_data["kor"], "HTML-ENTITIES", "UTF8") : "Text not available in this version"; ?>
                                                            </span>
                                                            <span class="tw-text">
                                                                <?php echo isset($translation_data["tw"]) ? mb_convert_encoding($translation_data["tw"], "HTML-ENTITIES", "UTF8") : "Text not available in this version"; ?>
                                                            </span>
                                                            <span class="hk-text">
                                                                <?php echo isset($translation_data["hk"]) ? mb_convert_encoding($translation_data["hk"], "HTML-ENTITIES", "UTF8") : "Text not available in this version"; ?>
                                                            </span>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?php echo URL; ?>/js/jsdiff.js"></script>
<script src="<?php echo URL; ?>/js/application.js"></script>