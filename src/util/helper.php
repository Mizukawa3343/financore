<?php

function format_readable_date($date_string)
{
    // Check if the date string is valid and not empty
    if (empty($date_string) || strtotime($date_string) === false) {
        return "Invalid Date";
    }

    // Convert the date string to a timestamp
    $timestamp = strtotime($date_string);

    // Format the timestamp into a readable date without the time
    // F: Full month name (e.g., September)
    // j: Day of the month without leading zeros (1 to 31)
    // Y: 4-digit year (e.g., 2025)
    $readable_date = date('F j, Y', $timestamp);

    return $readable_date;
}

function format_readable_datetime($date_string)
{
    if (empty($date_string) || strtotime($date_string) === false) {
        return "Invalid Date";
    }

    $timestamp = strtotime($date_string);

    // Example: October 3, 2025 2:35 PM
    return date('F j, Y g:i A', $timestamp);
}
function get_department_logo($department_id)
{
    switch ($department_id) {
        case 1:
            return "/ctc-feex/assets/system-image/cbmit.png";
        case 2:
            return "/ctc-feex/assets/system-image/cte.png";
        case 3:
            return "/ctc-feex/assets/system-image/crim.png";
        case 4:
            return "/ctc-feex/assets/system-image/shs.png";
        default:
            return "/ctc-feex/assets/images/ctc-logo.png";
    }
}