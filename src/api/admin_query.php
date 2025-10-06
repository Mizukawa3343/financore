<?php

function get_courses_by_department_id($conn, $department_id)
{
    $sql = "SELECT * FROM course WHERE department_id = ?";
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
    c.course AS course_name,
    s.year,
    d.acronym AS department_acronym
FROM
    students s
JOIN
    course c ON s.course = c.id
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