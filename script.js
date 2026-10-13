const input = document.querySelector("#to_do_name");
const addButton = document.querySelector("#to_do_add_btn");
const taskList = document.querySelector("#to_do_list");

const API_URL = "api.php";

// Communicate with the PHP backend
async function request(action, data = {}, method = "POST") {
  const options = { method };

  if (method === "POST") {
    options.headers = {
      "Content-Type": "application/json"
    };

    options.body = JSON.stringify({ action, ...data });
  }
  
  const response = await fetch(
    method === "GET" ? API_URL : API_URL,
    options
  );
  
  const result = await response.json();
  
  if (!response.ok) {
    throw new Error(result.error || "Request failed");
  }

  return result;
}

// Display one task
function renderTask(task) {
  const li = document.createElement("li");
  li.dataset.id = task.id;

  const item = document.createElement("div");
  item.className = "to-do-item";

  const text = document.createElement("div");
  text.className = "to-do";
  text.textContent = task.task;

  const actions = document.createElement("div");
  actions.className = "actions";

  const doneButton = document.createElement("button");
  doneButton.type = "button";
  doneButton.className = "done";
  doneButton.textContent = "✓";
  doneButton.setAttribute("aria-label", "Toggle task completion");

  const deleteButton = document.createElement("button");
  
}