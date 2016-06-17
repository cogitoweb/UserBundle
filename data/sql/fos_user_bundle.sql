-- DROP TABLE IF EXISTS fos_user_group      CASCADE;
-- DROP TABLE IF EXISTS fos_user_user       CASCADE;
-- DROP TABLE IF EXISTS fos_user_user_group CASCADE;
-- DROP TABLE IF EXISTS sessions            CASCADE;

--
-- Groups
--
CREATE TABLE fos_user_group (
	id    serial                 NOT null,
	name  character varying(255) NOT null,
	roles text                   NOT null,

	PRIMARY KEY(id)
);
CREATE UNIQUE INDEX UNIQ_583D1F3E5E237E06 ON fos_user_group (name);
COMMENT ON COLUMN fos_user_group.roles IS '(DC2Type:array)';

--
-- Users
--
CREATE TABLE fos_user_user (
	id                    serial                      NOT null,
	username              character varying(255)      NOT null,
	username_canonical    character varying(255)      NOT null,
	email                 character varying(255)      NOT null,
	email_canonical       character varying(255)      NOT null,
	enabled               boolean                     NOT null,
	salt                  character varying(255)      NOT null,
	password              character varying(255)      NOT null,
	last_login            timestamp without time zone,
	locked                boolean                     NOT null,
	expired               boolean                     NOT null,
	expires_at            timestamp without time zone,
	confirmation_token    character varying(255),
	password_requested_at timestamp without time zone,
	roles                 text                        NOT null,
	credentials_expired   boolean                     NOT null,
	credentials_expire_at timestamp without time zone,
	created_at            timestamp without time zone NOT null,
	updated_at            timestamp without time zone NOT null,
	date_of_birth         timestamp without time zone,
	firstname             character varying(64),
	lastname              character varying(64),
	website               character varying(64),
	biography             character varying(1000),
	gender                character varying(1),
	locale                character varying(8),
	timezone              character varying(64),
	phone                 character varying(64),
	facebook_uid          character varying(255),
	facebook_name         character varying(255),
	facebook_data         text,
	twitter_uid           character varying(255),
	twitter_name          character varying(255),
	twitter_data          text,
	gplus_uid             character varying(255),
	gplus_name            character varying(255),
	gplus_data            text,
	token                 character varying(255),
	two_step_code         character varying(255),

	PRIMARY KEY(id)
);
CREATE UNIQUE INDEX UNIQ_C560D76192FC23A8 ON fos_user_user (username_canonical);
CREATE UNIQUE INDEX UNIQ_C560D761A0D96FBF ON fos_user_user (email_canonical);
COMMENT ON COLUMN fos_user_user.roles         IS '(DC2Type:array)';
COMMENT ON COLUMN fos_user_user.facebook_data IS '(DC2Type:json)';
COMMENT ON COLUMN fos_user_user.twitter_data  IS '(DC2Type:json)';
COMMENT ON COLUMN fos_user_user.gplus_data    IS '(DC2Type:json)';

--
-- Join table
--
CREATE TABLE fos_user_user_group (
	user_id  integer NOT null,
	group_id integer NOT null,

	PRIMARY KEY(user_id, group_id)
);
CREATE INDEX IDX_B3C77447A76ED395 ON fos_user_user_group (user_id);
CREATE INDEX IDX_B3C77447FE54D947 ON fos_user_user_group (group_id);

ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_fos_user_user_group_user_id  FOREIGN KEY (user_id)  REFERENCES fos_user_user  (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_fos_user_user_group_group_id FOREIGN KEY (group_id) REFERENCES fos_user_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;

--
-- Session
--
CREATE TABLE sessions (
	sess_id       character varying(128) NOT null,
	sess_data     bytea                  NOT null,
	sess_time     integer                NOT null,
	sess_lifetime integer                NOT null,

	PRIMARY KEY(sess_id)
);