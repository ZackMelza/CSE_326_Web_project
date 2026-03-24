USE cse326_auth;

INSERT INTO users (username, email, password_hash, role) VALUES
  ('zack_admin', 'admin@example.com', '$2y$12$yfy/xs2MkIfc5rAGigx5dOBeqo1di6GN4KR9sjxDhjKn8Oh5M/UjK', 'admin'),
  ('demo_writer', 'writer@example.com', '$2y$12$yfy/xs2MkIfc5rAGigx5dOBeqo1di6GN4KR9sjxDhjKn8Oh5M/UjK', 'member'),
  ('demo_viewer', 'viewer@example.com', '$2y$12$yfy/xs2MkIfc5rAGigx5dOBeqo1di6GN4KR9sjxDhjKn8Oh5M/UjK', 'member')
ON DUPLICATE KEY UPDATE
  username = VALUES(username),
  email = VALUES(email),
  password_hash = VALUES(password_hash),
  role = VALUES(role);

INSERT INTO posts (user_id, title, category, summary) VALUES
  (1, 'Assignment Overview', 'Documentation', 'A short summary of the backend milestones already completed in the project.'),
  (1, 'Authentication Flow', 'Security', 'Register, login, logout, sessions, password hashing, and redirect guards are all active.'),
  (2, 'PDO Migration Notes', 'Backend', 'The project now uses PDO prepared statements for database access as required by the assignment.'),
  (2, 'Search Module', 'Feature', 'The protected list page supports keyword filtering with bookmarkable GET requests.'),
  (3, 'Presentation Prep', 'Planning', 'The project journal in lessons_stuff records the work chronologically for the final presentation.')
ON DUPLICATE KEY UPDATE title = VALUES(title), category = VALUES(category), summary = VALUES(summary);
