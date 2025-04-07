
<template>
  <div class="courses-page">
    <div class="courses-header">
      <h1 class="page-title">Все курсы</h1>
      <button v-if="user.role === 'teacher'" class="create-btn" @click="showModal = true">
        + Создать курс
      </button>
    </div>

    <div v-if="courses.length > 0" class="courses-grid">
      <router-link
        v-for="course in courses"
        :key="course.id"
        :to="`/courses/${course.id}`"
        class="course-card"
      >
      <div class="course-card__header" :style="{ backgroundColor: course.color || '#ccc' }">
  <h2 class="course-title">{{ course.title || 'Без названия' }}</h2>
</div>
<div class="course-card__info">
  <p class="course-group"><strong>Группа: {{ course.group_name || '—' }}</strong></p>
  <p class="course-teacher">{{ course.teacher || 'Преподаватель не указан' }}</p>
  <div class="progress-text">
    Прогресс: {{ course.progress !== null ? course.progress + '%' : 'недоступен' }}
  </div>
</div>



      </router-link>
    </div>

    <div v-else class="no-courses">
      <p>📭 Курсы пока не найдены.</p>
      <p v-if="user.role === 'teacher'">Нажмите «Создать курс», чтобы начать.</p>
      <p v-else>Попросите преподавателя добавить вас в курс.</p>
    </div>

    <!-- Модалка создания курса -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h2>Создание курса</h2>
        <div class="form-group">
          <label>Название курса *</label>
          <input type="text" v-model="form.name" />
        </div>
        <div class="form-group">
          <label>Группа</label>
          <input type="text" v-model="form.group" />
        </div>
        <div class="form-group">
          <label>Описание</label>
          <textarea v-model="form.description"></textarea>
        </div>
        <div class="form-group">
          <label>Полезные ссылки</label>
          <input type="text" v-model="form.links" placeholder="через запятую" />
        </div>
        <div class="modal-actions">
          <button @click="closeModal">Отмена</button>
          <button class="primary" @click="createCourse">Создать</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Courses',
  data() {
    return {
      courses: [],
      user: {
        role: ''
      },
      showModal: false,
      form: {
        name: '',
        group: '',
        description: '',
        links: ''
      }
    }
  },
  async mounted() {
    const token = localStorage.getItem('token')

    const resUser = await fetch('http://localhost:8000/user.php', {
      headers: { Authorization: 'Bearer ' + token }
    })
    if (resUser.ok) {
      const data = await resUser.json()
      this.user.role = data.role
    }

    await this.fetchCourses()
  },
  methods: {
    closeModal() {
      this.showModal = false
    },
    async fetchCourses() {
      const token = localStorage.getItem('token')
      try {
        const res = await fetch('http://localhost:8000/get_courses.php', {
          headers: { Authorization: 'Bearer ' + token }
        })
        if (res.ok) {
          const data = await res.json()
          this.courses = data.courses || []
        } else {
          alert('Не удалось загрузить курсы.')
        }
      } catch (e) {
        alert('Ошибка соединения при загрузке курсов.')
        console.error(e)
      }
    },
    async createCourse() {
      const token = localStorage.getItem('token')
      if (!this.form.name.trim()) {
        alert('Введите название курса')
        return
      }

      const payload = {
        title: this.form.name,
        group: this.form.group,
        description: this.form.description,
        resources: this.form.links
      }

      try {
        const res = await fetch('http://localhost:8000/create_course.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + token
          },
          body: JSON.stringify(payload)
        })

        const result = await res.json()

        if (res.ok && result.success) {
          alert('✅ Курс успешно создан!')
          this.showModal = false
          this.form = { name: '', group: '', description: '', links: '' }
          await this.fetchCourses()
        } else {
          alert('❌ Ошибка: ' + (result.error || 'Неизвестная ошибка'))
        }
      } catch (e) {
        alert('❌ Ошибка соединения: ' + e.message)
        console.error(e)
      }
    }
  }
}
</script>

<style scoped>
.courses-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.create-btn {
  background-color: #6366f1;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}
.create-btn:hover {
  background-color: #4f46e5;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal {
  background: white;
  padding: 30px;
  border-radius: 12px;
  width: 400px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.modal h2 {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 16px;
}
.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 6px;
}
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}

.modal-actions button {
  padding: 10px 14px;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
}
.modal-actions .primary {
  background-color: #6366f1;
  color: white;
}

.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.course-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
  text-decoration: none;
  color: inherit;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.course-card__header {
  padding: 20px;
  color: white;
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
}

.course-card__header h2 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 600;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.course-card__info {
  padding: 20px;
}

.course-card__info strong {
  display: block;
  font-size: 1.1rem;
  margin-bottom: 8px;
  color: #1f2937;
}

.course-card__info p {
  margin: 8px 0;
  color: #6b7280;
  font-size: 0.9rem;
}

.progress-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  margin: 15px 0 8px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #6366f1;
  border-radius: 4px;
  transition: width 0.5s ease;
}

.progress-text {
  text-align: right;
  font-size: 0.85rem;
  color: #6b7280;
}

.no-courses {
  background: #f9fafb;
  padding: 40px;
  border-radius: 12px;
  text-align: center;
  font-size: 1.1rem;
  color: #4b5563;
  margin-top: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.no-courses p {
  margin: 10px 0;
}

.no-courses p:first-child {
  font-size: 2rem;
  color: #9ca3af;
}

.course-title {
  font-size: 1.3rem;
  font-weight: 700;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.course-group {
  font-size: 0.95rem;
  color: #1f2937;
  margin: 6px 0 0;
}

.course-teacher {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 10px;
}


</style>
