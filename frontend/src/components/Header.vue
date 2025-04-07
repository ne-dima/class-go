<template>
  <header class="header">
    <div class="header-left">
      <button class="toggle-btn" @click="$emit('toggleSidebar')">
        <i class="fas fa-bars"></i>
      </button>
      <input class="search" placeholder="Поиск курсов, заданий и материалов..." />
    </div>

    <div class="header-right">
      <button class="join-btn" @click="showModal = true">+ Присоединиться к курсу</button>

      <div class="notif-dropdown" ref="notif" @click="toggleNotifDropdown">
        <div class="notif-icon">
          <i class="fas fa-bell"></i>
          <span v-if="hasNotifications" class="notif-indicator"></span>
        </div>
        <div class="dropdown" v-if="notifOpen">
          <div class="notif-item" v-for="(notif, i) in notifications" :key="i">{{ notif }}</div>
        </div>
      </div>

      <div class="profile-dropdown" ref="dropdown" @click="toggleDropdown">
        <img class="avatar" :src="user.avatar_url" alt="Avatar" />
        <span class="usthis.showModal = falseername">{{ fullName }}</span>
        <div class="dropdown" v-if="dropdownOpen">
          <button @click="goToSettings">⚙ Настройки</button>
          <button @click="goToProfile">👤 Профиль</button>
          <button @click="logout">🚪 Выйти</button>
        </div>
      </div>
    </div>


  </header>

      <!-- Модалка присоединения -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h2>Присоединение к курсу</h2>
        <p>Введите 6-значный код курса</p>
        <input
          v-model="courseCode"
          type="text"
          maxlength="6"
          placeholder="Код курса"
          class="modal-input"
        />
        <div class="modal-actions">
          <button class="btn" @click="closeModal">Назад</button>
          <button class="btn primary" @click="joinCourse">Присоединиться</button>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: 'Header',
  data() {
    return {
      showModal: false,
      courseCode: '',
      dropdownOpen: false,
      notifOpen: false,
      notifications: [
        "Новое задание по математике",
        "Приглашение в курс «Физика 2»",
        "Оценка за проект обновлена"
      ],
      user: {
        name: '',
        surname: '',
        patronymic: '',
        avatar_url: ''
      }
    }
  },
  computed: {
    fullName() {
      return `${this.user.surname} ${this.user.name} ${this.user.patronymic}`.trim()
    },
    hasNotifications() {
      return this.notifications.length > 0
    }
  },
  mounted() {
    this.fetchUser()
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    async fetchUser() {
      const token = localStorage.getItem('token')
      if (!token) return

      try {
        const res = await fetch('http://localhost:8000/user.php', {
          headers: { Authorization: 'Bearer ' + token }
        })
        if (res.ok) {
          const data = await res.json()
          this.user.name = data.name || ''
          this.user.surname = data.surname || ''
          this.user.patronymic = data.patronymic || ''
          this.user.avatar_url = data.avatar_url || ''
        }
      } catch (e) {
        console.error('Ошибка загрузки пользователя:', e)
      }
    },
    async joinCourse() {
  const token = localStorage.getItem('token')
  if (!token || !this.courseCode.trim()) return

  try {
    const res = await fetch('http://localhost:8000/join_course.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: 'Bearer ' + token
      },
      body: JSON.stringify({ code: this.courseCode.trim() })
    })

    const data = await res.json()

    if (!res.ok) {
      this.$toast.error(data.message || 'Ошибка при присоединении')
      return
    }

    if (data.alreadyJoined) {
      this.$toast('Вы уже состоите в этом курсе', { type: 'info' })
    } else {
      this.$toast.success('Вы успешно присоединились к курсу!')

      // Закрываем модалку и очищаем поле
      this.showModal = false
      this.courseCode = ''

      // Автообновление курса
      setTimeout(() => {
        window.location.reload()
      }, 100)
    }
  } catch (err) {
    console.error('Ошибка запроса:', err)
    this.$toast.error('Ошибка соединения с сервером')
  }
}
,
    closeModal() {
      this.showModal = false
      this.courseCode = ''
    },
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen
    },
    toggleNotifDropdown() {
      this.notifOpen = !this.notifOpen
    },
    handleClickOutside(event) {
      if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
        this.dropdownOpen = false
      }
      if (this.$refs.notif && !this.$refs.notif.contains(event.target)) {
        this.notifOpen = false
      }
    },
    goToSettings() {
      this.$router.push('/settings')
    },
    goToProfile() {
      this.$router.push('/profile')
    },
    logout() {
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 10;
  background: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 64px;
  padding: 0 24px;
  border-bottom: 1px solid #e5e7eb;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.search {
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 8px 12px;
  width: 260px;
}

.toggle-btn {
  background: none;
  border: none;
  font-size: 20px;
  color: #374151;
  cursor: pointer;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.join-btn {
  background: #6366f1;
  color: white;
  padding: 8px 14px;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

.join-btn:hover {
  background: #4f46e5;
}

.notif-dropdown {
  position: relative;
  cursor: pointer;
}

.notif-icon {
  position: relative;
  font-size: 20px;
  color: #374151;
}

.notif-indicator {
  position: absolute;
  top: -4px;
  right: -4px;
  width: 10px;
  height: 10px;
  background-color: red;
  border-radius: 50%;
  border: 2px solid white;
}

.notif-dropdown .dropdown {
  position: absolute;
  right: 0;
  top: 40px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
  min-width: 240px;
  z-index: 100;
  max-height: 300px;
  overflow-y: auto;
}

.notif-item {
  padding: 10px 16px;
  font-size: 14px;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
}

.notif-item:last-child {
  border-bottom: none;
}

.profile-dropdown {
  position: relative;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  user-select: none;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #6366f1;
}

.username {
  font-weight: 500;
  color: #374151;
}

.profile-dropdown .dropdown {
  position: absolute;
  right: 0;
  top: 48px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  min-width: 160px;
  z-index: 100;
}

.dropdown button {
  padding: 10px 16px;
  background: none;
  border: none;
  text-align: left;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.dropdown button:hover {
  background-color: #f3f4f6;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(2px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal {
  background: white;
  padding: 30px;
  border-radius: 12px;
  width: 400px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  animation: fadein 0.3s ease;
}

.modal h2 {
  font-size: 20px;
  margin-bottom: 10px;
}

.modal p {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 15px;
}

.modal-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 16px;
}

.modal-actions {
  display: flex;
  justify-content: space-between;
}

.btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 500;
  background-color: #f3f4f6;
  color: #374151;
  transition: background 0.3s;
}

.btn:hover {
  background-color: #e5e7eb;
}

.btn.primary {
  background-color: #6366f1;
  color: white;
}

.btn.primary:hover {
  background-color: #4f46e5;
}

@keyframes fadein {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
