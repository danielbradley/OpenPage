#
#	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
#	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
#

CREATE TABLE uids (

uid                             INT(11)  NOT NULL AUTO_INCREMENT,
type                        VARCHAR(20)  NOT NULL DEFAULT '',

PRIMARY KEY (uid)
);

CREATE TABLE users (

username                    VARCHAR(99)  NOT NULL,
uid                             INT(11)  NOT NULL,
last_login                 DATETIME      NOT NULL,

user_salt                       INT(11)  NOT NULL,
user_hash                   VARCHAR(8)   NOT NULL,
password_hash               VARCHAR(16)  NOT NULL,
user_status                 VARCHAR(10)  NOT NULL,

first_name                  VARCHAR(50)  NOT NULL,
last_name                   VARCHAR(50)  NOT NULL,

PRIMARY KEY (username), UNIQUE KEY (uid)
);



CREATE TABLE sessions (

id                          VARCHAR(32)  NOT NULL,
username                    VARCHAR(99)  NOT NULL,
created                     TIMESTAMP    NOT NULL,
updated                     TIMESTAMP    NOT NULL,
expiry                          INT(64)  NOT NULL,

PRIMARY KEY (id)
);



CREATE TABLE activations (

uid                             INT(11)  NOT NULL,
timestamp                 TIMESTAMP      NOT NULL,
type                        VARCHAR(30)  NOT NULL,
token                       VARCHAR(64)  NOT NULL,

PRIMARY KEY (uid,type)
);



CREATE FUNCTION uid_create( $Type VARCHAR(20) )
RETURNS INT(11)
BEGIN

INSERT INTO uids (type) VALUES ( $Type );

return LAST_INSERT_ID();

END;



CREATE FUNCTION User_Exists( $Username CHAR(99) )
RETURNS BOOLEAN
BEGIN

return Exists( SELECT username FROM users WHERE username=$Username );

END;



CREATE FUNCTION user_create( $Username CHAR(99), $Password CHAR(99), $First_name CHAR(50), $Last_name CHAR(50), $Type VARCHAR(20) )
RETURNS INT(11)
BEGIN

DECLARE _id      INT(11) DEFAULT 0;
DECLARE _salt    INT(11) DEFAULT 0;
DECLARE _uhash  CHAR(8)  DEFAULT '';
DECLARE _phash  CHAR(16) DEFAULT '';

IF ! User_Exists( $Username ) THEN
	SET _id     = uid_create( $Type );
	SET _salt   = RAND() * 1000;
	SET _uhash  = MD5( concat($Username,_salt) );
	SET _phash  = MD5( concat($Password,_salt) );

	INSERT INTO users VALUES ( $Username, _id, 0, _salt, _uhash, _phash, "INACTIVE", $First_name, $Last_name );
END IF;

return _id;

END;



CREATE FUNCTION Session_Terminate( $Sid CHAR(32) )
RETURNS BOOLEAN
BEGIN

DECLARE _ret    BOOLEAN DEFAULT False;

IF EXISTS( SELECT id FROM sessions WHERE id=$Sid ) THEN
	DELETE FROM sessions WHERE id=$Sid;
	SET _ret = True;
END IF;

return _ret;

END;




CREATE FUNCTION Session_Verify( $Sid CHAR(32) )
RETURNS BOOLEAN
BEGIN

DECLARE _ret       BOOLEAN   DEFAULT  False;
DECLARE _time       INT(64)  DEFAULT  0;
DECLARE _expiry     INT(64)  DEFAULT  0;

SET     _time = UNIX_TIMESTAMP();

SELECT  expiry   INTO _expiry   FROM sessions WHERE id=$Sid;

IF _time < _expiry THEN
	SET _expiry = _expiry + 1000;
	UPDATE sessions SET expiry=_expiry WHERE id=$Sid;
	SET _ret = True;
ELSE
	SET _ret = session_terminate($Sid);
	SET _ret = False;
END IF;

return _ret;

END;
