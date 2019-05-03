<div class="container">
    <h1><?php echo $title; ?></h1>
    <div class="translation-guide">
        <p>
            To propose a translation for the English patch: enter your translation into the fields below, and then click
            submit. Before your submittion is reviewed, you'll have the chance to modify it by returning to this page,
            making changes to the fields, and then hitting submit again.  <b>If after you submit your translations and
            the page reloads, you don't see them, then they were automatically denied for one of the reasons listed
            below.</b>
        </p>
        <p>
            Much like forum BB code, there's a few tags you can use to control how the text is displayed in-game.  Also
            like BB code, all of these tags are enclosed in brackets, as you can see in the examples below.  <b>In most
            cases though, you should just copy the effect over from the untranslated text.</b>  Examples:
            <ul>
                <li>
                    <span class="highlight">[align = ""center""]</span> will center all of the text that comes after it
                </li>
                <li>
                    <span class="highlight">[font size = ""##"" color = ""######""]</span>text
                    <span class="highlight">[/font]</span> will color and size the font between the font tags to the
                    values set in quotations
                </li>
                <li>
                    <span class="highlight">[br]</span> puts everything after it on a new line (like hitting the
                    enter key)
                </li>
            </ul>
        </p>
        <p>
            When translating, you may come across parts of the text that are colored red and bolded.  These represent
            placeholders that the game will fill in with certain values. For example, it might say
            "<span class="highlight">%d</span> Wins". That means that, in-game, "%d" will be replaced by the # of wins.
            If there is a placeholder in the untranslated text, then it must also be in the English translation,
            elsewise the translation will be automatically rejected.
        </p>
        <p>
            In some cases, the characters <span class="highlight">"</span> and <span class="highlight">%</span> may
            appear twice in a row, as <span class="highlight">""</span> and <span class="highlight">%%</span>.
            When you see this, you must also replicate it in your translation.
        </p>
    </div>
    <div class="line"></div>
    <div class="translation-menu">
        <div>
            <label>Untranslated Version: </label>
            <?php echo $version == 1 ? "<span class=\"active\">" : "<a href=\"" . URL . "/translations/view/1/{$file}/{$type}/{$page}/{$sub_page}/{$mode}/{$search_mode}/{$search_query}\">"; ?>Korea<?php echo $version == 1 ? "</span>" : "</a>"; ?> |
            <?php echo $version == 2 ? "<span class=\"active\">" : "<a href=\"" . URL . "/translations/view/2/{$file}/{$type}/{$page}/{$sub_page}/{$mode}/{$search_mode}/{$search_query}\">"; ?>Taiwan<?php echo $version == 2 ? "</span>" : "</a>"; ?> |
            <?php echo $version == 3 ? "<span class=\"active\">" : "<a href=\"" . URL . "/translations/view/3/{$file}/{$type}/{$page}/{$sub_page}/{$mode}/{$search_mode}/{$search_query}\">"; ?>Hong Kong<?php echo $version == 3 ? "</span>" : "</a>"; ?>
        </div>
        <div>
            <?php if($file == 0 && !isset($search_query)): ?>
                <label>IDs starting with the letter: </label>
                <?php $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"); ?>
                <?php for($i = 0; $i < sizeof($alphabet); $i++): ?>
                    <?php if($page == $i + 1): ?>
                        <b><?php echo $alphabet[$i]; ?></b>
                        <?php echo $i != sizeof($alphabet) - 1 ? "|" : ""; ?>
                    <?php else: ?>
                        <a href="<?php echo URL . "/translations/view/{$version}/{$file}/{$type}/" . ($i + 1) . "/1/{$mode}/{$search_mode}/{$search_query}"; ?>"><?php echo $alphabet[$i]; ?></a>
                        <?php echo $i != sizeof($alphabet) - 1 ? "|" : ""; ?>
                    <?php endif; ?>
                <?php endfor; ?>
            <?php else: ?>
                <form name="searchform" action="" method="post">
                    <label>Jump to page: </label>
                    <input name="pagenum" type="text" value="" style="width: 50px; padding-left: 2px;">
                    <input type="submit" value="Jump">
                    (Total number of pages: <?php echo $num_pages; ?>)
                </form>
            <?php endif; ?>
        </div>
        <div>
            <form name="searchform" action="" method="post">
                <label>Text Search: </label>
                <input name="query" type="text" maxlength="30" value="<?php echo htmlspecialchars(isset($search_query) ? $search_query : ""); ?>" style="width: 250px; padding-left: 2px;">
                <input type="radio" name="options" id="searchradio1" value="1" <?php echo ($search_mode != 2) ? "checked" : ""; ?>><label for="searchradio1">Search Translated and Untranslated Text</label>
                <input type="radio" name="options" id="searchradio2" value="2" <?php echo ($search_mode == 2) ? "checked" : ""; ?>><label for="searchradio2">Search ID</label>
                <input type="submit" value="Search">
                <?php if(isset($search_query)): ?>
                    | <a href="<?php echo URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/"; ?>">Clear search</a>
                <?php endif; ?>
            </form>
        </div>
        <div>
            <label>Page: </label>
            <?php if($sub_page - 10 > 1): ?>
                ... |
            <?php endif; ?>
            <?php for($i = $sub_page - 10; $i <= $sub_page + 10; $i++): ?>
                <?php if($i > 0 && $i <= $num_pages): ?>
                    <?php if($i == $sub_page): ?>
                        <b><?php echo $i; ?></b> |
                    <?php else: ?>
                        <a href="<?php echo URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/{$i}/{$mode}/{$search_mode}/{$search_query}"; ?>"><?php echo $i; ?></a> |
                    <?php endif; ?>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if($sub_page + 10 < $num_pages): ?>
                ... |
            <?php endif; ?>
            <?php if($mode == 1): ?>
                <b>Untranslated</b> |
            <?php else: ?>
                <a href="<?php echo URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/1/1/{$search_mode}/{$search_query}"; ?>">Untranslated</a> |
            <?php endif; ?>
            <?php if($mode == 2): ?>
                <b>Untranslated and without Pending</b>
            <?php else: ?>
                <a href="<?php echo URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/1/2/{$search_mode}/{$search_query}"; ?>">Untranslated and without Pending</a>
            <?php endif; ?>
            <?php if($mode != 0): ?>
                | <a href="<?php echo URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/1/0/{$search_mode}/{$search_query}"; ?>">Remove Untranslated/Pending restriction</a>
            <?php endif; ?>
        </div>
    </div>
    <?php if($strings === false || $num_pages == 0): ?>
        <h2>No strings found</h2>
    <?php else: ?>
        <form name="editform" action="" method="post">
            <table class="translation-list">
                <thead>
                    <tr>
                        <td width="20%">ID</td>
                        <td width="40%">Untranslated Text</td>
                        <td width="40%">English Translation</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($strings as $string_data): ?>
                        <tr>
                            <td>
                                <?php echo str_replace("DST_", "", $string_data->id); ?>
                            </td>
                            <td>
                                <?php if(isset($string_data->string)): ?>
                                    <?php echo mb_convert_encoding($this->model->highlightVariables($string_data->string), "HTML-ENTITIES", "UTF-8"); ?>
                                <?php else: ?>
                                    <span class="highlight">This string doesn't exist in this version</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($string_data->num_pending > 0): ?>
                                    <div class="small blue highlight">
                                        <?php if(isset($string_data->ustring)): ?>
                                            (This translation is pending approval)
                                        <?php else: ?>
                                            (<?php echo $string_data->num_pending; ?> pending translation<?php echo $string_data->num_pending == 1 ? "" : "s"; ?>)
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($string_data->string)): ?>
                                    <textarea name="game_string_<?php echo str_replace(".", "_", $string_data->id); ?>"><?php echo htmlspecialchars(isset($string_data->ustring) ? $string_data->ustring : $string_data->tstring); ?></textarea>
                                <?php else: ?>
                                    <?php echo htmlspecialchars(isset($string_data->ustring) ? $string_data->ustring : $string_data->tstring); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <input type="submit" value="Submit Translations" style="width: 100%; height: 32px;">
        </form>
    <?php endif; ?>
</div>