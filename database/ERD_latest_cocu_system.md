# COCU System ERD (Latest - Clean View)

This ERD is based on the live `cocu_db` schema as of 2026-04-17.

```mermaid
erDiagram
    USERS {
        int userID PK
        varchar student_id UK
        varchar email UK
        varchar name
        tinyint isAdmin
        timestamp created_at
    }

    CLUB_CATALOG {
        int clubCatalogID PK
        varchar clubName UK
        int created_by FK
        tinyint is_active
        timestamp created_at
    }

    EVENTS {
        int eventID PK
        int userID FK
        int clubCatalogID FK
        varchar eventTitle
        varchar eventType
        date eventDate
        decimal eventHours
        int participantCapacity
        int registeredCount
        tinyint waitlistEnabled
        int waitlistCount
        enum status
        int reviewed_by FK
    }

    MERITS {
        int meritID PK
        int userID FK
        int eventID FK
        int achievementID
        varchar activityName
        int hours
        int base_hours
        int achievement_bonus
        enum status
        int reviewed_by FK
        int resubmission_count
    }

    ACHIEVEMENTS {
        int achievementID PK
        int userID FK
        int eventID FK
        varchar title
        varchar category
        varchar achievementLevel
        enum status
        int reviewed_by FK
    }

    CLUBS {
        int clubID PK
        int userID FK
        varchar clubName
        varchar role
        enum request_type
        enum status
        int reviewed_by FK
    }

    MERIT_STATUS_LOGS {
        int logID PK
        int meritID FK
        int changed_by FK
        enum from_status
        enum to_status
        timestamp created_at
    }

    MERIT_CERTIFICATES {
        int certificateID PK
        int userID FK
        int source_meritID FK
        int issued_by FK
        int milestone_hours
        int approved_hours_snapshot
        varchar certificate_code UK
        timestamp issued_at
    }

    USERS ||--o{ EVENTS : submits
    USERS ||--o{ MERITS : submits
    USERS ||--o{ ACHIEVEMENTS : submits
    USERS ||--o{ CLUBS : submits
    USERS ||--o{ CLUB_CATALOG : creates

    CLUB_CATALOG ||--o{ EVENTS : categorizes
    EVENTS ||--o{ MERITS : linked_to
    EVENTS ||--o{ ACHIEVEMENTS : linked_to

    MERITS ||--o{ MERIT_STATUS_LOGS : audited_by_logs
    MERITS ||--o{ MERIT_CERTIFICATES : certificate_source
```

## Secondary FK Links (kept out of main diagram for readability)

- `achievements.reviewed_by -> users.userID`
- `clubs.reviewed_by -> users.userID`
- `events.reviewed_by -> users.userID`
- `merits.reviewed_by -> users.userID`
- `merit_status_logs.changed_by -> users.userID`
- `merit_certificates.issued_by -> users.userID`

## Schema Note

- `merits.achievementID` exists in the live DB, but currently has no FK constraint to `achievements.achievementID`.
