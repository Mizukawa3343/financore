<?php

function get_courses_by_department_id($conn, $department_id)
{
    $sql = "SELECT * FROM courses WHERE department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$department_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

function get_all_fees_by_department_id($conn, $department_id)
{
    $sql = "
    SELECT
        ft.id,
        ft.description AS fee_name,
        -- Calculate Total Collected: Use COALESCE to return 0.00 if the sum is NULL
        COALESCE(SUM(sf.amount_due - sf.current_balance), 0.00) AS total_collected,
        -- Calculate Total To Collect: Use COALESCE to return 0.00 if the sum is NULL
        COALESCE(SUM(sf.current_balance), 0.00) AS total_to_collect
    FROM
        fees_type ft
    LEFT JOIN
        student_fees sf ON ft.id = sf.fees_id
    WHERE
        ft.department_id = ?
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
        s.year,                   -- Year level from students table
        c.name AS course_name,    -- Course name from courses table
        sf.status                 -- Payment status of this specific fee
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
        s.student_id, -- The actual student number
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        s.year,
        c.name AS course,
        d.acronym AS department_acronym
    FROM
        students s
    JOIN
        courses c ON s.course = c.id
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