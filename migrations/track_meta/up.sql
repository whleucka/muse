CREATE TABLE IF NOT EXISTS track_meta (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	track_id INT UNSIGNED NOT NULL,
	cover VARCHAR(255),
	filesize INT UNSIGNED,
	bitrate INT UNSIGNED,
	mime_type VARCHAR(255),
	playtime_string VARCHAR(255),
	playtime_seconds INT UNSIGNED,
	track_number INT UNSIGNED,
	title MEDIUMTEXT,
	artist MEDIUMTEXT,
	album MEDIUMTEXT,
	genre MEDIUMTEXT,
	year INT UNSIGNED,
	updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	FOREIGN KEY(track_id) REFERENCES tracks(id) ON DELETE CASCADE
);
