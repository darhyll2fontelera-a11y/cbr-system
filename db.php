<?php
$conn = new mysqli("localhost", "root", "", "blood_donation_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ── Safely add columns to donations table if missing ──
$cols = [];
$r = $conn->query("SHOW COLUMNS FROM donations");
if ($r) { while ($c = $r->fetch_assoc()) $cols[] = $c['Field']; }

if (!in_array('user_id', $cols)) {
    $conn->query("ALTER TABLE donations ADD COLUMN user_id INT DEFAULT NULL");
}
if (!in_array('type', $cols)) {
    $conn->query("ALTER TABLE donations ADD COLUMN type VARCHAR(20) DEFAULT 'donation'");
}

// ── Create donor_screening table if it doesn't exist ──
// This table stores every answer from the 15-question eligibility quiz,
// linked to the matching donation record.
$conn->query("
    CREATE TABLE IF NOT EXISTS donor_screening (
        id                  INT AUTO_INCREMENT PRIMARY KEY,
        donation_id         INT          DEFAULT NULL COMMENT 'FK → donations.id',
        user_id             INT          DEFAULT NULL COMMENT 'FK → users.id',

        -- Vitals (numeric)
        sc_age              INT          DEFAULT NULL COMMENT 'Donor age in years',
        sc_weight           DECIMAL(5,1) DEFAULT NULL COMMENT 'Donor weight in kg',

        -- Q3 – Q15 (yes / no)
        sc_last_donated     VARCHAR(3)   DEFAULT NULL COMMENT 'Q3: Donated in last 56 days?',
        sc_feeling_well     VARCHAR(3)   DEFAULT NULL COMMENT 'Q4: Feeling well today?',
        sc_heart_condition  VARCHAR(3)   DEFAULT NULL COMMENT 'Q5: Heart disease / high BP / stroke?',
        sc_diabetes         VARCHAR(3)   DEFAULT NULL COMMENT 'Q6: Insulin-dependent or uncontrolled diabetes?',
        sc_hepatitis_hiv    VARCHAR(3)   DEFAULT NULL COMMENT 'Q7: Hepatitis B/C, HIV/AIDS, or syphilis?',
        sc_active_cancer    VARCHAR(3)   DEFAULT NULL COMMENT 'Q8: Active cancer or blood disorder?',
        sc_travel_endemic   VARCHAR(3)   DEFAULT NULL COMMENT 'Q9: Traveled to malaria/dengue area last 12 mo?',
        sc_tattoo_piercing  VARCHAR(3)   DEFAULT NULL COMMENT 'Q10: Tattoo or piercing in last 12 months?',
        sc_iv_drugs         VARCHAR(3)   DEFAULT NULL COMMENT 'Q11: IV drug use (non-prescribed)?',
        sc_recent_procedure VARCHAR(3)   DEFAULT NULL COMMENT 'Q12: Surgery, dental, or transfusion last 12 mo?',
        sc_pregnant         VARCHAR(3)   DEFAULT NULL COMMENT 'Q13: Pregnant, gave birth <6 mo, or breastfeeding?',
        sc_medications      VARCHAR(3)   DEFAULT NULL COMMENT 'Q14: On antibiotics, blood thinners, or Accutane?',
        sc_recent_vaccine   VARCHAR(3)   DEFAULT NULL COMMENT 'Q15: Vaccine received in last 4 weeks?',

        -- Overall result
        eligible            VARCHAR(3)   DEFAULT 'yes' COMMENT 'Passed all 15 checks: yes/no',

        created_at          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_donation  (donation_id),
        INDEX idx_user      (user_id)
    )
");
?>
