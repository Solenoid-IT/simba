CREATE OR REPLACE VIEW `view::document_tag::all` AS
SELECT
    document_tag_table.`tenant`,
    document_tag_table.`document`,

    tag_table.`id` AS `tag.id`,
    tag_table.`name` AS `tag.name`,
    tag_table.`value` AS `tag.value`,
    tag_table.`color` AS `tag.color`
FROM
    `document_tag` document_tag_table
        INNER JOIN
    `tag` tag_table
        ON
    (
        document_tag_table.`tag` = tag_table.`id`
    )
;