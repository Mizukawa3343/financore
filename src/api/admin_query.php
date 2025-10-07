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
    $sql = "SELECT * FROM fees_type WHERE department_id = ?";
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