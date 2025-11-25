<?php
session_start();

// Function to add items to the session
function addItemsToSession($items) {
    $_SESSION['selected_items'] = $items;
}

// Function to add a worker to the session
function addWorkerToSession($workerId) {
    $_SESSION['selected_worker'] = $workerId;
}

// Function to retrieve session data
function getSessionData() {
    return [
        'items' => $_SESSION['selected_items'] ?? [],
        'worker' => $_SESSION['selected_worker'] ?? null,
    ];
}
?>
