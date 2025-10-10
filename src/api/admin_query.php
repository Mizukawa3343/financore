<?php

function get_courses_by_department_id($conn, $department_id)
{
    $sql = "SELECT * FROM courses WHERE department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_student_by_id($conn, $student_id)
{
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}




function get_all_students_by_department_id($conn, $department_id)
{
    $sql = "
    SELECT
    s.id,
    s.student_id,
    s.last_name,
    s.first_name,
    s.gender,
    c.name AS course_name,
    s.year,
    d.acronym AS department_acronym
FROM
    students s
JOIN
    courses c ON s.course = c.id
JOIN
    department d ON s.department_id = d.id
WHERE
    s.department_id = ?;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_department_by_id($conn, $department_id)
{
    $sql = "SELECT * FROM department WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_all_fees_by_department_id($conn, $department_id)
{
    $sql = "
        SELECT
    ft.id,
    ft.description AS fee_name,
    -- Calculate Total Collected: amount paid by students
    COALESCE(SUM(sf.amount_due - sf.current_balance), 0.00) AS total_collected,
    -- Calculate Total To Collect: static sum of all assigned fees (amount_due)
    COALESCE(SUM(sf.amount_due), 0.00) AS total_to_collect
FROM
    fees_type ft
LEFT JOIN
    student_fees sf ON ft.id = sf.fees_id
WHERE
    ft.department_id = ?
    AND ft.status = 0 -- *** NEW: Filters to include only fee types where the administrative status is 0 ***
GROUP BY
    ft.id, ft.description
ORDER BY
    ft.id;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_fee_by_id($conn, $fee_id)
{
    $sql = "SELECT * FROM fees_type WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fee_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ASSIGN ACTION QUERIES FUNCTION

function assign_fee_to_all_students($conn, $fee_id, $department_id)
{
    $sql = "
    INSERT INTO student_fees (
        student_id, fees_id, amount_due, current_balance, status, assigned_at
    )
    SELECT
        s.id AS student_id,
        ft.id AS fees_id,
        ft.amount AS amount_due,
        ft.amount AS current_balance,
        'unpaid' AS status,
        NOW() AS assigned_at
    FROM
        students s
    CROSS JOIN
        fees_type ft
    WHERE
        ft.id = ?
        AND s.department_id = ?
        AND s.id NOT IN (
            SELECT student_id
            FROM student_fees
            WHERE fees_id = ft.id
        );
    ";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$fee_id, $department_id])) {
        // Returns the number of rows inserted
        return $stmt->rowCount();
    } else {
        return 0; // Return 0 if the query failed to execute
    }
}

function assign_fee_to_all_students_by_year($conn, $fee_id, $department_id, $year)
{
    $sql = "
    INSERT INTO student_fees (
        student_id, fees_id, amount_due, current_balance, status, assigned_at
    )
    SELECT
        s.id, ft.id, ft.amount, ft.amount, 'unpaid', NOW()
    FROM
        students s
    CROSS JOIN
        fees_type ft
    WHERE
        ft.id = ?
        AND s.department_id = ?
        AND s.year = ?
        AND s.id NOT IN (
            SELECT student_id FROM student_fees WHERE fees_id = ft.id
        );
    ";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$fee_id, $department_id, $year])) {
        return $stmt->rowCount();
    } else {
        return 0;
    }
}
function assign_fee_to_all_students_by_course($conn, $fee_id, $department_id, $course_id)
{
    $sql = "
    INSERT INTO student_fees (
        student_id, fees_id, amount_due, current_balance, status, assigned_at
    )
    SELECT
        s.id, ft.id, ft.amount, ft.amount, 'unpaid', NOW()
    FROM
        students s
    CROSS JOIN
        fees_type ft
    WHERE
        ft.id = ?
        AND s.department_id = ?
        AND s.course = ?
        AND s.id NOT IN (
            SELECT student_id FROM student_fees WHERE fees_id = ft.id
        );
    ";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$fee_id, $department_id, $course_id])) {
        return $stmt->rowCount();
    } else {
        return 0;
    }
}

function assign_fee_to_all_students_by_year_and_course($conn, $fee_id, $department_id, $year, $course_id)
{
    $sql = "
    INSERT INTO student_fees (
        student_id, fees_id, amount_due, current_balance, status, assigned_at
    )
    SELECT
        s.id, ft.id, ft.amount, ft.amount, 'unpaid', NOW()
    FROM
        students s
    CROSS JOIN
        fees_type ft
    WHERE
        ft.id = ?
        AND s.department_id = ?
        AND s.year = ?
        AND s.course = ?
        AND s.id NOT IN (
            SELECT student_id FROM student_fees WHERE fees_id = ft.id
        );
    ";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$fee_id, $department_id, $year, $course_id])) {
        return $stmt->rowCount();
    } else {
        return 0;
    }
}

function get_fee_details_by_id($conn, $fee_id, $department_id)
{
    $sql = "
    SELECT
        -- Fee Details
        ft.description AS fee_name,
        ft.amount AS fee_unit_amount,
        ft.due_date,
        
        -- Aggregated Metrics
        COUNT(sf.student_id) AS total_students_assigned,

        -- Total Collected (Payments Received)
        COALESCE(SUM(sf.amount_due - sf.current_balance), 0.00) AS total_collected,
        
        -- Total To Collect (Remaining Balance)
        COALESCE(SUM(sf.current_balance), 0.00) AS total_to_collect
        
    FROM
        fees_type ft
    LEFT JOIN
        student_fees sf ON ft.id = sf.fees_id
    WHERE
        ft.id = ?                -- Filter by Fee ID
        AND ft.department_id = ? -- Authorization Check
    GROUP BY
        ft.id, ft.description, ft.amount, ft.due_date; -- Must group by all selected non-aggregate fields
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fee_id, $department_id]);

    // Fetch the single row of results
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_students_assigned_to_fee($conn, $fee_id, $department_id)
{
    $sql = "
    SELECT
        s.id,                     -- Student's internal Row ID
        s.student_id,             -- The actual student number
        s.last_name,
        s.first_name,
        s.year,
        s.course,                   -- Year level from students table
        c.name AS course_name,    -- Course name from courses table
        sf.status,
        sf.current_balance                 -- Payment status of this specific fee
    FROM
        student_fees sf
    JOIN
        students s ON sf.student_id = s.id
    JOIN
        courses c ON s.course = c.id
    WHERE
        sf.fees_id = ?              -- Filter by the specific Fee ID
        AND s.department_id = ?     -- Authorization: Filter by the admin's department
    ORDER BY
        s.last_name, s.first_name;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fee_id, $department_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_student_info_by_id($conn, $student_id)
{
    $sql = "
    SELECT
        s.picture,
        s.first_name,
        s.last_name,
        s.student_id,
        s.gender, -- The actual student number
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        s.year,
        c.name AS course,
        c.id AS course_id, -- Added: The course ID (from the courses table)
        d.acronym AS department_acronym
    FROM
        students s
    JOIN
        courses c ON CONVERT(s.course, UNSIGNED INTEGER) = c.id
    JOIN
        department d ON s.department_id = d.id
    WHERE
        s.id = ? -- Filter by the internal student row ID
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id]);

    // Fetch a single row
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_outstanding_fees_by_student_id($conn, $student_id)
{
    $sql = "
    SELECT
        sf.fees_id,
        ft.description AS fee_name,
        sf.amount_due,          -- The original amount of the fee
        sf.current_balance,     -- The remaining amount the student still owes
        ft.due_date,
        sf.status
    FROM
        student_fees sf
    JOIN
        fees_type ft ON sf.fees_id = ft.id
    WHERE
        sf.student_id = ?
        -- Selects fees that are either entirely 'unpaid' OR paid 'partial'ly
        AND sf.status IN ('unpaid', 'partial')
        -- Ensures we only return fees where the remaining balance is greater than zero
        AND sf.current_balance > 0.00
    ORDER BY
        ft.due_date ASC;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// RECEIPTS QUERY
function get_students_data_for_receipt($conn, $student_id)
{
    $sql = "
    SELECT
    s.student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    s.year AS student_year,
    c.name AS course
FROM
    students s
JOIN
    courses c ON CONVERT(s.course, UNSIGNED INTEGER) = c.id
WHERE
    s.id = ?;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_transaction_data_for_receipt($conn, $transaction_id)
{
    $sql = "
    SELECT
    pt.amount_paid,
    pt.transaction_date AS date,
    pt.student_fees_id,
    pt.recorded_by_user_id,
    u.full_name AS processed_by
FROM
    payment_transaction pt
JOIN
    users u ON pt.recorded_by_user_id = u.id
WHERE
    pt.id = ?;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$transaction_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_student_fee_details_for_receipt($conn, $student_id, $fees_id)
{
    $sql = "
        SELECT
            sf.*,
            ft.description AS fee_description
        FROM
            student_fees sf
        JOIN
            fees_type ft ON sf.fees_id = ft.id
        WHERE
            sf.student_id = ? AND sf.fees_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id, $fees_id]);

    // The returned array will contain:
    // id, student_id, fees_id, amount_due, current_balance, status, assigned_at, AND fee_description
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_receipt_number_for_receipt($conn, $receipt_id)
{
    $sql = "
    SELECT
    receipt_number
FROM
    receipts
WHERE
    id = ?;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$receipt_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// STUDENT PERSONAL PAYMENT HISTORY

function get_student_transaction_history($conn, $student_id)
{
    $sql = "
    SELECT 
    pt.id AS transaction_id,
    pt.student_fees_id,
    pt.transaction_date,
    pt.amount_paid,
    pt.payment_method,
    pt.receipt_id,
    pt.recorded_by_user_id
FROM payment_transaction pt
WHERE pt.student_id = ?
ORDER BY pt.transaction_date DESC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_fee_type_by_id($conn, $student_id)
{
    $sql = "
    SELECT * FROM fees_type WHERE id = ? 
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_user_by_id($conn, $user_id)
{
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// DASHBOARD QUERY

function get_total_students_by_department($conn, $department_id)
{
    $sql = "
    SELECT COUNT(*) AS total_students FROM students WHERE department_id = ?;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_total_collected_by_department($conn, $department_id)
{
    $sql = "
    SELECT
    COALESCE(SUM(pt.amount_paid), 0.00) AS total_revenue_current_month
FROM
    payment_transaction pt
JOIN
    students s ON pt.student_id = s.id
WHERE
    s.department_id = ? -- Filter by the authorized Department ID
    AND YEAR(pt.transaction_date) = YEAR(CURDATE())
    AND MONTH(pt.transaction_date) = MONTH(CURDATE());
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_outstanding_balance_by_department($conn, $department_id)
{
    $sql = "
    SELECT
    COALESCE(SUM(sf.current_balance), 0.00) AS total_outstanding_balance
FROM
    student_fees sf
JOIN
    students s ON sf.student_id = s.id
WHERE
    s.department_id = ?; -- Filter by the authorized Department ID
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_pending_payments_by_department($conn, $department_id)
{
    $sql = "
    SELECT
    COUNT(sf.id) AS count_pending_fees,
    COALESCE(SUM(sf.current_balance), 0.00) AS total_pending_amount
FROM
    student_fees sf
JOIN
    students s ON sf.student_id = s.id
JOIN
    fees_type ft ON sf.fees_id = ft.id
WHERE
    s.department_id = ? -- Filter by the authorized Department ID
    AND sf.current_balance > 0.00 -- Must have an outstanding balance
    -- Condition to find fees that are either OVERDUE or DUE SOON (next 30 days)
    AND (
        ft.due_date < CURDATE() -- Fees that are Overdue
        OR ft.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) -- Fees due within the next 30 days
    );
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_today_transaction_by_department($conn, $department_id)
{
    $sql = "
    SELECT
    COUNT(pt.id) AS total_transactions_today,
    COALESCE(SUM(pt.amount_paid), 0.00) AS total_revenue_today
FROM
    payment_transaction pt
JOIN
    students s ON pt.student_id = s.id
WHERE
    s.department_id = ? -- Filter by the authorized Department ID
    -- Filters the transactions to only include those that occurred on the current date
    AND DATE(pt.transaction_date) = CURDATE();
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_overdue_fees_by_department($conn, $department_id)
{
    $sql = "
    SELECT
    COUNT(sf.id) AS count_overdue_fees,
    COALESCE(SUM(sf.current_balance), 0.00) AS total_overdue_amount
FROM
    student_fees sf
JOIN
    students s ON sf.student_id = s.id
JOIN
    fees_type ft ON sf.fees_id = ft.id
WHERE
    s.department_id = ? -- Filter by the authorized Department ID
    AND sf.current_balance > 0.00 -- Must have an outstanding balance
    AND ft.due_date < CURDATE(); -- The due date must be before today (i.e., overdue)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_total_assigned_fees_by_department($conn, $department_id)
{
    $sql = "
   SELECT
    COALESCE(SUM(sf.amount_due), 0.00) AS total_fees_assigned
FROM
    student_fees sf
JOIN
    students s ON sf.student_id = s.id
WHERE
    s.department_id = ?; -- Filter by the authorized Department ID
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_total_fully_paid_students_by_department($conn, $department_id)
{
    //     $sql = "
//    SELECT
//     COUNT(s.id) AS count_fully_paid_students
// FROM
//     students s
// WHERE
//     s.department_id = ? -- Filter by the authorized Department ID
//     -- Exclude any student who has an outstanding balance on an ACTIVE fee (status = 0)
//     AND s.id NOT IN (
//         SELECT
//             sf.student_id
//         FROM
//             student_fees sf
//         JOIN
//             fees_type ft ON sf.fees_id = ft.id  -- Join to check the fee's administrative status
//         WHERE
//             sf.current_balance > 0.00
//             AND ft.status = 0                   -- *** NEW FILTER: Only check status for active fees ***
//         GROUP BY
//             sf.student_id
//     );
//     ";

    $sql = "
SELECT
    COUNT(s.id) AS count_fully_paid_students
FROM
    students s
WHERE
    s.department_id = ? -- Filter by the authorized Department ID
    
    -- CONDITION 1: The student MUST NOT have any outstanding balance on an ACTIVE fee (ft.status = 0).
    AND s.id NOT IN (
        SELECT
            sf.student_id
        FROM
            student_fees sf
        JOIN
            fees_type ft ON sf.fees_id = ft.id
        WHERE
            sf.current_balance > 0.00
            AND ft.status = 0 -- Only look at balances for active fees
        GROUP BY
            sf.student_id
    )
    
    -- CONDITION 2: The student MUST have at least one active fee (ft.status = 0) assigned to be considered in this count.
    AND EXISTS (
        SELECT 1
        FROM student_fees sf_active
        JOIN fees_type ft_active ON sf_active.fees_id = ft_active.id
        WHERE sf_active.student_id = s.id
          AND ft_active.status = 0 -- Check if they have been assigned any ACTIVE fee
    );
";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_recent_transaction($conn, $department_id)
{
    $sql = "
    SELECT
    pt.id AS transaction_id,
    pt.receipt_id,
    pt.student_id,
    s.picture AS student_picture,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    pt.amount_paid,
    pt.transaction_date
FROM
    payment_transaction pt
JOIN
    students s ON pt.student_id = s.id
WHERE
    pt.department_id = ? -- e.g., 1 for CBMIT
ORDER BY
    pt.transaction_date DESC
LIMIT 6;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetchAll();
}

// REPORTS QUERY
function get_monthly_transaction($conn, $department_id)
{
    $sql = "
    SELECT
    *
FROM
    payment_transaction
WHERE
    -- 1. Get the first day of the current month
    transaction_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    
    -- 2. Get the first day of the *next* month (exclusive end date)
    AND transaction_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
	AND department_id = ?;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fee_summary_report($conn, $department_id)
{
    $sql = "
    SELECT
    FT.description AS Fee_Name,
    FT.amount AS Fee_Base_Amount,
    FT.due_date AS Due_Date,
    COUNT(SF.id) AS Assigned_Students_Count,
    SUM(SF.amount_due) AS Total_Due_for_Fee,
    SUM(SF.amount_due - SF.current_balance) AS Total_Collected,
    SUM(SF.current_balance) AS Remaining_Balance
FROM
    fees_type FT
JOIN
    department D ON FT.department_id = D.id
JOIN
    student_fees SF ON FT.id = SF.fees_id
WHERE
    D.id = ? -- Filter by the Admin's department
GROUP BY
    FT.description, FT.amount, FT.due_date
ORDER BY
    FT.due_date ASC;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function student_balances_report($conn, $department_id)
{
    $sql = "
    SELECT
    S.student_id AS Student_ID,
    S.last_name,
    S.first_name,
    C.name AS Course_Name,
    FT.description AS Fee_Name,
    SF.amount_due AS Total_Fee_Amount,
    (SF.amount_due - SF.current_balance) AS Amount_Paid,
    SF.current_balance AS Current_Balance,
    FT.due_date AS Fee_Due_Date,
    SF.status AS Fee_Status
FROM
    student_fees SF
JOIN
    students S ON SF.student_id = S.id
JOIN
    fees_type FT ON SF.fees_id = FT.id
JOIN
    courses C ON S.course = C.id -- Assuming S.course stores the course ID
WHERE
    S.department_id = ? -- Filter by the Admin's department
    AND SF.current_balance > 0.00 -- Only show fees with an outstanding balance
ORDER BY
    S.last_name, SF.current_balance DESC;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
