package main

import (
    "encoding/json"
    "io/ioutil"
    "log"
    "net/http"
    "os"
)

type Task struct {
    Task string `json:"task"`
    Done bool   `json:"done"`
}

const filePath = "tasks.json"

func main() {
    http.Handle("/", http.FileServer(http.Dir("./static")))

    http.HandleFunc("/api/add", func(w http.ResponseWriter, r *http.Request) {
        if r.Method != http.MethodPost {
            http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
            return
        }

        var newTask Task
        err := json.NewDecoder(r.Body).Decode(&newTask)
        if err != nil {
            http.Error(w, "Invalid JSON", http.StatusBadRequest)
            return
        }

        // Read existing tasks
        var tasks []Task
        if _, err := os.Stat(filePath); err == nil {
            data, _ := ioutil.ReadFile(filePath)
            json.Unmarshal(data, &tasks)
        }

        tasks = append(tasks, newTask)

        jsonData, _ := json.MarshalIndent(tasks, "", "  ")
        ioutil.WriteFile(filePath, jsonData, 0644)

        w.WriteHeader(http.StatusCreated)
        json.NewEncoder(w).Encode(newTask)
    })

    log.Println("✅ Server running at http://localhost:8080")
    log.Fatal(http.ListenAndServe(":8080", nil))
}
