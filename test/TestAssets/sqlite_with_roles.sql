CREATE TABLE user(
    username TEXT,
    password TEXT,
    email TEXT
);

CREATE TABLE role(
    role TEXT
);

CREATE TABLE user_role(
    username TEXT,
    role TEXT
);

INSERT INTO user (username, password, email) VALUES
('test', '$2y$10$C822kPutHb8S/An9pBzJHeaN2/uqytA88O5VtTaY9m9EzWCJPDF7e', 'test@foo.com');

INSERT INTO role (role) VALUES
('user'),
('admin');

INSERT INTO user_role (username, role) VALUES
('test', 'user'),
('test', 'admin');
