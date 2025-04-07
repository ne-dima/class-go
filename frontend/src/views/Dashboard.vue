<template>
  <div class="dashboard">
    <h2>Добро пожаловать, {{ user.surname }} {{ user.name }}!</h2>

    <div v-if="loading" class="loading">Загрузка...</div>

    <div v-else>
      <div v-if="hasCourses" class="dashboard__stats">
        <div class="stat-card stat-blue">
          <div class="label">Общая оценка</div>
          <strong>{{ stats.grade !== null ? stats.grade + '%' : '—' }}</strong>
        </div>
        <div class="stat-card stat-green">
          <div class="label">Выполнено заданий</div>
          <strong>{{ hasStats ? stats.completed + '/' + stats.total : '—' }}</strong>
        </div>
        <div class="stat-card stat-orange">
          <div class="label">Ожидает выполнения</div>
          <strong>{{ hasStats && stats.pending > 0 ? stats.pending : '—' }}</strong>
        </div>
        <div class="stat-card stat-purple">
          <div class="label">Активные курсы</div>
          <strong>{{ stats.courses }}</strong>
        </div>
      </div>

      <div v-else class="empty-state">
        <p>Присоединитесь хотя бы к одному курсу, чтобы получить статистику 📊</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardPage',
  data() {
    return {
      stats: null,
      loading: true,
      user: {
        surname: '',
        name: '',
        patronymic: ''
      }
    }
  },
  computed: {
    hasCourses() {
      return this.stats && this.stats.courses > 0
    },
    hasStats() {
      return this.hasCourses && this.stats.total > 0
    }
  },
  async mounted() {
    const token = localStorage.getItem('token')

    try {
      const statsRes = await fetch('http://localhost:8000/dashboard_stats.php', {
        headers: { Authorization: 'Bearer ' + token }
      })
      this.stats = await statsRes.json()

      const userRes = await fetch('http://localhost:8000/user.php', {
        headers: { Authorization: 'Bearer ' + token }
      })
      const userData = await userRes.json()
      this.user = userData
    } catch (e) {
      console.error('Ошибка загрузки данных:', e)
    } finally {
      this.loading = false
    }
  }
}
</script>

<style scoped>

.dashboard h2 {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 24px;
}

.dashboard__stats {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-bottom: 30px;
}

.stat-card {
  flex: 1;
  min-width: 200px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  transition: transform 0.2s ease;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  border-left: 5px solid transparent;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.stat-blue {
  border-left-color: #3b82f6;
}
.stat-green {
  border-left-color: #22c55e;
}
.stat-orange {
  border-left-color: #f59e0b;
}
.stat-purple {
  border-left-color: #8b5cf6;
}

.stat-card .label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 6px;
}

.stat-card strong {
  font-size: 20px;
  color: #111827;
}

/* Красивая заглушка */
.empty-state {
  min-height: 250px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 500;
  color: #6b7280;
  text-align: center;
  background: #f9fafb;
  border-radius: 12px;
  padding: 40px;
}
</style>
