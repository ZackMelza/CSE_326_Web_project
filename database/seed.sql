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

INSERT INTO specialties (id, code, name, sector) VALUES
  (1, 'PRI-EDU', 'Primary Education', 'General Education'),
  (2, 'MATH-SEC', 'Secondary Mathematics', 'STEM'),
  (3, 'ICT-SEC', 'Secondary Informatics', 'STEM')
ON DUPLICATE KEY UPDATE
  code = VALUES(code),
  name = VALUES(name),
  sector = VALUES(sector);

INSERT INTO appointment_lists (id, specialty_id, title, publish_year, status) VALUES
  (1, 1, 'Primary Appointment List 2026', 2026, 'published'),
  (2, 2, 'Mathematics Appointment List 2026', 2026, 'published'),
  (3, 3, 'Informatics Appointment List 2026', 2026, 'review')
ON DUPLICATE KEY UPDATE
  specialty_id = VALUES(specialty_id),
  title = VALUES(title),
  publish_year = VALUES(publish_year),
  status = VALUES(status);

INSERT INTO candidates (id, first_name, last_name, email, city, phone) VALUES
  (1, 'Maria', 'Ioannou', 'maria.ioannou@example.com', 'Nicosia', '+35799110011'),
  (2, 'Andreas', 'Georgiou', 'andreas.georgiou@example.com', 'Limassol', '+35799110022'),
  (3, 'Eleni', 'Christou', 'eleni.christou@example.com', 'Larnaca', '+35799110033'),
  (4, 'Petros', 'Savva', 'petros.savva@example.com', 'Paphos', '+35799110044')
ON DUPLICATE KEY UPDATE
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  email = VALUES(email),
  city = VALUES(city),
  phone = VALUES(phone);

INSERT INTO candidate_list_entries (id, candidate_id, list_id, ranking, status, notes) VALUES
  (1, 1, 1, 12, 'active', 'Strong classroom management profile and complete documentation.'),
  (2, 2, 2, 8, 'active', 'Mathematics candidate with strong postgraduate record.'),
  (3, 3, 3, 5, 'review', 'Pending final transcript verification by the admin team.'),
  (4, 4, 1, 31, 'active', 'Primary teaching candidate currently available for rural placements.'),
  (5, 1, 2, 19, 'review', 'Cross-specialty evaluation kept for demonstration purposes.')
ON DUPLICATE KEY UPDATE
  candidate_id = VALUES(candidate_id),
  list_id = VALUES(list_id),
  ranking = VALUES(ranking),
  status = VALUES(status),
  notes = VALUES(notes);

INSERT INTO tracked_candidates (id, user_id, candidate_id, label, is_active) VALUES
  (1, (SELECT id FROM users WHERE email = 'writer@example.com'), 1, 'High priority profile', 1),
  (2, (SELECT id FROM users WHERE email = 'writer@example.com'), 3, 'Awaiting review outcome', 1),
  (3, (SELECT id FROM users WHERE email = 'viewer@example.com'), 2, 'Follow for shortlist updates', 1)
ON DUPLICATE KEY UPDATE
  user_id = VALUES(user_id),
  candidate_id = VALUES(candidate_id),
  label = VALUES(label),
  is_active = VALUES(is_active);

INSERT INTO audit_logs (id, user_id, action, entity_type, entity_id) VALUES
  (1, (SELECT id FROM users WHERE email = 'admin@example.com'), 'seed', 'specialty', 1),
  (2, (SELECT id FROM users WHERE email = 'admin@example.com'), 'seed', 'appointment_list', 1),
  (3, (SELECT id FROM users WHERE email = 'admin@example.com'), 'seed', 'candidate', 1),
  (4, (SELECT id FROM users WHERE email = 'admin@example.com'), 'seed', 'candidate_list_entry', 1),
  (5, (SELECT id FROM users WHERE email = 'writer@example.com'), 'seed', 'tracked_candidate', 1)
ON DUPLICATE KEY UPDATE
  action = VALUES(action),
  entity_type = VALUES(entity_type),
  entity_id = VALUES(entity_id);
