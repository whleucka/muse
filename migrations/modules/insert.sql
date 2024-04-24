INSERT INTO modules (title, path, item_order, max_permission_level, parent_module_id) VALUES
("Administration", NULL, 0, NULL, NULL),
("Account", NULL, 1, NULL, NULL),
("Users", "users", 0, 1, 1),
("Type", "user-type", 0, 0, 3),
("Modules", "modules", 1, 0, 1),
("Sessions", "sessions", 2, 1, 1),
("Profile", "profile", 0, 2, 2)
