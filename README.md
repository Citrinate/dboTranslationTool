# Dragon Ball Online Translation Tool

This tool is meant to organize the collaborative efforts of the localization of Dragon Ball Online.

## Install Guide

1. Open the config file in `dbo_translator/config/config.php` and fill in the MySQL username and password variables
2. The contents of the zip file should then be uploaded above your website's root folder, so that the `dbo_translator` folder isn't publicly accessible
3. Execute the sql files, from the "sql" folder, in order
4. Navigate to `your-domain.com/dbo_translator/` and create an account, this account will become the "Head Admin" account.

## User states

All users fall into one of the four categories:

**Unauthorized user**: All users who create an account will recieve a prompt telling them that they cannot submit translations until their accounts are approved by an admin user.

**Authorized user**: This user can submit translations, and download `translation.txt` files

**Admin user**: This user can authorize users to submit translations, and can accept or deny submitted translations.  An admin can also remove authorization from an authorized user.  If an admin user attempts to submit a translation themselves, that translation will be automatically accepted.

**Head Admin**: The first user to have created an account (user with `user_id` = 1, you can change this in the config file if needed).  This user has all of the same powers as an admin, but also has the ability to promote and demote new Admins.

## Database table info

`game_strings` table breakdown:

* `file`: Breaks down the strings first by the file they're contained in.  The filenames for these are defined in the model.php file, and are defaulted to:
  * 0 = `.\language\local_data.dat`
  * 1 = `.\language\local_sync_data.dat`
  * 2 = `.\data\o_table_text_all_data.edf`
  * 3 = `.\data\o_table_quest_text_data.edf`
  * 4 = No single file, some files only contain a single string, such all of the `.html` help files.  In these cases, I've decided to use their `id` field as the filename.
* `type`: The `text_all_data.edf` file is broken down into 28 different types of strings, numbered from 0 to 27.  The `table_quest_text_data.edf` file is also in the same format, but only uses a single type: 0.  Other than these two files, the `type` field is not used and is defaulted to 0.
* `id`: Simply, the ID for the string.  As stated earlier, when `file` is equal to 4, then `id` represents the filename for the string.

---

`game_strings_temp` table breakdown:

`file`, `type`, and `id` are the same as in `game_strings`

* `status`: Represents the current state of the submission and may have one of the following values:
  * 0 = The string is still pending; no admin has acted on it yet.
  * 1 = The string was accepted by an admin user and is now in the `game_strings` database.  The string that used to be in the `game_strings` database is now in this row's `action_replace` column.
  * 2 = The string was not accepted by an admin user.

## Other Details

The `game_strings` data provided here is just to show that the tool works.  The data it contains from each of the 3 versions of DBO is incomplete, and the translations are from the final version of the DBOCOM English Patch (plus some testing I did). Before you really start using it, you're going to want to delete the contents of this table, extract the untranslated strings from each of the 3 versions, convert them into SQL queries, and import them into the database.  There's no tool that handles this automatically.

The option to download a `translations.txt` file is there as an example.  The format it exports is a human-readable format used by the DBOCOM English Patch.