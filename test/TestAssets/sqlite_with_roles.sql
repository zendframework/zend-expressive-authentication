CREATE TABLE user(
    username TEXT,
    password TEXT
);

CREATE TABLE role(
    role TEXT
);

CREATE TABLE user_role(
    username TEXT,
    role TEXT
);

INSERT INTO user (username, password) VALUES
('test', '$2y$10$C822kPutHb8S/An9pBzJHeaN2/uqytA88O5VtTaY9m9EzWCJPDF7e');

INSERT INTO role (role) VALUES
('user'),
('admin');

INSERT INTO user_role (username, role) VALUES
('test', 'user'),
('test', 'admin');
