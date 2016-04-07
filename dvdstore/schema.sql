CREATE TABLE actors (
  actor_id int(11) NOT NULL auto_increment,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  PRIMARY KEY  (actor_id),
  UNIQUE KEY actor_id (actor_id)
) TYPE=MyISAM;

CREATE TABLE contains (
  order_number int(11) NOT NULL,
  barcode bigint(20) NOT NULL,
  quantity mediumint(9) NOT NULL default '1',
  PRIMARY KEY  (order_number,barcode)
) TYPE=MyISAM;

CREATE TABLE credit_cards (
  card_number bigint(16) NOT NULL,
  expiry_date timestamp(4) NOT NULL,
  card_name varchar(50) NOT NULL,
  PRIMARY KEY  (card_number),
  UNIQUE KEY card_number (card_number)
) TYPE=MyISAM;

CREATE TABLE customers (
  username varchar(20) NOT NULL,
  password varchar(32) NOT NULL,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  date_of_birth date NOT NULL default '0000-00-00',
  email varchar(80) NOT NULL,
  street varchar(30) NOT NULL,
  suburb varchar(30) NOT NULL,
  postcode varchar(4) NOT NULL,
  state_id smallint(5) NOT NULL,
  PRIMARY KEY  (username),
  UNIQUE KEY username (username)
) TYPE=MyISAM;

CREATE TABLE directors (
  director_id int(11) NOT NULL auto_increment,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  PRIMARY KEY  (director_id),
  UNIQUE KEY director_id (director_id)
) TYPE=MyISAM;

CREATE TABLE dvd (
  barcode bigint(20) NOT NULL,
  title varchar(60) NOT NULL,
  synopsis text NOT NULL,
  active tinyint(1) NOT NULL,
  stock_avail int(11) NOT NULL,
  director_id int(11) default NULL,
  sell_price decimal(4,2) NOT NULL default '0.00',
  cost decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (barcode),
  UNIQUE KEY barcode (barcode),
  KEY title (title)
) TYPE=MyISAM;

CREATE TABLE genres (
  genre_id int(11) NOT NULL auto_increment,
  description varchar(30) NOT NULL,
  PRIMARY KEY  (genre_id),
  UNIQUE KEY genre_id (genre_id)
) TYPE=MyISAM;

CREATE TABLE has_stars_of (
  barcode bigint(20) NOT NULL,
  actor_id int(11) NOT NULL,
  PRIMARY KEY  (barcode,actor_id)
) TYPE=MyISAM;

CREATE TABLE is_type_of (
  barcode bigint(20) NOT NULL,
  genre_id int(11) NOT NULL,
  PRIMARY KEY  (barcode,genre_id)
) TYPE=MyISAM;

CREATE TABLE orders (
  order_number int(11) NOT NULL auto_increment,
  username varchar(20) NOT NULL,
  ship_datetime datetime NOT NULL,
  PRIMARY KEY  (order_number),
  UNIQUE KEY order_number (order_number)
) TYPE=MyISAM;

CREATE TABLE pays_with (
  card_number bigint(16) NOT NULL,
  username varchar(20) NOT NULL,
  PRIMARY KEY  (card_number,username)
) TYPE=MyISAM;

CREATE TABLE shopping_trolley (
  username varchar(20) NOT NULL,
  barcode bigint(20) NOT NULL,
  quantity mediumint(9) NOT NULL default '1',
  PRIMARY KEY  (username,barcode)
) TYPE=MyISAM;

CREATE TABLE states (
  state_id smallint(5) unsigned NOT NULL auto_increment,
  description varchar(30) NOT NULL,
  PRIMARY KEY  (state_id)
) TYPE=MyISAM;

    