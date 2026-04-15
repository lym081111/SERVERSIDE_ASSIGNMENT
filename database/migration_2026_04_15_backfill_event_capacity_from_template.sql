-- Migration: backfill event capacity from existing template rows
-- For rows with NULL participantCapacity, copy from earliest matching
-- event signature (club + title + type + date) that has capacity.

START TRANSACTION;

UPDATE events e
JOIN (
    SELECT e1.eventID,
           (
               SELECT e2.participantCapacity
               FROM events e2
               WHERE e2.clubCatalogID <=> e1.clubCatalogID
                 AND e2.eventDate = e1.eventDate
                 AND LOWER(TRIM(e2.eventTitle)) = LOWER(TRIM(e1.eventTitle))
                 AND LOWER(TRIM(COALESCE(e2.eventType, ''))) = LOWER(TRIM(COALESCE(e1.eventType, '')))
                 AND e2.participantCapacity IS NOT NULL
                 AND e2.participantCapacity > 0
                 AND e2.status IN ('pending', 'approved')
               ORDER BY e2.eventID ASC
               LIMIT 1
           ) AS templateCapacity,
           (
               SELECT e2.waitlistEnabled
               FROM events e2
               WHERE e2.clubCatalogID <=> e1.clubCatalogID
                 AND e2.eventDate = e1.eventDate
                 AND LOWER(TRIM(e2.eventTitle)) = LOWER(TRIM(e1.eventTitle))
                 AND LOWER(TRIM(COALESCE(e2.eventType, ''))) = LOWER(TRIM(COALESCE(e1.eventType, '')))
                 AND e2.participantCapacity IS NOT NULL
                 AND e2.participantCapacity > 0
                 AND e2.status IN ('pending', 'approved')
               ORDER BY e2.eventID ASC
               LIMIT 1
           ) AS templateWaitlistEnabled
    FROM events e1
    WHERE e1.participantCapacity IS NULL
) t ON t.eventID = e.eventID
SET e.participantCapacity = t.templateCapacity,
    e.waitlistEnabled = COALESCE(t.templateWaitlistEnabled, e.waitlistEnabled)
WHERE t.templateCapacity IS NOT NULL
  AND t.templateCapacity > 0;

COMMIT;

