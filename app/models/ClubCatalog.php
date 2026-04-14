<?php

class ClubCatalog {

    public static function getAllActive() {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM club_catalog WHERE is_active = 1 ORDER BY clubName ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll($search = null) {
        $db = Database::connect();
        $sql = "SELECT cc.*, u.name AS createdByName
                FROM club_catalog cc
                LEFT JOIN users u ON u.userID = cc.created_by";
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " WHERE cc.clubName LIKE ? OR cc.description LIKE ?";
            $term = '%' . $search . '%';
            $params = [$term, $term];
        }

        $sql .= " ORDER BY cc.is_active DESC, cc.clubName ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM club_catalog WHERE clubCatalogID = ?");
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($clubName, $description = null, $createdBy = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO club_catalog (clubName, description, created_by)
             VALUES (?, ?, ?)"
        );
        return $stmt->execute([
            trim((string) $clubName),
            $description !== null ? trim((string) $description) : null,
            $createdBy !== null ? (int) $createdBy : null,
        ]);
    }

    public static function setActiveStatus($id, $isActive) {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE club_catalog SET is_active = ? WHERE clubCatalogID = ?");
        return $stmt->execute([(int) ($isActive ? 1 : 0), (int) $id]);
    }
}

?>
