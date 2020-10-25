CREATE TABLE ads_listing (
    lid         INT(11)          NOT NULL AUTO_INCREMENT,
    cid         INT(11)          NOT NULL DEFAULT '0',
    title       VARCHAR(100)     NOT NULL DEFAULT '',
    expire      CHAR(3)          NOT NULL DEFAULT '',
    type        VARCHAR(100)     NOT NULL DEFAULT '',
    desctext    TEXT             NOT NULL,
    tel         VARCHAR(15)      NOT NULL DEFAULT '',
    price       VARCHAR(100)     NOT NULL DEFAULT '',
    typeprice   VARCHAR(100)     NOT NULL DEFAULT '',
    date        VARCHAR(25)               DEFAULT NULL,
    email       VARCHAR(100)     NOT NULL DEFAULT '',
    submitter   VARCHAR(60)      NOT NULL DEFAULT '',
    usid        VARCHAR(6)       NOT NULL DEFAULT '',
    town        VARCHAR(200)     NOT NULL DEFAULT '',
    country     VARCHAR(200)     NOT NULL DEFAULT '',
    valid       VARCHAR(11)      NOT NULL DEFAULT '',
    photo       VARCHAR(100)     NOT NULL DEFAULT '',
    photo2      VARCHAR(100)     NOT NULL DEFAULT '',
    photo3      VARCHAR(100)     NOT NULL DEFAULT '',
    view        VARCHAR(10)      NOT NULL DEFAULT '0',
    item_rating DOUBLE(6, 4)     NOT NULL DEFAULT '0.0000',
    item_votes  INT(11) UNSIGNED NOT NULL DEFAULT '0',
    user_rating DOUBLE(6, 4)     NOT NULL DEFAULT '0.0000',
    user_votes  INT(11) UNSIGNED NOT NULL DEFAULT '0',
    comments    INT(11) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (lid)
)
    ENGINE = ISAM;

CREATE TABLE ads_categories (
    cid      INT(11)         NOT NULL AUTO_INCREMENT,
    pid      INT(5) UNSIGNED NOT NULL DEFAULT '0',
    title    VARCHAR(50)     NOT NULL DEFAULT '',
    img      VARCHAR(150)    NOT NULL DEFAULT '',
    ordre    INT(5)          NOT NULL DEFAULT '0',
    affprice INT(5)          NOT NULL DEFAULT '0',
    PRIMARY KEY (cid)
)
    ENGINE = ISAM;

CREATE TABLE ads_type (
    id_type  INT(11)      NOT NULL AUTO_INCREMENT,
    nom_type VARCHAR(150) NOT NULL DEFAULT '',
    PRIMARY KEY (id_type)
)
    ENGINE = ISAM;


INSERT INTO ads_type
VALUES (1, 'for sale');
INSERT INTO ads_type
VALUES (2, 'for exchange');
INSERT INTO ads_type
VALUES (3, 'search');
INSERT INTO ads_type
VALUES (4, 'for lend');


CREATE TABLE ads_price (
    id_price  INT(11)      NOT NULL AUTO_INCREMENT,
    nom_price VARCHAR(150) NOT NULL DEFAULT '',
    PRIMARY KEY (id_price)
)
    ENGINE = ISAM;


INSERT INTO ads_price
VALUES (1, 'exact price');
INSERT INTO ads_price
VALUES (2, 'a day');
INSERT INTO ads_price
VALUES (3, 'a week');
INSERT INTO ads_price
VALUES (4, 'a quarter');
INSERT INTO ads_price
VALUES (5, 'a month');
INSERT INTO ads_price
VALUES (6, 'a year');
INSERT INTO ads_price
VALUES (7, 'to discuss');
INSERT INTO ads_price
VALUES (8, 'maximum');
INSERT INTO ads_price
VALUES (9, 'minimum');

CREATE TABLE `ads_ip_log` (
    ip_id     INT(11)      NOT NULL AUTO_INCREMENT,
    lid       INT(11)      NOT NULL DEFAULT '0',
    date      VARCHAR(25)           DEFAULT NULL,
    submitter VARCHAR(60)  NOT NULL DEFAULT '',
    ipnumber  VARCHAR(150) NOT NULL DEFAULT '',
    email     VARCHAR(100) NOT NULL DEFAULT '',
    PRIMARY KEY (`ip_id`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 1;

#
# Table structure for table `ads_votedata`
#

CREATE TABLE ads_item_votedata (
    ratingid        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    lid             INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    ratinguser      INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    rating          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    ratinghostname  VARCHAR(60)         NOT NULL DEFAULT '',
    ratingtimestamp INT(10)             NOT NULL DEFAULT '0',
    PRIMARY KEY (ratingid),
    KEY ratinguser (ratinguser),
    KEY ratinghostname (ratinghostname)
)
    ENGINE = ISAM;

#
# Table structure for table `ads_votedata`
#

CREATE TABLE ads_user_votedata (
    ratingid        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    usid            INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    ratinguser      INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    rating          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    ratinghostname  VARCHAR(60)         NOT NULL DEFAULT '',
    ratingtimestamp INT(10)             NOT NULL DEFAULT '0',
    PRIMARY KEY (ratingid),
    KEY ratinguser (ratinguser),
    KEY ratinghostname (ratinghostname)
)
    ENGINE = ISAM;


