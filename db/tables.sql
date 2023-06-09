-- Courts

CREATE TABLE IF NOT EXISTS courts
(
    id integer PRIMARY KEY AUTOINCREMENT,
    name character varying(64) NOT NULL,
    surface_id integer NOT NULL REFERENCES surfaces (id)
);

-- Surfaces

CREATE TABLE IF NOT EXISTS surfaces
(
    id integer PRIMARY KEY AUTOINCREMENT,
    name character varying(64) NOT NULL,
    price real NOT NULL CHECK (price > 0)
);

-- Reservations

CREATE TABLE IF NOT EXISTS reservations
(
    id integer PRIMARY KEY AUTOINCREMENT,
    court_id integer NOT NULL REFERENCES courts (id),

    phone_number character varying(32) NOT NULL,
    double_game boolean NOT NULL,

    start_time timestamp without time zone NOT NULL,
    end_time timestamp without time zone NOT NULL,

    CONSTRAINT "end is after start" CHECK (end_time > start_time)
);