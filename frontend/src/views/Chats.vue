<template>
    <div class="chats">
      <div class="sidebar">
        <h3>Чаты курсов</h3>
        <ul>
          <li
            v-for="(count, course) in chatCounts"
            :key="course"
            :class="{ active: selectedCourse === course }"
            @click="selectedCourse = course"
          >
            {{ course }}
            <span>{{ count }}</span>
          </li>
        </ul>
      </div>
  
      <div class="main">
        <div class="top-bar">
          <input type="text" placeholder="Поиск сообщений..." v-model="searchQuery" />
          <div class="icons">
            <i class="fas fa-paper-plane"></i>
          </div>
        </div>
  
        <div class="messages-list">
          <div
            v-for="msg in filteredMessages"
            :key="msg.id"
            class="message"
          >
            <div class="message-header">
              <img :src="msg.avatar" class="avatar" />
              <div>
                <div class="name">{{ msg.name }}</div>
                <div class="date">{{ msg.date }}</div>
              </div>
            </div>
            <div class="message-text">{{ msg.text }}</div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ChatsPage',
    data() {
      return {
        searchQuery: '',
        selectedCourse: 'Все чаты',
        messages: [
          {
            id: 1,
            course: 'Математика',
            name: 'Алексей Мышев',
            text: 'Привет, кто готов к лабе?',
            date: '12.03.2025',
            avatar: 'https://i.pravatar.cc/40?img=1'
          },
          {
            id: 2,
            course: 'Программирование',
            name: 'Дмитрий Сиротин',
            text: 'Кто-нибудь может скинуть материалы?',
            date: '11.03.2025',
            avatar: 'https://i.pravatar.cc/40?img=2'
          }
        ]
      };
    },
    computed: {
      filteredMessages() {
        return this.messages.filter((msg) => {
          const byCourse =
            this.selectedCourse === 'Все чаты' || msg.course === this.selectedCourse;
          const bySearch = msg.text.toLowerCase().includes(this.searchQuery.toLowerCase());
          return byCourse && bySearch;
        });
      },
      chatCounts() {
        const counts = { 'Все чаты': this.messages.length };
        this.messages.forEach((msg) => {
          counts[msg.course] = (counts[msg.course] || 0) + 1;
        });
        return counts;
      }
    }
  };
  </script>
  
  <style scoped>
  .chats {
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
    transition: background 0.2s;
  }
  
  .sidebar li:hover {
    background-color: #f3f4f6;
  }
  
  .sidebar li.active {
    background: #eef2ff;
    color: #4f46e5;
    font-weight: bold;
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
  
  .messages-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .message {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: background 0.2s;
  }
  .message:hover {
    background: #f3f4f6;
  }
  
  .message-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
  }
  
  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
  }
  
  .name {
    font-weight: bold;
  }
  
  .date {
    font-size: 12px;
    color: #6b7280;
  }
  
  .message-text {
    font-size: 14px;
  }
  </style>
  