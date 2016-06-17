CREATE TABLE sessions (
	sess_id       VARCHAR(128) NOT NULL PRIMARY KEY,
	sess_data     BYTEA        NOT NULL,
	sess_time     INTEGER      NOT NULL,
	sess_lifetime INTEGER      NOT NULL
);