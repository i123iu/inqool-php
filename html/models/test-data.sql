-- Courts

INSERT INTO courts (name, surface_id) VALUES ('Kurt 1', 1);
INSERT INTO courts (name, surface_id) VALUES ('Kurt 2', 1);
INSERT INTO courts (name, surface_id) VALUES ('Kurt 3', 2);
INSERT INTO courts (name, surface_id) VALUES ('Kurt 4', 2);
INSERT INTO courts (name, surface_id) VALUES ('Kurt 5', 1);

-- Surfaces

INSERT INTO surfaces (name, price) VALUES ('Povrch A', 10);
INSERT INTO surfaces (name, price) VALUES ('Povrch B', 20);

-- Reservations

INSERT INTO reservations (court_id, phone_number, double_game, start_time, end_time) VALUES (1, '111111111', true, '2024-01-01 12:30', '2024-01-01 13:30');
INSERT INTO reservations (court_id, phone_number, double_game, start_time, end_time) VALUES (1, '222222222', false, '2024-01-01 13:30', '2024-01-01 14:00');
INSERT INTO reservations (court_id, phone_number, double_game, start_time, end_time) VALUES (1, '333333333', true, '2024-01-01 15:00', '2024-01-01 17:00');
INSERT INTO reservations (court_id, phone_number, double_game, start_time, end_time) VALUES (3, '111111111', false, '2024-01-01 18:00', '2024-01-01 19:30');