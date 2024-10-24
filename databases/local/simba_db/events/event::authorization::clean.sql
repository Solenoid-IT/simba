CREATE EVENT `%( DB_NAME )%`.`event::authorization::clean`
ON SCHEDULE EVERY 1 HOUR
STARTS '2023-12-12 00:00:00'
DO
    DELETE
    FROM
        `%( DB_NAME )%`.`authorization`
    WHERE
        CURRENT_TIMESTAMP >= `datetime.expiration`
;