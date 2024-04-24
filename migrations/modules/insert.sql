INSERT INTO modules (title, path, class_name, sql_table, primary_key, item_order, max_permission_level, parent_module_id) VALUES
("Administration", NULL, NULL, NULL, NULL, 0, NULL, NULL),
("Account", NULL, NULL, NULL, NULL, 1, NULL, NULL),
("Users", "users", "\\App\\Modules\\Users", "users", "id", 0, 1, 1),
("User Types", "user-types", "\\App\\Modules\\UserTypes", "user_types", "id", 0, 0, 3),
("Modules", "modules", "\\App\\Modules\\Modules", "modules", "id", 1, 0, 1),
("Sessions", "sessions", "\\App\\Modules\\Sessions", "sessions", "id", 2, 1, 1),
("Profile", "profile", "\\App\\Modules\\Profile", "users", "id", 0, 2, 2)
