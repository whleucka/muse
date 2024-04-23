INSERT INTO modules (title, path, max_permission_level, parent_module_id) VALUES
("Administration", NULL, NULL, NULL),
("Account", NULL, NULL, NULL),
("Users", "users", 1, 1),
("Modules", "modules", 0, 1),
("Sessions", "sessions", 1, 1),
("Profile", "profile", 2, 2)
