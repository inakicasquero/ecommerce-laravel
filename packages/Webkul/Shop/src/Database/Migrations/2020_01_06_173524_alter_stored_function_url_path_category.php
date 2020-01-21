<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AlterStoredFunctionUrlPathCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $functionSQL = <<< SQL
            DROP FUNCTION IF EXISTS `get_url_path_of_category`;
            CREATE FUNCTION get_url_path_of_category(
                categoryId INT,
                localeCode VARCHAR(255)
            )
            RETURNS VARCHAR(255)
            DETERMINISTIC
            BEGIN
            
                DECLARE urlPath VARCHAR(255);
                
                IF NOT EXISTS (
                    SELECT id 
                    FROM categories
                    WHERE
                        id = categoryId
                        AND parent_id IS NULL
                )
                THEN
                    SELECT
                        GROUP_CONCAT(parent_translations.slug SEPARATOR '/') INTO urlPath
                    FROM
                        categories AS node,
                        categories AS parent
                        JOIN category_translations AS parent_translations ON parent.id = parent_translations.category_id
                    WHERE
                        node._lft >= parent._lft
                        AND node._rgt <= parent._rgt
                        AND node.id = categoryId
                        AND node.parent_id IS NOT NULL
                        AND parent.parent_id IS NOT NULL
                        AND parent_translations.locale = localeCode
                    GROUP BY
                        node.id;
                        
                    IF urlPath IS NULL
                    THEN
                        SET urlPath = (SELECT slug FROM category_translations WHERE category_translations.category_id = categoryId);
                    END IF;
                 ELSE
                    SET urlPath = '';
                 END IF;
                 
                 RETURN urlPath;
            END;
SQL;

        DB::unprepared($functionSQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS `get_url_path_of_category`;');
    }
}
