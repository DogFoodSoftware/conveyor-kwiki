-- TODO: change to useraccount
CREATE TABLE documentation.comment (
	id			INT,
	target_url     		VARCHAR(256) NOT NULL,
	comment_text   		VARCHAR(1024) NOT NULL,
	commenter		INT,
	direct_byline		VARCHAR(128),
	direct_email		VARCHAR(128),
	PRIMARY KEY (id),
	FOREIGN KEY (id) REFERENCES kibbles.entity (id)
);
