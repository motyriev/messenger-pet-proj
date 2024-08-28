CREATE TABLE IF NOT EXISTS friend_request_statuses
(
    id          SERIAL PRIMARY KEY,
    status_name VARCHAR(30) NOT NULL UNIQUE
);


INSERT INTO friend_request_statuses (status_name)
VALUES ('pending'),
       ('accepted'),
       ('declined')
ON CONFLICT (status_name) DO NOTHING;


CREATE TABLE IF NOT EXISTS friend_requests
(
    id           SERIAL PRIMARY KEY,
    requester_id INT NOT NULL,
    requested_id INT NOT NULL,
    status_id    INT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_friend_request_status
        FOREIGN KEY (status_id)
            REFERENCES friend_request_statuses (id)
);
CREATE INDEX IF NOT EXISTS idx_friend_request_status_id ON friend_requests (status_id);
CREATE OR REPLACE FUNCTION update_updated_at_column()
    RETURNS TRIGGER AS
$$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

DO
$$
    BEGIN
        IF NOT EXISTS (SELECT 1
                       FROM pg_trigger
                       WHERE tgname = 'update_friend_requests_updated_at') THEN
            CREATE TRIGGER update_friend_requests_updated_at
                BEFORE UPDATE
                ON friend_requests
                FOR EACH ROW
            EXECUTE PROCEDURE update_updated_at_column();
        END IF;
    END
$$;


CREATE TABLE IF NOT EXISTS friends
(
    user_1 INT NOT NULL,
    user_2 INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_1, user_2)
);

CREATE OR REPLACE FUNCTION update_created_at_column()
    RETURNS TRIGGER AS
$$
BEGIN
    NEW.created_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

DO
$$
    BEGIN
        IF NOT EXISTS (SELECT 1
                       FROM pg_trigger
                       WHERE tgname = 'update_friends_created_at') THEN
            CREATE TRIGGER update_friends_created_at
                BEFORE UPDATE
                ON friends
                FOR EACH ROW
            EXECUTE PROCEDURE update_created_at_column();
        END IF;
    END
$$;