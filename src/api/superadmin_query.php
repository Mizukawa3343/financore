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

function get_all_logs($conn)
{
    $sql = "
    SELECT
    l.action,
    u.full_name AS user_fullname,
    d.acronym AS department_acronym,
    l.date
FROM
    logs l
JOIN
    users u ON l.user_id = u.id
JOIN
    department d ON l.department_id = d.id
ORDER BY
    l.date DESC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function department_performance_report($conn)
{
    $sql = '
    SELECT
    d.department_name,
    -- Total Assigned Fees: The sum of the original amount due for all fees in that department.
    SUM(sf.amount_due) AS "total_assigned",
    -- Total Collected: The total amount paid is calculated by subtracting the outstanding balance from the total amount due.
    (SUM(sf.amount_due) - SUM(sf.current_balance)) AS "total_collected",
    -- Collection Rate: (Total Paid / Total Due) * 100
    ROUND(( (SUM(sf.amount_due) - SUM(sf.current_balance)) / SUM(sf.amount_due) ) * 100, 2) AS "collection_rate"
FROM
    department d
JOIN
    fees_type ft ON d.id = ft.department_id
JOIN
    student_fees sf ON ft.id = sf.fees_id
GROUP BY
    d.department_name
ORDER BY
    "Collection rate" DESC;
    ';
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function performing_fees_report($conn)
{
    $sql = '
    SELECT
    ft.description AS "fee_name",
    d.department_name,
    ft.amount,
    COUNT(sf.id) AS "student_assigned",
    -- Use COALESCE to replace NULL total collected with 0
    COALESCE(SUM(sf.amount_due) - SUM(sf.current_balance), 0) AS "total_collected",
    -- Use COALESCE to replace NULL outstanding balance with 0
    COALESCE(SUM(sf.current_balance), 0) AS "outstanding_balance"
FROM
    fees_type ft
JOIN
    department d ON ft.department_id = d.id
LEFT JOIN
    student_fees sf ON ft.id = sf.fees_id
GROUP BY
    ft.description, d.department_name, ft.amount
ORDER BY
    d.department_name, "outstanding_balance" DESC;
    ';
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_monthly_audit_trail($conn)
{
    $sql = "
    SELECT
    l.date AS transaction_date,
    u.full_name AS user_fullname,
    d.acronym AS department_acronym,
    l.action AS action_performed
FROM
    logs l
JOIN
    users u ON l.user_id = u.id
JOIN
    department d ON l.department_id = d.id
WHERE
    -- Get the first day of the current month (e.g., '2025-10-01')
    l.date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    -- Get the first day of the next month (e.g., '2025-11-01')
    -- By checking for dates LESS THAN the next month's start, we include the entire current month.
    AND l.date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
ORDER BY
    l.date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}