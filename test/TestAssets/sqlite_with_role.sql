CREATE TABLE user(
    username TEXT,
    password TEXT,
    role TEXT
);

INSERT INTO user (username, password, role) VALUES ('test', '$2y$10$C822kPutHb8S/An9pBzJHeaN2/uqytA88O5VtTaY9m9EzWCJPDF7e', 'admin');
