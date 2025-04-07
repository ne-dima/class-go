<template>
    <div class="contacts">
      <h1 class="contacts__title">Контакты преподавателей</h1>
  
      <div class="contacts__grid">
        <div
          class="contact-card"
          v-for="teacher in teachers"
          :key="teacher.email"
        >
          <img :src="teacher.avatar" class="contact-card__avatar" />
  
          <div class="contact-card__info">
            <h3 class="contact-card__name">{{ teacher.name }}</h3>
            <p class="contact-card__subject">{{ teacher.subject }}</p>
            <p class="contact-card__email">
              <i class="fas fa-envelope"></i> {{ teacher.email }}
            </p>
            <p class="contact-card__phone">
              <i class="fas fa-phone"></i> {{ teacher.phone }}
            </p>
          </div>
  
          <router-link
            class="contact-card__chat-btn"
            :to="'/chats'"
          >
            <i class="fas fa-comments"></i> Перейти в чат
          </router-link>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ContactsPage',
    data() {
      return {
        teachers: [
          {
            name: 'Иванов Иван Иванович',
            subject: 'Математика',
            email: 'ivanov@example.com',
            phone: '+7 (900) 111-22-33',
            avatar: 'https://i.pravatar.cc/100?img=10'
          },
          {
            name: 'Петров Пётр Петрович',
            subject: 'Физика',
            email: 'petrov@example.com',
            phone: '+7 (901) 222-33-44',
            avatar: 'https://i.pravatar.cc/100?img=11'
          },
          {
            name: 'Сидорова Анна Николаевна',
            subject: 'Программирование',
            email: 'sidorova@example.com',
            phone: '+7 (902) 333-44-55',
            avatar: 'https://i.pravatar.cc/100?img=12'
          }
        ]
      }
    }
  }
  </script>
  
  <style scoped>
  
  .contacts__title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
  }
  
  .contacts__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }
  
  .contact-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: box-shadow 0.2s;
  }
  
  .contact-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
  }
  
  .contact-card__avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 16px;
  }
  
  .contact-card__info {
    text-align: center;
    margin-bottom: 16px;
  }
  
  .contact-card__name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 4px;
  }
  
  .contact-card__subject {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
  }
  
  .contact-card__email,
  .contact-card__phone {
    font-size: 14px;
    color: #374151;
    margin: 2px 0;
  }
  
  .contact-card__email i,
  .contact-card__phone i {
    margin-right: 6px;
  }
  
  .contact-card__chat-btn {
    margin-top: auto;
    background: #6366f1;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s;
  }
  
  .contact-card__chat-btn:hover {
    background: #4f46e5;
  }
  </style>
  