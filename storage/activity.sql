START TRANSACTION;
TRUNCATE TABLE activity_log;

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'User Login', 'User Login successfully', 'App\\Models\\User', NULL, 11, 'App\\Models\\User', 11, '{\"ip\": \"127.0.0.1\", \"target\": \"test\", \"activity\": \"User Login successfully\"}', NULL, '2023-10-30 09:37:38', '2023-10-30 09:37:38'),
(2, 'Role created', 'Test User created Role Writer.', 'Spatie\\Permission\\Models\\Role', NULL, 2, 'App\\Models\\User', 11, '{\"ip\": \"127.0.0.1\", \"target\": \"Writer\", \"activity\": \"Role created successfully\"}', NULL, '2023-10-30 09:38:21', '2023-10-30 09:38:21');
COMMIT;
