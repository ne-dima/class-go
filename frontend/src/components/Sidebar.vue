<template>
  <div :class="['sidebar', { expanded }]" @click.self="$emit('toggle')">
    <div class="sidebar__inner">
      <div class="sidebar__top">
        <div class="sidebar__logo">
          🎓
          <transition name="fade-slide">
            <span v-if="expanded" class="logo-text">ClassGo</span>
          </transition>
        </div>

        <nav class="sidebar__nav">
          <router-link
            v-for="item in menuItems"
            :key="item.label"
            :to="item.route"
            class="sidebar__item"
            active-class="sidebar__item--active"
          >
            <i :class="item.icon" class="sidebar__icon"></i>
            <transition name="fade-slide">
              <span v-if="expanded" class="sidebar__text">{{ item.label }}</span>
            </transition>
          </router-link>
        </nav>
      </div>

      <div class="sidebar__bottom">
        <transition name="fade-slide">
          <div v-if="expanded" class="sidebar__user">
            <div class="sidebar__user-name">{{ user.name }}</div>
            <div class="sidebar__user-role">{{ user.role }}</div>
          </div>
        </transition>

        <router-link to="/settings" class="sidebar__item">
          <i class="fas fa-cog sidebar__icon"></i>
          <transition name="fade-slide">
            <span v-if="expanded" class="sidebar__text">Настройки</span>
          </transition>
        </router-link>

        <router-link to="/logout" class="sidebar__item">
          <i class="fas fa-sign-out-alt sidebar__icon"></i>
          <transition name="fade-slide">
            <span v-if="expanded" class="sidebar__text">Выйти</span>
          </transition>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppSidebar',
  props: {
    expanded: Boolean
  },
  data() {
    return {
      menuItems: [
        { label: 'Успеваемость', icon: 'fas fa-chart-line', route: '/' },
        { label: 'Курсы', icon: 'fas fa-book', route: '/courses' },
        { label: 'Задания', icon: 'fas fa-clipboard-check', route: '/assignments' },
        { label: 'Календарь', icon: 'fas fa-calendar-alt', route: '/calendar' },
        { label: 'Чаты', icon: 'fas fa-comments', route: '/chats' },
        { label: 'Контакты', icon: 'fas fa-address-book', route: '/contacts' }
      ],
      user: {
        name: '',
        role: ''
      }
    }
  },
  mounted() {
    this.fetchUser()
  },
  methods: {
    async fetchUser() {
      const res = await fetch('http://localhost:8000/user.php', {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token')
        }
      })
      if (res.ok) {
        const data = await res.json()
        this.user.name = data.name
        this.user.role = data.role === 'student' ? 'Студент' : 'Преподаватель'
      }
    }
  }
}
</script>

<style scoped>
.sidebar {
  background: #fff;
  border-right: 1px solid #e5e7eb;
  height: 100vh;
  width: 70px;
  transition: width 0.3s ease;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  display: flex;
  flex-direction: column;
}

.sidebar.expanded {
  width: 220px;
}

.sidebar__inner {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
}

.sidebar__logo {
  font-size: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px;
  height: 64px;
  box-sizing: border-box;
}

.logo-text {
  font-size: 18px;
  font-weight: bold;
  white-space: nowrap;
}

.sidebar__nav,
.sidebar__bottom {
  display: flex;
  flex-direction: column;
}

.sidebar__item {
  display: flex;
  align-items: flex-start;
  padding: 12px 20px;
  color: #4b5563;
  font-size: 15px;
  text-decoration: none;
  transition: background 0.2s;
  height: 48px;
  box-sizing: border-box;
}

.sidebar__item:hover {
  background-color: #f3f4f6;
}

.sidebar__item--active {
  background-color: #eef2ff;
  color: #6366f1;
}

.sidebar__icon {
  width: 20px;
  min-width: 20px;
  text-align: center;
  font-size: 16px;
  line-height: 24px;
  margin-top: 2px;
}

.sidebar__text {
  margin-left: 14px;
  white-space: nowrap;
  line-height: 24px;
  height: 24px;
}

.sidebar__toggle {
  background: #f3f4f6;
  border: none;
  font-size: 16px;
  color: #4b5563;
  cursor: pointer;
  width: 100%;
  padding: 12px 20px;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  justify-content: flex-start;
  border-top: 1px solid #e5e7eb;
  height: 48px;
}

.sidebar__toggle:hover {
  background-color: #e0e7ff;
}

.sidebar__user {
  padding: 16px 20px;
  border-top: 1px solid #f3f4f6;
  font-size: 14px;
}

.sidebar__user-name {
  font-weight: 600;
  color: #1f2937;
}

.sidebar__user-role {
  color: #6b7280;
  font-size: 13px;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}
</style>
