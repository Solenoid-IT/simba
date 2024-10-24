CREATE OR REPLACE VIEW `view::activity::all` AS
SELECT
    activity_table.*,

    user_table.`tenant` AS `user.tenant`,
    user_table.`name` AS `user.name`
FROM
    `activity` activity_table
        INNER JOIN
    `user` user_table
        ON
    (
        activity_table.`user` = user_table.`id`
    )
;