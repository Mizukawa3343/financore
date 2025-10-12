<?php

function total_students($conn)
{
    $sql = "
    SELECT
    COUNT(id) AS total_student_count
FROM
    students;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_overall_collected_all_departments($conn)
{
    $sql = "
    SELECT
    -- Total Collected: The difference between what was due and what is currently outstanding (paid portion)
    COALESCE(SUM(sf.amount_due - sf.current_balance), 0.00) AS total_collected_lifetime,
    
    -- Total Assigned: The total gross amount of all fees ever assigned
    COALESCE(SUM(sf.amount_due), 0.00) AS total_assigned_lifetime
FROM
    student_fees sf;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_overall_balances_all_departments($conn)
{
    $sql = "
    SELECT
    COALESCE(SUM(sf.current_balance), 0.00) AS total_outstanding,
    COALESCE(SUM(sf.amount_due), 0.00) AS total_assigned
FROM
    student_fees sf;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_collection_rate_all_departments($conn)
{
    $sql = "
    SELECT
    -- Total Collected: The difference between what was due and what is currently outstanding (paid portion)
    COALESCE(SUM(sf.amount_due - sf.current_balance), 0.00) AS total_collected_lifetime,
    
    -- Total Assigned: The total gross amount of all fees ever assigned
    COALESCE(SUM(sf.amount_due), 0.00) AS total_assigned_lifetime
FROM
    student_fees sf;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_all_active_departments($conn)
{
    $sql = "
    SELECT
    COUNT(id) AS total_active_departments
FROM
    department;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_all_transaction_all_department($conn)
{
    $sql = "
    SELECT
    COUNT(id) AS transaction_count_today,
    COALESCE(SUM(amount_paid), 0.00) AS total_amount_today
FROM
    payment_transaction pt
WHERE
    DATE(pt.transaction_date) = CURDATE();
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_overdues_all_department($conn)
{
    $sql = "
    SELECT
    COUNT(sf.id) AS overdue_fee_count,
    COALESCE(SUM(sf.current_balance), 0.00) AS total_overdue_amount
FROM
    student_fees sf
JOIN
    fees_type ft ON sf.fees_id = ft.id
WHERE
    sf.current_balance > 0.00    -- Condition 1: Must still have an outstanding balance
    AND ft.due_date < CURDATE();  -- Condition 2: The fee's due date is in the past
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_total_receipts_count($conn)
{
    $sql = "
   SELECT
    COUNT(id) AS total_receipts_generated
FROM
    receipts;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_all_users($conn)
{
    $sql = "
    SELECT * FROM `users` WHERE role = 'admin'
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_all_department($conn)
{
    $sql = "SELECT * FROM department";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function display_department($conn)
{
    $sql = "
    SELECT
    d.id AS department_id,
    d.department_name,
    -- Concatenate the user's full name, and show a message if no admin is linked
    COALESCE(u.full_name, 'No Admin Assigned') AS secretary
FROM
    department d
LEFT JOIN
    users u ON d.id = u.department_id 
    -- ❗ Filter to ensure we only match users who are explicitly 'admin'
    AND u.role = 'admin' 
ORDER BY
    d.id ASC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_department_by_id($conn, $department_id)
{
    $sql = "SELECT * FROM department WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}