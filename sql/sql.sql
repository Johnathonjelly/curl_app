USE curl_rss;

CREATE TABLE users
(
  userID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(75),
  pwd BINARY
);

CREATE TABLE rss
(
  rssID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  userID INT UNSIGNED NOT NULL,
  friendly VARCHAR(50),
  rss_url VARCHAR(150)
);