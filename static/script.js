document.querySelector("#to_do_add_btn").addEventListener("click", () => {
    const input = document.querySelector("#to_do_name");
    const taskText = input.value.trim();

    if (taskText === "") {
        alert("Please Add your tasks!!");
        return;
    }

    const li = document.createElement("li");
    li.innerHTML = `
        <div class="to-do-item">
            <div class="to-do">${taskText}</div>
            <div class="actions">
                <span class="done">&#10003;</span>
                <span class="delete">&#x1f5d1;</span>
            </div>
        </div>`;

    li.querySelector(".delete").addEventListener("click", (e) => {
        e.currentTarget.closest("li").remove();
    });

    li.querySelector(".done").addEventListener("click", (e) => {
        e.currentTarget.closest("li").classList.add("to_do_done");
    });

    document.querySelector("#to_do_list").appendChild(li);
    input.value = "";

    // 🔥 Send task to backend
    fetch("/api/add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ task: taskText, done: false })
    });
});
