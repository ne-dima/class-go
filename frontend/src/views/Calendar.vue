<template>
    <div class="calendar-page">
      <div class="calendar-header">
        <h1>Календарь заданий</h1>
        <div class="calendar-nav">
          <button @click="prevMonth">←</button>
          <span>{{ formattedMonthYear }}</span>
          <button @click="nextMonth">→</button>
        </div>
      </div>
  
      <div class="calendar-grid">
        <div class="day-name" v-for="day in weekDays" :key="day">{{ day }}</div>
  
        <div v-for="(blank, i) in blanks" :key="'b' + i" class="calendar-cell blank"></div>
  
        <div
          v-for="day in daysInMonth"
          :key="day"
          class="calendar-cell"
          :class="{
            today: isToday(day),
            deadline: isDeadline(day)
          }"
        >
          <span class="day-number">{{ day }}</span>
          <div v-if="isDeadline(day)" class="task-dot">📌 Задание</div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'CalendarPage',
    data() {
      const now = new Date()
      return {
        currentMonth: now.getMonth(),
        currentYear: now.getFullYear(),
        today: now,
        weekDays: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
        deadlines: ['2025-04-08', '2025-04-14', '2025-04-22'] // формат YYYY-MM-DD
      }
    },
    computed: {
      formattedMonthYear() {
        return new Date(this.currentYear, this.currentMonth).toLocaleString('ru-RU', {
          month: 'long',
          year: 'numeric'
        })
      },
      daysInMonth() {
        return new Date(this.currentYear, this.currentMonth + 1, 0).getDate()
      },
      firstDayOfWeek() {
        const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay()
        return firstDay === 0 ? 6 : firstDay - 1
      },
      blanks() {
        return Array(this.firstDayOfWeek).fill(null)
      }
    },
    methods: {
      isToday(day) {
        const d = new Date(this.currentYear, this.currentMonth, day)
        return (
          d.getDate() === this.today.getDate() &&
          d.getMonth() === this.today.getMonth() &&
          d.getFullYear() === this.today.getFullYear()
        )
      },
      isDeadline(day) {
        const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
        return this.deadlines.includes(dateStr)
      },
      nextMonth() {
        if (this.currentMonth === 11) {
          this.currentMonth = 0
          this.currentYear++
        } else {
          this.currentMonth++
        }
      },
      prevMonth() {
        if (this.currentMonth === 0) {
          this.currentMonth = 11
          this.currentYear--
        } else {
          this.currentMonth--
        }
      }
    }
  }
  </script>
  
  <style scoped>
  
  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .calendar-nav button {
    background: none;
    border: 1px solid #d1d5db;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    color: #4b5563;
  }
  
  .calendar-nav span {
    margin: 0 12px;
    font-weight: 500;
    font-size: 16px;
  }
  
  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
  }
  
  .day-name {
    font-weight: bold;
    text-align: center;
    padding-bottom: 6px;
    color: #4b5563;
  }
  
  .calendar-cell {
    background: white;
    padding: 16px 10px;
    text-align: center;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-weight: 500;
    color: #1f2937;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    font-size: 15px;
    position: relative;
  }
  
  .calendar-cell.today {
    background: #eef2ff;
    border: 2px solid #6366f1;
    color: #111827;
  }
  
  .calendar-cell.deadline {
    background-color: #fff7ed;
    border-color: #f97316;
  }
  
  .task-dot {
    margin-top: 10px;
    background: #fde68a;
    color: #92400e;
    padding: 2px 8px;
    font-size: 12px;
    border-radius: 6px;
    font-weight: 500;
  }
  
  .day-number {
    font-size: 16px;
    font-weight: 600;
  }
  
  .blank {
    background: transparent;
    border: none;
  }

  .calendar-cell:hover {
  background-color: #f0f4ff;
  border-color: #93c5fd;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
}

.calendar-cell.today:hover {
  background-color: #e0e7ff;
}

.calendar-cell.deadline:hover {
  background-color: #fff3d5;
  border-color: #fb923c;
}

  </style>
  