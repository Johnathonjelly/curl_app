USE curl_rss;

DELIMITER //
DROP PROCEDURE IF EXISTS sp_addUser;

CREATE PROCEDURE sp_addUser(
  IN email VARCHAR(75),
  IN password VARCHAR(230)
)BEGIN

INSERT IGNORE INTO users(email, pwd) VALUES
(
  email,
  password
);
SELECT LAST_INSERT_ID() AS userID;
END;

DROP PROCEDURE IF EXISTS sp_login;

CREATE PROCEDURE sp_login(
  IN email VARCHAR(75),
  IN password VARCHAR(230)
)BEGIN
SELECT * FROM users WHERE email = email AND password = pwd;
END;


DROP PROCEDURE IF EXISTS sp_addRSS;

CREATE PROCEDURE sp_addRSS
(
  IN userID INT(10),
  IN friendlyName VARCHAR(50),
  IN url VARCHAR(150)
)
BEGIN
INSERT IGNORE INTO rss(userID, friendly, rss_url)
VALUES
(
  userID,
  friendlyName,
  url
);
END;
//
DELIMITER ;
