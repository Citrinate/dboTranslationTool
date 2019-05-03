<?php

class Model
{
    private $_login = null;
    private $_page_size = 20;
    private $_output_file = "translation.txt";
    private $_version_name_map = array(
        1 => "kor",
        2 => "tw",
        3 => "hk"
    );
    private $_file_name_map = array(
        0 => ".\\language\\local_data.dat",
        1 => ".\\language\\local_sync_data.dat",
        2 => ".\\data\\o_table_text_all_data.edf",
        3 => ".\\data\\o_table_quest_text_data.edf"
    );
    private $_group_names = array(
        0 => array(
            0 => "User Interface Text" // language\local_data.dat strings
        ),
        1 => array(
            0 => "User Interface Error Messages" // language\local_sync_data.dat strings
        ),
        2 => array( /* data\o_table_text_all_data.edf string groups */
            0 => "Social Skill Names",
            1 => "Emote Slash Commands",
            2 => "HTB Text",
            3 => "Non-equiptable Item Names & Effects",
            4 => "Item Slot Names",
            5 => "Monster Names & Monster Dialog",
            6 => "NPC Names & NPC Dialog",
            7 => "Skill Names & Skill Flavor Text",
            8 => "Item Effects & Skill Effects",
            9 => "Possibly Vehicle Rental Status",
            10 => "World Location Names 1",
            11 => "Object Names",
            12 => "Quest Item Names",
            13 => "Lots of Various Text",
            14 => "Menu Titles",
            15 => "Possibly Tutorial Pop-up Text",
            16 => "Eternal Dragon Dialog",
            17 => "Eternal Dragon Wish Choices",
            18 => "TMQ/TLQ Text",
            19 => "TMQ/TLQ Objectives",
            20 => "World Location Names 2",
            21 => "***********",
            22 => "Unknown Dialog",
            23 => "Quest Status",
            24 => "Loading Tips",
            25 => "DWC Text",
            26 => "Player Titles",
            27 => "Instance Names"
        ),
        3 => array( /* data\o_table_quest_text_data.edf string groups */
            0 => "Quest Text"
        ),
        4 => array(
            0 => "Help Files"
        )
    );

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * getters and setters
     */

    public function getGroupNames() { return $this->_group_names; }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param object $db A PDO database connection
     */
    function __construct($db)
    {
        try {
            $this->db = $db;
        } catch (PDOException $e) {
            exit('Database connection could not be established.');
        }

        require_once(APP . "classes/Login.php");
        $this->_login = new Login();
    }

    public function isLoggedIn()
    {
        return $this->_login->isUserLoggedIn();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Determine if user has permission to submit translations
     *
     * @return bool
     */
    public function userHasAccess()
    {
        return (
            (isset($_SESSION['hasAccess']) && $_SESSION['hasAccess'] == 1)
            || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1)
            || (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == HEAD_ADMIN)
        );
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Determine if user has admin permissions
     *
     * @return bool
     */
    public function userIsAdmin()
    {
        return (
            isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1
            || (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == HEAD_ADMIN)
        );
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Determine if user has head admin permissions
     *
     * @return bool
     */
    public function userIsHeadAdmin()
    {
        return (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == HEAD_ADMIN);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getUserID()
    {
        return $_SESSION['user_id'];
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getUserName()
    {
        return $_SESSION['user_name'];
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function getGroupCompletion()
    {
        $count = array();

        { /* fetch counts of untranslated and translated strings */

            $sql = "
              SELECT
                file,
                type,
                (en IS NOT NULL) as translated,
                count(*) as count
              FROM game_strings
              GROUP BY file, type, (en IS NOT NULL)
            ";

            $query = $this->db->prepare($sql);
            $query->execute();
            foreach($query->fetchAll() as $row) {
                $count[$row->file][$row->type][$row->translated ? "translated" : "untranslated"] = $row->count;
            }
        }

        { /* build totals and percentages */

            foreach($count as $file_id => $file_data) {
                foreach($file_data as $group_id => $group_data) {
                    if(!isset($group_data["translated"]) || !isset($group_data["untranslated"])) {
                        if(isset($group_data["translated"])) {
                            $group_data["untranslated"] = 0;
                        } else {
                            unset($count[$file_id][$group_id]);
                            continue;
                        }
                    }

                    $count[$file_id][$group_id]["total"] = $group_data["translated"] + $group_data["untranslated"];
                    $count[$file_id][$group_id]["percent"] = number_format(100 * ($group_data["translated"] / $count[$file_id][$group_id]["total"]), 2);
                }
            }
        }

        return $count;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $version
     * @return bool|int|string
     */
    private function _convertVersion($version)
    {
        $temp = is_numeric($version) ? $this->_version_name_map : array_flip($this->_version_name_map);
        if(isset($temp[$version])) {
            return $temp[$version];
        }

        return false;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $page
     * @return bool
     */
    private function _getAlphabeticPageNumber($page)
    {
        $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H",
                          "I", "J", "K", "L", "M", "N", "O", "P",
                          "Q", "R", "S", "T", "U", "V", "W", "X",
                          "Y", "Z");

        if(!isset($alphabet[$page - 1])) {
            return false;
        } else {
            return $alphabet[$page - 1];
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $version
     * @param $file
     * @param $type
     * @param $page
     * @param $sub_page
     * @param $mode
     * @param $search_mode
     * @param $search_query
     * @return bool
     */
    public function getLanguageStrings($version, $file, $type, $page, $sub_page, $mode, $search_mode, $search_query)
    {
        /* build limit starting point */
        $first_result = $sub_page <= 0 ? 0 : ($sub_page - 1) * $this->_page_size;

        /* sanitize inputs */
        $version_name = $this->_convertVersion($version);
        if($version_name === false) {
            return false;
        }

        /* we only want to fetch untranslated strings */
        $untranslated = "";
        if($mode == 1 || $mode == 2) {
            $untranslated = "AND t1.en IS NULL AND t2.string IS NULL";
        }

        /* we also don't want to show any items that have pending translations */
        $not_pending = "";
        if($mode == 2) {
            $not_pending = "HAVING num_pending = 0";
        }

        /* .dat files are broken down into pages based on their names */
        $name_filters = "";
        if($file == 0 || $file == 1) {
            $page = $this->_getAlphabeticPageNumber($page);
            if($page === false) return false;
            $name_filters = "AND t1.id LIKE 'DST_{$page}%'";
        }

        /* search for strings by name/id
        this also overrides the name filters above */
        $search_filter = "";
        $search_parameters = array();
        if(isset($search_query)) {
            if($search_mode == 1) {
                /* search translated/untranslated text */
                $search_filter = "AND ({$version_name} LIKE :search_string OR en LIKE :eng_search_string)";
                $search_parameters = array(":search_string" => "%{$search_query}%", ":eng_search_string" => "%{$search_query}%");
                $name_filters = "";
            } else if($search_mode == 2) {
                /* search id */
                if(is_numeric($search_query)) {
                    $search_filter = "AND t1.id >= {$search_query}";
                } else {
                    $search_filter = "AND t1.id LIKE :search_id";
                    $search_parameters = array(":search_id" => "%$search_query%");
                    /* override name filters */
                    $name_filters = "";
                }
            }
        }

        /* build query */
        $sql = "
          SELECT SQL_CALC_FOUND_ROWS
            t1.id,
            t1.{$version_name} as string,
            t1.en as tstring,
            t2.string as ustring,
            (SELECT count(*) FROM game_strings_temp WHERE (file = t1.file AND id = t1.id AND type = t1.type AND status = 0)) as num_pending
          FROM game_strings t1
          LEFT JOIN game_strings_temp t2 ON (t1.file = t2.file AND t1.id = t2.id AND t1.type = t2.type AND t2.user_id = :userid AND t2.status = 0)
          WHERE (
            t1.file = :file AND
            t1.type = :type
            {$name_filters}
            {$untranslated}
            {$search_filter}
          )
          {$not_pending}
          ORDER BY t1.id + 0 ASC
          LIMIT {$first_result}, {$this->_page_size}
        ";

        $parameters = array(":userid" => $this->getUserID(), ":file" => $file, ":type" => $type) + $search_parameters;
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
        return $query->fetchAll();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getNumPages()
    {
        $sql = "SELECT FOUND_ROWS()";
        $query = $this->db->prepare($sql);
        $query->execute();
        return ceil($query->fetchAll()[0]->{"FOUND_ROWS()"} / $this->_page_size);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $string
     * @return mixed
     */
    public function highlightVariables($string)
    {
        $open_tag = '<span class="highlight">';
        $close_tag = '</span>';

        $string = preg_replace("([%][a-z])", "{$open_tag}$0{$close_tag}", $string);
        $string = str_replace('%.0f', "{$open_tag}%.0f{$close_tag}", $string);
        return $string;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $version
     * @param $file
     * @param $type
     * @return bool
     */
    public function submitStrings($version, $file, $type)
    {
        /* sanitize inputs */
        $version_name = $this->_convertVersion($version);
        if($version_name === false) {
            return false;
        }

        /* convert translations being submitted */
        $new_translations = array();
        foreach($_POST as $id => $value) {
            if(strpos($id, "game_string_") == 0 && !empty($value)) {
                $string_id = str_replace("game_string_", "", $id);
                if($file == 4) $string_id = str_replace("_", ".", $string_id); // ids are filenames, replace underscores with dots
                $value = mb_convert_encoding($value, "UTF-8", "HTML-ENTITIES");
                $new_translations[$string_id] = $value;
            }
        }

        { /* insert the translations */

            $check_existing_sql = "
              SELECT
                {$version_name} as string,
                en as tstring
              FROM game_strings
              WHERE (
                file = :file AND
                type = :type AND
                id = :id
              )
            ";

            $insert_pending_sql = "
              INSERT INTO game_strings_temp (file, type, id, user_id, string, date_added)
              VALUES(:file, :type, :id, :user_id, :string, :current_time)
              ON DUPLICATE KEY UPDATE
                string = VALUES(string)
            ";

            foreach($new_translations as $id => $value) {
                /* verify that the translation is new, and validate that it's in the correct form */
                $parameters = array(":file" => $file, ":type" => $type, ":id" => $id);
                $query = $this->db->prepare($check_existing_sql);
                $query->execute($parameters);
                $existing_string = $query->fetchAll();

                if(
                    isset($existing_string[0]->string)
                    && $existing_string[0]->tstring != $value
                    && substr_count($existing_string[0]->string, "%") == substr_count($value, "%") // ensure all variables are in the translation
                    && $value != $existing_string[0]->tstring
                ) {
                    /* insert the new translation into the temp db */
                    $parameters = array(
                        ":file" => $file,
                        ":type" => $type,
                        ":id" => $id,
                        ":user_id" => $this->getUserID(),
                        ":string" => $value,
                        ":current_time" => time()
                    );
                    $query = $this->db->prepare($insert_pending_sql);
                    $query->execute($parameters);
                } else {
                }
            }

            /* user is an admin, auto accept the translation(s) he just submitted
            note: we insert into the pending database even when we're dealing an admin just to keep a record
            of what this admin is doing */
            if($this->userIsAdmin()) {
                $this->acceptTranslationsForUser($this->getUserID());
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getUsers()
    {
        $sql = "SELECT user_id, user_name, hasAccess, isAdmin FROM users";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getPendingLanguageStrings()
    {
        $pending_strings = array();
        $sql = "
          SELECT
            t1.file,
            t1.id,
            t1.type,
            t1.user_id,
            t3.user_name,
            t1.string as new_translation,
            t2.en as old_translation,
            t2.kor,
            t2.tw,
            t2.hk
          FROM game_strings_temp t1
          JOIN game_strings t2 ON (t1.file = t2.file AND t1.id = t2.id AND t1.type = t2.type)
          JOIN users t3 ON (t1.user_id = t3.user_id)
          WHERE t1.status = 0
        ";

        $query = $this->db->prepare($sql);
        $query->execute();
        $query_data = $query->fetchAll();

        { /* merge pending translations for the same strings together */

            foreach($query_data as $row) {
                if(!isset($pending_strings[$row->file][$row->type][$row->id])) {
                    $pending_strings[$row->file][$row->type][$row->id] = array(
                        "kor" => $row->kor,
                        "tw" => $row->tw,
                        "hk" => $row->hk,
                        "old_translation" => $row->old_translation,
                        "new_translations" => array()
                    );
                }

                $pending_strings[$row->file][$row->type][$row->id]["new_translations"][] = array(
                    "user_id" => $row->user_id,
                    "user_name" => $row->user_name,
                    "translation" => $row->new_translation
                );
            }
        }

        return $pending_strings;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getPendingUsers()
    {
        $sql = "
          SELECT
            t1.user_id,
            t2.user_name,
            count(*) as count
          FROM game_strings_temp t1
          JOIN users t2 ON (t1.user_id = t2.user_id)
          WHERE t1.status = 0
          GROUP BY t1.user_id
          ORDER BY t1.user_id
        ";

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     * @param $status
     */
    public function setAccessStatus($userid, $status)
    {
        if(is_numeric($userid) && $userid != HEAD_ADMIN) {
            $sql = "UPDATE users SET hasAccess = :status WHERE user_id = :userid";
            $parameters = array(":status" => $status, ":userid" => $userid);
            $query = $this->db->prepare($sql);
            $query->execute($parameters);
        }
    }


    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     * @param $status
     */
    public function setAdminStatus($userid, $status)
    {
        if(is_numeric($userid) && $userid != HEAD_ADMIN) {
            $sql = "UPDATE users SET isAdmin = :status WHERE user_id = :userid";
            $parameters = array(":status" => $status, ":userid" => $userid);
            $query = $this->db->prepare($sql);
            $query->execute($parameters);
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function acceptTranslationsForUser($userid)
    {
        /* keep a record of the strings we're going to be replacing */
        $sql = "
          UPDATE game_strings_temp t1
          JOIN game_strings t2 ON (t1.file = t2.file AND t1.id = t2.id AND t1.type = t2.type)
          SET t1.action_replaced = t2.en
          WHERE (
            t1.user_id = :userid AND
            t1.status = 0
          )
        ";

        $parameters = array(":userid" => $userid);
        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        /* update the strings in the database */
        $sql = "
          UPDATE game_strings t1
          JOIN game_strings_temp t2 ON (t1.file = t2.file AND t1.id = t2.id AND t1.type = t2.type)
          SET t1.en = t2.string
          WHERE (
            t2.user_id = :userid AND
            t2.status = 0
          )
        ";

        $parameters = array(":userid" => $userid);
        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        /* mark the user's submitted strings as being accepted */
        $sql = "
          UPDATE game_strings_temp
          SET status = 1,
            action_admin = :current_userid,
            action_date = :current_time
          WHERE (
            status = 0 AND
            user_id = :userid
          )
        ";
        $parameters = array(":userid" => $userid, ":current_userid" => $this->getUserID(),":current_time" => time());
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function denyTranslationsForUser($userid)
    {
        $sql = "
          UPDATE game_strings_temp
          SET status = 2,
            action_admin = :current_userid,
            action_date = :current_time
          WHERE (
            status = 0 AND
            user_id = :userid
          )
        ";
        $parameters = array(":userid" => $userid, ":current_userid" => $this->getUserID(), ":current_time" => time());
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function handleTranslation()
    {
        /* sanitize inputs */
        $file   = isset($_POST["file"]) ? $_POST["file"] : null;
        $type   = isset($_POST["type"]) ? $_POST["type"] : null;
        $id     = isset($_POST["id"]) ? $_POST["id"] : null;
        $userid = isset($_POST["user_id"]) ? $_POST["user_id"] : null;
        $string = isset($_POST["string"]) ? $_POST["string"] : null;

        if(is_numeric($file) && is_numeric($type) && isset($id) && is_numeric($userid)
            && (!empty($string) || $userid == -1)
        ) {
            /* replace underscores with dots in filenames */
            if($file == 4) {
                $id = str_replace("_", ".", $id);
            }

            /* deny all strings for this translation */
            if($userid == -1) {
                $this->denyAllForID($file, $type, $id);
            }

            /* accept a string from 1 user, and deny for all others */
            else {
                /* keep a record of the string we're going to be replacing and mark this user's string as being accepted
                if we changed the string, update it to what we changed it to */
                $sql = "
                  UPDATE game_strings_temp t1
                  JOIN game_strings t2 ON (t1.file = t2.file AND t1.id = t2.id AND t1.type = t2.type)
                  SET t1.action_admin = :current_userid,
                    t1.action_date = :current_time,
                    t1.action_replaced = t2.en,
                    t1.status = 1,
                    t1.string = :string
                  WHERE (
                    t1.file = :file AND
                    t1.type = :type AND
                    t1.id = :id AND
                    t1.user_id = :userid AND
                    t1.status = 0
                  )
                ";

                $parameters = array(
                    ":string" => $string,
                    ":file" => $file,
                    ":type" => $type,
                    ":id" => $id,
                    ":userid" => $userid,
                    ":current_userid" => $this->getUserID(),
                    ":current_time" => time()
                );
                $query = $this->db->prepare($sql);
                $query->execute($parameters);

                /* update the strings in the database */
                $sql = "
                  UPDATE game_strings
                  SET en = :string
                  WHERE (
                    file = :file AND
                    type = :type AND
                    id = :id
                  )
                ";

                $parameters = array(":string" => $string, ":file" => $file, ":type" => $type, ":id" => $id);
                $query = $this->db->prepare($sql);
                $query->execute($parameters);

                /* deny the rest of the submissions (if there are any) */
                $this->denyAllForID($file, $type, $id);
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $file
     * @param $type
     * @param $id
     */
    public function denyAllForID($file, $type, $id)
    {
        $sql = "
          UPDATE game_strings_temp
          SET status = 2,
            action_admin = :current_userid,
            action_date = :current_time
          WHERE (
            status = 0 AND
            file = :file AND
            type = :type AND
            id = :id
          )
        ";
        $parameters = array(
            ":file" => $file,
            ":type" => $type,
            ":id" => $id,
            ":current_userid" => $this->getUserID(),
            ":current_time" => time()
        );
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function getAllTranslations()
    {
        $sql = "
          SELECT
            file,
            type,
            id,
            en AS string
          FROM game_strings
          WHERE en IS NOT NULL
          ORDER BY file, type, id
        ";

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getAllPersonalTranslations()
    {
        $sql = "
          SELECT
            t1.file,
            t1.type,
            t1.id,
            COALESCE(t2.string, t1.en) AS string
          FROM game_strings t1
          LEFT JOIN game_strings_temp t2 ON (t1.file = t2.file AND t1.type = t2.type AND t1.id = t2.id AND t2.user_id = :userid AND t2.status = 0)
          WHERE (t1.en IS NOT NULL OR t2.string IS NOT NULL)
          ORDER BY file, type, id
        ";

        $parameters = array(":userid" => $this->getUserID());
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
        return $query->fetchAll();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $translations
     */
    public function generateTranslationsFile($translations)
    {
        $current_file = null;
        $newline = "\r\n";
        $output = "";

        foreach($translations as $translation) {
            /* each translation has the following fields:
                file,
                type,
                id,
                string */
            if($translation->file != $current_file || $translation->file == 4) {
                $current_file = $translation->file;

                if($current_file == 4) {
                    /* strings with file = 4 are for files which only contain a single string, ex: html help files
                    in these cases, the id acts as the filename */
                    $output .= "{$translation->id}{$newline}";
                } else {
                    $output .= "{$this->_file_name_map[$translation->file]}{$newline}";
                }
            }

            /* print each translation to the output file */
            if($current_file == 0 || $current_file == 1) {
                /* .dat files */
                $output .= "{$translation->id}=\"{$translation->string}\"{$newline}";
            }

            else if($current_file == 4) {
                /* .html help files*/
                $output .= "{$translation->string}{$newline}";
            }

            else {
                /* .edf files */
                $output .= "{$translation->type}:{$translation->id}:{$translation->string}{$newline}";
            }
        }

        header('Content-type: text/plain');
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=\"translations.txt\"");

        /* translation.txt files are in UTC-2 Little-endian format, these files have a BOM of U+FFFE */
        echo "\xFF\xFE" . mb_convert_encoding($output, "UCS-2LE", "UTF-8");
    }
}
