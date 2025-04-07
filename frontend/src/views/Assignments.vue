<template>
    <div class="assignments">
      <div class="sidebar">
        <h3>Мои курсы</h3>
        <ul>
          <li
            v-for="(count, course) in courseCounts"
            :key="course"
            :class="{ active: selectedCourse === course }"
            @click="selectedCourse = course"
          >
            {{ course }}
            <span>{{ count }}</span>
          </li>
          <li @click="openModal" class="add-course">+ Присоединиться к курсу</li>
        </ul>
      </div>
  
      <div class="main">
        <div class="top-bar">
          <input type="text" placeholder="Поиск" v-model="searchQuery" />
          <div class="icons">
            <i class="fas fa-hdd"></i>
            <i class="fas fa-trash-alt"></i>
          </div>
        </div>
  
        <table class="tasks-table">
          <tr v-for="task in filteredTasks" :key="task.id">
            <td><input type="checkbox" /></td>
            <td><i class="fas fa-star yellow"></i></td>
            <td><strong>{{ task.course }}</strong></td>
            <td>{{ task.title }}</td>
            <td class="score-cell">
              <span
                v-if="task.score"
                :class="['score', getScoreColor(task.score)]"
              >
                {{ task.score }}
              </span>
              <span v-else class="no-score">Без срока сдачи</span>
            </td>
            <td class="due">{{ task.due }}</td>
          </tr>
        </table>
      </div>
  
      <!-- Модалка -->
      <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
        <div class="modal">
          <h3>Присоединиться к курсу</h3>
          <p>
            Введите 6-значный код курса. Его можно получить у преподавателя или
            других участников курса.
          </p>
          <input type="text" maxlength="6" placeholder="Код курса" />
          <button @click="closeModal">Назад</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "Assignments",
    data() {
      return {
        searchQuery: "",
        selectedCourse: "Все задания",
        showModal: false,
        tasks: [
          {
            id: 1,
            course: "Математика",
            title: "Лабораторная работа 1.",
            score: "88 / 100",
            due: "до 12.03",
          },
          {
            id: 2,
            course: "Физика",
            title: "Лекция 12.02",
            due: "Без срока сдачи",
          },
          {
            id: 3,
            course: "Программирование",
            title: "Курс по C#",
            score: "Просрочено",
            due: "до 01.02",
          },
          {
            id: 4,
            course: "Физика",
            title: "Практикум",
            score: "95 / 100",
            due: "до 12.03",
          },
          {
            id: 5,
            course: "Программирование",
            title: "Курсовая работа 2",
            score: "40 / 100",
            due: "до 12.03",
          },
        ],
      };
    },
    computed: {
      filteredTasks() {
        return this.tasks.filter((t) => {
          const byCourse =
            this.selectedCourse === "Все задания" ||
            t.course === this.selectedCourse;
          const bySearch = t.title
            .toLowerCase()
            .includes(this.searchQuery.toLowerCase());
          return byCourse && bySearch;
        });
      },
      courseCounts() {
        const counts = { "Все задания": this.tasks.length };
        this.tasks.forEach((task) => {
          counts[task.course] = (counts[task.course] || 0) + 1;
        });
        return counts;
      },
    },
    methods: {
      openModal() {
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      getScoreColor(score) {
        if (score.includes("Просрочено")) return "red";
        const percent = parseInt(score);
        if (percent >= 85) return "green";
        if (percent >= 60) return "orange";
        return "red";
      },
    },
  };
  </script>
  
  <style scoped>
  .assignments {
    display: flex;
    gap: 20px;
  }
  
  .sidebar {
    width: 250px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e5e7eb;
  }
  
  .sidebar h3 {
    font-size: 18px;
    margin-bottom: 12px;
  }
  
  .sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .sidebar li {
    padding: 10px 12px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
  }
  
  .sidebar li.active {
    background: #eef2ff;
    color: #4f46e5;
    font-weight: bold;
  }
  
  .add-course {
    color: #4f46e5;
    font-weight: 500;
    margin-top: 12px;
  }
  
  .main {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e5e7eb;
  }
  
  .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  
  .top-bar input {
    padding: 8px 12px;
    width: 300px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
  }
  
  .top-bar .icons i {
    font-size: 20px;
    margin-left: 16px;
    cursor: pointer;
  }
  
  .tasks-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .tasks-table tr {
    border-bottom: 1px solid #e5e7eb;
    height: 48px;
  }
  
  .tasks-table td {
    padding: 8px;
  }
  
  .yellow {
    color: #facc15;
  }
  
  .score {
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 6px;
    font-weight: 600;
  }
  
  .score.green {
    background: #dcfce7;
    color: #22c55e;
  }
  .score.orange {
    background: #fef3c7;
    color: #f59e0b;
  }
  .score.red {
    background: #fee2e2;
    color: #dc2626;
  }
  
  .due {
    font-size: 13px;
    color: #6b7280;
    text-align: right;
    white-space: nowrap;
  }
  
  .modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(17, 24, 39, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }
  
  .modal {
    background: white;
    padding: 30px;
    border-radius: 12px;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    text-align: center;
  }
  
  .modal h3 {
    margin-bottom: 10px;
  }
  
  .modal p {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 20px;
  }
  
  .modal input {
    padding: 8px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    width: 100%;
    margin-bottom: 15px;
  }
  
  .modal button {
    background-color: #4f46e5;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }

.sidebar li:hover {
  background-color: #f3f4f6;
  transition: background 0.2s;
}

.add-course:hover {
  background-color: #f3f4f6;
  border-radius: 6px;
  padding-left: 10px;
  transition: all 0.2s;
}

.tasks-table tr {
  transition: background-color 0.2s, transform 0.2s;
}
.tasks-table tr:hover {
  background-color: #f9fafb;
  transform: translateY(-1px);
  cursor: pointer;
}

.top-bar .icons i {
  transition: color 0.2s;
}
.top-bar .icons i:hover {
  color: #4f46e5;
}

  </style>
  