TRUNCATE TABLE friends RESTART IDENTITY CASCADE;
TRUNCATE TABLE friend_requests RESTART IDENTITY CASCADE;

INSERT INTO friends (user_1, user_2) VALUES (1, 2);
INSERT INTO friends (user_1, user_2) VALUES (1, 3);
INSERT INTO friends (user_1, user_2) VALUES (1, 4);

INSERT INTO friend_requests (requester_id, requested_id, status_id) VALUES (5, 1, 1);