SET default_storage_engine = INNODB;

CREATE DATABASE `%( DB_NAME )%`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci
;

USE `%( DB_NAME )%`;






CREATE TABLE `hierarchy`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `type`                               VARCHAR(255)                                             NOT NULL,
    `color`                              VARCHAR(255)                                                 NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`type`)
)
;

CREATE TABLE `tenant`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `name`                               VARCHAR(255)                                             NOT NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,
    `datetime.update`                    TIMESTAMP                                                    NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`name`)
)
;

CREATE TABLE `user`
(
    `id`                                  BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `tenant`                              BIGINT UNSIGNED                                          NOT NULL,

    `name`                                VARCHAR(255)                                             NOT NULL,

    `email`                               VARCHAR(255)                                             NOT NULL,

    `hierarchy`                           BIGINT UNSIGNED                                          NOT NULL,

    `birth.name`                          VARCHAR(255)                                                 NULL,
    `birth.surname`                       VARCHAR(255)                                                 NULL,

    `security.password`                   VARCHAR(255)                                                 NULL,
    `security.mfa`                        BOOLEAN                                    DEFAULT FALSE NOT NULL,

    `security.idk.authentication`         BOOLEAN                                    DEFAULT FALSE NOT NULL,
    `security.idk.public_key`             LONGBLOB                                                     NULL,
    `security.idk.signature`              LONGBLOB                                                     NULL,

    `datetime.insert`                     TIMESTAMP                                                NOT NULL,
    `datetime.update`                     TIMESTAMP                                                    NULL,
    `datetime.changelog_read`             TIMESTAMP                                                    NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`tenant`,`name`),
    UNIQUE  KEY (`email`),

    FOREIGN KEY (`tenant`)
    REFERENCES `tenant` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (`hierarchy`)
    REFERENCES `hierarchy` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

CREATE TABLE `session`
(
    `id`                                 VARCHAR(255)                                             NOT NULL,

    `data`                               LONGBLOB                                                 NOT NULL,

    `user`                               BIGINT UNSIGNED                                              NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,
    `datetime.update`                    TIMESTAMP                                                    NULL,
    `datetime.expiration`                TIMESTAMP                                                NOT NULL,



    PRIMARY KEY (`id`),

    FOREIGN KEY (`user`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

CREATE TABLE `activity`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `user`                               BIGINT UNSIGNED                                              NULL,
    `action`                             VARCHAR(255)                                                 NULL,

    `description`                        TEXT                                                         NULL,

    `session`                            VARCHAR(255)                                                 NULL,

    `ip`                                 VARCHAR(255)                                             NOT NULL,
    `user_agent`                         LONGTEXT                                                     NULL,

    `ip_info.country.code`               VARCHAR(255)                                                 NULL,
    `ip_info.country.name`               VARCHAR(255)                                                 NULL,
    `ip_info.isp`                        VARCHAR(255)                                                 NULL,

    `ua_info.browser`                    VARCHAR(255)                                                 NULL,
    `ua_info.os`                         VARCHAR(255)                                                 NULL,
    `ua_info.hw`                         VARCHAR(255)                                                 NULL,

    `resource.action`                    VARCHAR(255)                                                 NULL,
    `resource.type`                      VARCHAR(255)                                                 NULL,
    `resource.id`                        BIGINT UNSIGNED                                              NULL,
    `resource.key`                       VARCHAR(255)                                                 NULL,

    `alert_severity`                     TINYINT UNSIGNED                                             NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,
    `datetime.alert.read`                TIMESTAMP                                                    NULL,


    PRIMARY KEY (`id`),

    FOREIGN KEY (`user`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (`session`)
    REFERENCES `session` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
)
;

CREATE TABLE `authorization`
(
    `token`                              VARCHAR(255)                                             NOT NULL,

    `data`                               LONGBLOB                                                     NULL,

    `callback_url`                       TEXT                                                         NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,
    `datetime.expiration`                TIMESTAMP                                                NOT NULL,



    PRIMARY KEY (`token`)
)
;



CREATE TABLE `document`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `tenant`                             BIGINT UNSIGNED                                          NOT NULL,
    `path`                               VARCHAR(255)                                             NOT NULL,

    `owner`                              BIGINT UNSIGNED                                              NULL,

    `title`                              VARCHAR(255)                                             NOT NULL,
    `description`                        TEXT                                                     NOT NULL,

    `content`                            LONGTEXT                                                 NOT NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,
    `datetime.update`                    TIMESTAMP                                                    NULL,

    `datetime.option.active`             TIMESTAMP                                                    NULL,
    `datetime.option.sitemap`            TIMESTAMP                                                    NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`tenant`,`path`),
    
    FOREIGN KEY (`tenant`)
    REFERENCES `tenant` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    
    FOREIGN KEY (`owner`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
)
;

CREATE TABLE `tag`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `tenant`                             BIGINT UNSIGNED                                          NOT NULL,
    `name`                               VARCHAR(255)                                             NOT NULL,

    `owner`                              BIGINT UNSIGNED                                              NULL,

    `value`                              VARCHAR(255)                                             NOT NULL,
    `color`                              VARCHAR(255)                                             NOT NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,
    `datetime.update`                    TIMESTAMP                                                    NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`tenant`,`name`),
    
    FOREIGN KEY (`tenant`)
    REFERENCES `tenant` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    
    FOREIGN KEY (`owner`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
)
;

CREATE TABLE `document_tag`
(
    `tenant`                              BIGINT UNSIGNED                           NOT NULL,

    `document`                            BIGINT UNSIGNED                           NOT NULL,
    `tag`                                 BIGINT UNSIGNED                           NOT NULL,




    PRIMARY KEY (`document`,`tag`),
    
    FOREIGN KEY (`tenant`)
    REFERENCES `tenant` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    
    FOREIGN KEY (`document`)
    REFERENCES `document` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    
    FOREIGN KEY (`tag`)
    REFERENCES `tag` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;



/*

CREATE TABLE `tag`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `value`                              VARCHAR(255)                                             NOT NULL,
    `color`                              VARCHAR(255)                   DEFAULT '#b1b1b1'         NOT NULL,

    `datetime.insert`                    TIMESTAMP                      DEFAULT CURRENT_TIMESTAMP NOT NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`value`)
)
;

CREATE TABLE `document.tag`
(
    `document`                                 BIGINT UNSIGNED                                          NOT NULL,
    `tag`                                      BIGINT UNSIGNED                                          NOT NULL,



    PRIMARY KEY (`document`,`tag`),
    
    FOREIGN KEY (`document`)
    REFERENCES `document` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (`tag`)
    REFERENCES `tag` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

CREATE TABLE `visitor`
(
    `route`                              TEXT                                                     NOT NULL,

    `document`                           BIGINT UNSIGNED                                          NOT NULL,

    `ip.address`                         VARCHAR(255)                                             NOT NULL,
    `ip.country.code`                    VARCHAR(255)                                             NOT NULL,
    `ip.country.name`                    VARCHAR(255)                                             NOT NULL,
    `ip.isp`                             VARCHAR(255)                                             NOT NULL,

    `user_agent`                         LONGTEXT                                                 NOT NULL,

    `browser`                            VARCHAR(255)                                             NOT NULL,
    `os`                                 VARCHAR(255)                                             NOT NULL,
    `hw`                                 VARCHAR(255)                                             NOT NULL,

    `datetime.insert`                    TIMESTAMP                      DEFAULT CURRENT_TIMESTAMP NOT NULL
)
;

*/