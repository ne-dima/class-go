<template>
  <div class="course-detail">
    <router-link to="/courses" class="back-button">← Назад</router-link>

    <div v-if="loading" class="loading">Загрузка данных курса...</div>
    <div v-else-if="error" class="error">
      Ошибка загрузки данных: {{ error }}
      <button @click="fetchCourseData">Повторить попытку</button>
    </div>

    <template v-else>
      <div class="course-header">
        <div class="course-info">
          <p class="course-group" v-if="course.group_name">Группа: <strong>{{ course.group_name }}</strong></p>
          <h1 class="course-title">{{ course.title }}</h1>

          <div class="course-code" v-if="courseCode">
            <span class="code-label">Код курса:</span>
            <span class="code-value">{{ courseCode }}</span>
            <button @click="copyCode" class="copy-btn" title="Скопировать">
              <i class="fas fa-clone"></i>
            </button>
          </div>

          <p class="course-teacher" v-if="teacherName">
            <strong>{{ teacherName }}</strong>
          </p>
        </div>

        <div class="course-actions">
          <button v-if="userRole === 'student'" class="leave-course-btn" @click="leaveCourse">
            <i class="fas fa-sign-out-alt"></i> Покинуть курс
          </button>

            <button v-if="userRole === 'teacher'" class="delete-course-btn" @click="deleteCourse">
              <i class="fas fa-trash"></i> Удалить курс
            </button>

          <button class="course-tasks-btn">
            <i class="fas fa-tasks"></i> Задания
          </button>
        </div>

      </div>

      <div class="course-body">
        <div class="student-list" v-if="students.length">
          <h3>Список учащихся</h3>
          <div
            v-for="(student, i) in students"
            :key="i"
            class="student"
            :class="{ 'teacher-divider': student.role === 'Преподаватель' }"
          >
            <img :src="student.avatar || defaultAvatar" class="avatar" />
            <div class="info">
              <div class="name">{{ student.name }}</div>
              <div class="role">{{ student.role }}</div>
            </div>
          </div>
        </div>

        <div class="course-messages">
          <h3 class="message-heading">Сообщения курса</h3>

          <div class="message" v-for="(message, i) in messages" :key="i">
            <div class="message-header">
              <img :src="message.avatar || defaultAvatar" class="avatar" />
              <div>
                <div class="name">{{ message.name }}</div>
                <div class="date">{{ message.date }}</div>
              </div>
              <button @click="deleteMessage(message.id)" class="delete-btn" title="Удалить" v-if="userRole === 'teacher'">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
            <div class="message-text">{{ message.text }}</div>
          </div>

          <div class="message-input-wrapper" v-if="userRole === 'teacher'">
            <h4 class="input-heading">Новое сообщение</h4>
            <div class="message-input-card">
              <textarea v-model="newMessage" placeholder="Введите сообщение..." rows="3" />
              <button @click="sendMessage" :disabled="!newMessage.trim()">
                <i class="fas fa-paper-plane"></i> Отправить
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Модальное окно подтверждения -->
    <div v-if="showConfirmModal" class="confirm-overlay" @click.self="showConfirmModal = false">
      <div class="confirm-modal">
        <h3>{{ modalType === 'leave' ? 'Покинуть курс?' : 'Удалить курс?' }}</h3>
        <p>
          {{ modalType === 'leave'
            ? 'Вы действительно хотите покинуть этот курс?'
            : 'Курс и все его данные будут удалены безвозвратно.' }}
        </p>
        <div class="modal-actions">
          <button class="btn" @click="showConfirmModal = false">Отмена</button>
          <button class="btn danger" @click="confirmAction">Подтвердить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const defaultAvatar = 'https://via.placeholder.com/40';

export default {
  name: 'CourseDetail',
  data() {
    return {
      course: {},
      students: [],
      messages: [],
      courseCode: '',
      newMessage: '',
      loading: true,
      error: null,
      userRole: '',
      showConfirmModal: false,
      modalType: '',
      defaultAvatar
    };
  },
  computed: {
    teacherName() {
      const { surname, name, patronymic } = this.course;
      return `${surname || ''} ${name || ''} ${patronymic || ''}`.trim();
    }
  },
  methods: {
    async fetchCourseData() {
      this.loading = true;
      this.error = null;
      const token = localStorage.getItem('token');
      const courseId = this.$route.params.id;

      try {
        const res = await fetch(`http://localhost:8000/get_course.php?id=${courseId}`, {
          headers: { Authorization: 'Bearer ' + token }
        });

        if (!res.ok) throw new Error('Ошибка сервера: ' + res.status);
        const data = await res.json();

        this.course = data.course || {};
        this.courseCode = data.course?.code || '';

        this.students = (data.students || [])
          .map(s => ({
            name: `${s.surname || ''} ${s.name || ''}`.trim(),
            role: s.role === 'teacher' ? 'Преподаватель' : 'Студент',
            avatar: s.avatar_url || defaultAvatar
          }))
          .sort((a, b) => {
            if (a.role === 'Преподаватель') return -1;
            if (b.role === 'Преподаватель') return 1;
            return a.name.localeCompare(b.name);
          });

        this.messages = (data.messages || []).map(m => ({
          id: m.id,
          name: `${m.surname || ''} ${m.name || ''}`.trim(),
          avatar: m.avatar_url || defaultAvatar,
          text: m.text || '',
          date: m.created_at ? new Date(m.created_at).toLocaleString() : ''
        }));
      } catch (err) {
        console.error('Ошибка при загрузке курса:', err);
        this.error = err.message;
        this.$toast.error('Не удалось загрузить данные курса');
      } finally {
        this.loading = false;
      }
    },

    async fetchUserRole() {
      const token = localStorage.getItem('token');
      try {
        const res = await fetch('http://localhost:8000/user.php', {
          headers: { Authorization: 'Bearer ' + token }
        });
        if (res.ok) {
          const data = await res.json();
          this.userRole = data.role;
        }
      } catch (err) {
        console.error('Ошибка получения роли:', err);
      }
    },

    async sendMessage() {
      if (this.userRole !== 'teacher') {
        this.$toast.error('Студенты не могут отправлять сообщения');
        return;
      }

      if (!this.newMessage.trim()) return;
      const token = localStorage.getItem('token');
      const courseId = this.$route.params.id;

      try {
        const res = await fetch('http://localhost:8000/send_message.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + token
          },
          body: JSON.stringify({
            course_id: courseId,
            text: this.newMessage.trim()
          })
        });

        if (res.ok) {
          this.newMessage = '';
          await this.fetchCourseData();
          this.$toast.success('Сообщение отправлено!');
        } else {
          throw new Error('Ошибка сервера');
        }
      } catch (err) {
        console.error('Ошибка при отправке сообщения:', err);
        this.$toast.error('Не удалось отправить сообщение');
      }
    },

    async deleteMessage(messageId) {
      const token = localStorage.getItem('token');
      try {
        const res = await fetch('http://localhost:8000/delete_message.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + token
          },
          body: JSON.stringify({ message_id: messageId })
        });

        if (res.ok) {
          this.$toast.success('Сообщение удалено!');
          await this.fetchCourseData();
        } else {
          throw new Error('Ошибка при удалении');
        }
      } catch (err) {
        console.error('Ошибка удаления:', err);
        this.$toast.error('Не удалось удалить сообщение');
      }
    },

    leaveCourse() {
      this.modalType = 'leave';
      this.showConfirmModal = true;
    },

    deleteCourse() {
      this.modalType = 'delete';
      this.showConfirmModal = true;
    },

    async confirmAction() {
      const token = localStorage.getItem('token');
      const courseId = this.$route.params.id;
      this.showConfirmModal = false;

      try {
        if (this.modalType === 'leave') {
          const res = await fetch('http://localhost:8000/leave_course.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: 'Bearer ' + token
            },
            body: JSON.stringify({ course_id: courseId })
          });

          if (res.ok) {
            this.$toast.success('Вы покинули курс');
            this.$router.push('/courses');
          } else {
            throw new Error('Ошибка при выходе');
          }
        } else if (this.modalType === 'delete') {
          const res = await fetch(`http://localhost:8000/delete_course.php?id=${courseId}`, {
            method: 'DELETE',
            headers: {
              Authorization: 'Bearer ' + token
            }
          });

          if (res.ok) {
            this.$toast.success('Курс удалён');
            this.$router.push('/courses');
          } else {
            throw new Error('Ошибка при удалении');
          }
        }
      } catch (err) {
        console.error(err);
        this.$toast.error('Произошла ошибка при выполнении действия');
      }
    },

    copyCode() {
      if (!this.courseCode) return;
      navigator.clipboard.writeText(this.courseCode);
      this.$toast.success('Код скопирован!');
    }
  },
  mounted() {
    this.fetchCourseData();
    this.fetchUserRole();
  }
};
</script>


<style scoped>

h3 {
  margin-top: 0;
}

.back-button {
  display: inline-block;
  margin-bottom: 20px;
  color: #6366f1;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s;
}
.back-button:hover {
  color: #4338ca;
}

.course-header {
  background: #6366f1;
  color: white;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  position: relative;
  overflow: hidden;
}

.course-header::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image: url('@/assets/pattern.svg'); 
  background-repeat: repeat;
  background-size: auto;
  opacity: 1; 
  z-index: 0;
}


.course-header > * {
  position: relative;
  z-index: 1;
}

.course-info {
  line-height: 1.6;
  flex: 1;
  padding-right: 80px;
}

.course-title {
  font-size: 28px;
  font-weight: bold;
  margin: 10px 0;
}

.course-teacher {
  font-size: 15px;
  margin-top: 10px;
  padding-top: 10px;
}

.course-code {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 16px;
  margin-top: 8px;
}

.code-label {
  font-weight: 500;
}
.code-value {
  background: rgba(255, 255, 255, 0.2);
  padding: 4px 10px;
  border-radius: 6px;
  font-family: monospace;
}
.copy-btn {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
}

.course-body {
  display: flex;
  align-items: flex-start;
  gap: 30px;
}

.student-list {
  width: 240px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  flex-shrink: 0;
  align-self: flex-start; 
}

.student {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-top: 12px;
}
.teacher-divider {
  padding-top: 12px;
  margin-top: 16px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.name {
  font-weight: 600;
}
.role {
  font-size: 12px;
  color: #6b7280;
}

.course-messages {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.message {
  background: #f9fafb;
  padding: 16px;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.message-header {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 10px;
}
.date {
  font-size: 12px;
  color: #6b7280;
}
.message-input {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.message-input textarea {
  resize: none;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #d1d5db;
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
}

.message-input button {
  align-self: flex-end;
  background-color: #6366f1;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.message-input button:hover {
  background-color: #4f46e5;
}

.message-input button:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.loading, .error {
  padding: 20px;
  text-align: center;
  font-size: 1.1rem;
  color: #6b7280;
}

.error {
  color: #ef4444;
}

.error button {
  margin-top: 10px;
  padding: 8px 16px;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.avatar {
  background-color: #f3f4f6;
}

@media (max-width: 768px) {
  .course-body {
    flex-direction: column;
  }
  
  .student-list {
    width: auto;
    margin-bottom: 20px;
  }
  
  .course-header {
    flex-direction: column;
    padding-bottom: 70px;
  }
  
  .course-info {
    padding-right: 0;
  }

}

.delete-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  font-size: 16px;
  cursor: pointer;
  margin-left: auto;
  transition: color 0.2s;
}
.delete-btn:hover {
  color: #ef4444;
}

.message-heading {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
  margin-top: 5px;
  color: #374151;
}

.input-heading {
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 10px;
  color: #374151;
}

.message-input-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.message-input-card textarea {
  resize: none;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #d1d5db;
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
  font-family: inherit;
}

.message-input-card button {
  align-self: flex-end;
  background-color: #6366f1;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.3s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.message-input-card button:hover {
  background-color: #4f46e5;
}

.message-input-card button:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.leave-course-btn,
.delete-course-btn {
  background: white;
  color: #ef4444;
  border: 1px solid #ef4444;
  border-radius: 8px;
  padding: 8px 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.3s;
}


.leave-course-btn:hover,
.delete-course-btn:hover {
  background: #ef4444;
  color: white;
}

.confirm-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;
}

.confirm-modal {
  background: white;
  padding: 24px;
  border-radius: 12px;
  width: 400px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.confirm-modal h3 {
  font-size: 20px;
  margin-bottom: 10px;
}

.confirm-modal p {
  color: #4b5563;
  font-size: 14px;
  margin-bottom: 20px;
}

.modal-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-top: 20px;
}


.btn {
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  background: #f3f4f6;
  color: #374151;
  transition: background 0.3s;
}

.btn:hover {
  background: #e5e7eb;
}

.btn.danger {
  background: #ef4444;
  color: white;
}

.btn.danger:hover {
  background: #dc2626;
}

.course-actions {
  position: absolute;
  bottom: 20px;
  right: 20px;
  display: flex;
  gap: 10px;
  align-items: center;
}

.course-actions .delete-course-btn,
.course-actions .leave-course-btn {
  background: white;
  color: #ef4444;
  border: 1px solid #ef4444;
  border-radius: 8px;
  padding: 8px 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.3s;
}

.course-actions .delete-course-btn:hover,
.course-actions .leave-course-btn:hover {
  background: #ef4444;
  color: white;
}

.course-actions .course-tasks-btn {
  background: white;
  color: #4b5563;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.3s;
}
.course-actions .course-tasks-btn:hover {
  background: #f3f4f6;
}

</style>