#
# Tabellenstruktur für Tabelle `tutorials`
#

CREATE TABLE tutorials (
    tid        INT(11)          NOT NULL AUTO_INCREMENT,
    cid        INT(11)          NOT NULL DEFAULT '0',
    gid        INT(11)          NOT NULL DEFAULT '0',
    tname      TEXT             NOT NULL,
    tdesc      TEXT             NOT NULL,
    timg       VARCHAR(255)     NOT NULL DEFAULT '',
    tcont      TEXT             NOT NULL,
    tlink      VARCHAR(255)     NOT NULL DEFAULT '',
    tauthor    TEXT             NOT NULL,
    status     TINYINT(1)       NOT NULL DEFAULT '0',
    codes      TINYINT(1)       NOT NULL DEFAULT '0',
    hits       INT(11)          NOT NULL DEFAULT '0',
    rating     DOUBLE(6, 4)     NOT NULL DEFAULT '0.0000',
    votes      INT(5) UNSIGNED  NOT NULL DEFAULT '0',
    date       INT(10)          NOT NULL DEFAULT '0',
    submitter  INT(11) UNSIGNED NOT NULL DEFAULT '0',
    dir        INT(10)          NOT NULL DEFAULT '0',
    timgwidth  INT(6)           NOT NULL DEFAULT '0',
    timgheight INT(6)           NOT NULL DEFAULT '0',
    PRIMARY KEY (tid),
    KEY cid (cid),
    KEY rating (rating),
    KEY status (status),
    KEY gid (gid),
    KEY tlink (tlink)
)
    ENGINE = ISAM;



#
# Tabellenstruktur für Tabelle `tutorials_categorys`
#

CREATE TABLE tutorials_categorys (
    cid        INT(11)      NOT NULL AUTO_INCREMENT,
    scid       INT(11)      NOT NULL DEFAULT '0',
    cname      VARCHAR(40)  NOT NULL DEFAULT '',
    cdesc      TEXT         NOT NULL,
    cimg       VARCHAR(255) NOT NULL DEFAULT '',
    cimgwidth  INT(6)       NOT NULL DEFAULT '0',
    cimgheight INT(6)       NOT NULL DEFAULT '0',
    PRIMARY KEY (cid),
    KEY scid (scid),
    KEY cname (cname)
)
    ENGINE = ISAM;



#
# Tabellenstruktur für Tabelle `tutorials_groups`
#

CREATE TABLE tutorials_groups (
    gid   INT(11)     NOT NULL AUTO_INCREMENT,
    cid   INT(11)     NOT NULL DEFAULT '0',
    pos   INT(11)     NOT NULL DEFAULT '0',
    gname VARCHAR(40) NOT NULL DEFAULT '',
    PRIMARY KEY (gid),
    KEY cid (cid),
    KEY pos (pos)
)
    ENGINE = ISAM;


#
# Tabellenstruktur für Tabelle `tutorials_votedata`
#

CREATE TABLE tutorials_votedata (
    ratingid        INT(15) UNSIGNED    NOT NULL AUTO_INCREMENT,
    tid             INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    ratinguser      INT(5)              NOT NULL DEFAULT '0',
    rating          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    ratinghostname  VARCHAR(60)         NOT NULL DEFAULT '',
    ratingtimestamp INT(10)             NOT NULL DEFAULT '0',
    PRIMARY KEY (ratingid),
    KEY ratinguser (ratinguser),
    KEY ratinghostname (ratinghostname),
    KEY ratingtimestamp (ratingtimestamp),
    KEY tid (tid)
)
    ENGINE = ISAM;
