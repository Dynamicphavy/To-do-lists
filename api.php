<?php

header("Content-Type: application/json; charset=UTF-8");

$file = __DIR__ . "/tasks.json"

// Read existing tasks
function getTasks($file) {
    if (!file_exists($file)) {
        file_put_contents($file, "[]")
    }

    $json = file_get_contents($file);
    $tasks = json_decode($json, true);

    return is_array($tasks) ? $tasks : [];
}

// Save tasks to JSON
function saveTasks($file, $tasks) {
    $json = json_encode(
        $tasks,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    return file_put_contents($file, $json, LOCK_EX) !== false;
}

// GET: Return all tasks
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo json_encode(getTasks($file));
    exit;
}

// Only allow GET and POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// Read JSON request body
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

$action = $data["action"] ?? "";
$tasks = getTasks($file);

// Add a task
if ($action === "add") {
    $taskText = trim($data["task"] ?? "");

    if ($taskText === "") {
        http_response_code(400);
        echo json_encode(["error" => "Task cannot be empty"]);
        exit
    }

    $task = [
        "id" => uniqid("task_", true),
        "task" => $taskText,
        "done" => false
    ];

    $tasks[] = $task;
}

// Complete or uncomplete a task
elseif ($action === "update") {
    $found = false;

    foreach ($tasks as &$task) {
        if ($task["id"] === ($data["id"] ?? "")) {
            $task["done"] = (bool) ($data["done"] ?? false);
            $found = true;
            break;
        }
    }
    unset($task);

    if (!$found) {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
        exit;
    }
}

// Delete a task
elseif ($action === "delete") {
    $id = $data["id"] ?? "";
    $originalCount = count($tasks);

    $tasks = array_values(array_filter(
        $tasks,
        fn($task) => $task["id"] !== $id
    ));

    if (count($tasks) === $originalCount) {
        http_response_code(404);
        echo json_encode(["error" => "Task not found"]);
        exit;
    }
}

// Reject unknown actions
else {
    http_response_code(400);
    echo json_encode(["error" => "Unknown action"]);
    exit;
}

// Save changes
if (!saveTasks($file, $tasks)) {
    http_response_code(500);
    echo json_encode(["error" => "Could not save tasks"]);
    exit;
}

echo json_encode(["success" => true, "tasks" => $tasks]);