SET NAMES utf8mb4;

INSERT INTO annonces (
    user_id, title, description, type, preferred_date,
    from_city, to_city, from_lat, from_lng, to_lat, to_lng,
    price, weight, volume, constraints,
    status, photo, created_at, updated_at
) VALUES
      (2, 'Montage meuble IKEA', 'Besoin d’aide pour monter une armoire', 'service', '2025-07-21',
       NULL, NULL, NULL, NULL, NULL, NULL,
       40.00, NULL, NULL, '2 heures estimées',
       'publiée', NULL, NOW(), NOW()),

      (2, 'Aide déménagement', 'Besoin de bras pour déplacer des cartons', 'service', '2025-07-23',
       NULL, NULL, NULL, NULL, NULL, NULL,
       60.00, NULL, NULL, NULL,
       'publiée', NULL, NOW(), NOW()),

      (2, 'Jardinage ponctuel', 'Tonte de pelouse', 'service', '2025-07-28',
       NULL, NULL, NULL, NULL, NULL, NULL,
       25.00, NULL, NULL, NULL,
       'publiée', NULL, NOW(), NOW());

