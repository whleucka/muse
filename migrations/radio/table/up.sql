CREATE TABLE radio (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
	uuid VARCHAR(36) DEFAULT (UUID()),
  station_name varchar(255) NOT NULL,
  location varchar(255),
  src_url varchar(255) NOT NULL,
  cover_url varchar(255),
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
)
