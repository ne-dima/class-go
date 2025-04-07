<template>
  <div class="register-page">
    <div class="form-wrapper">
      <h2>Создание аккаунта</h2>
      <p class="subtitle">Создайте аккаунт для продолжения</p>

      <form @submit.prevent="submitForm">
        <label>Email</label>
        <input type="email" v-model="form.email" required placeholder="ivan_ivanov@gmail.com" />

        <label>Фамилия</label>
        <input type="text" v-model="form.surname" required placeholder="Иванов" />

        <label>Имя</label>
        <input type="text" v-model="form.name" required placeholder="Иван" />

        <label>Отчество</label>
        <input type="text" v-model="form.patronymic" placeholder="Иванович" />

        <div class="role-group-wrapper">
          <div class="input-group">
            <label>Роль</label>
            <select v-model="form.role">
              <option value="Студент">Студент</option>
              <option value="Преподаватель">Преподаватель</option>
            </select>
          </div>

          <div class="input-group" v-if="form.role === 'Студент'">
            <label>Группа</label>
            <input type="text" v-model="form.group" placeholder="ИВТ-521" />
          </div>
        </div>

        <label>Институт</label>
        <input type="text" v-model="form.university" placeholder="Например: НИЯУ МИФИ" />

        <label>Пароль</label>
        <input :type="showPassword ? 'text' : 'password'" v-model="form.password" required />

        <div class="show-password">
          <label class="checkbox-label">
            <input type="checkbox" v-model="showPassword" />
            <span>Показать пароль</span>
          </label>
        </div>

        <button type="submit">Создать аккаунт</button>

        <p v-if="errorMessage" style="color: red; margin-top: 10px">{{ errorMessage }}</p>
        <p v-if="successMessage" style="color: green; margin-top: 10px">{{ successMessage }}</p>

        <p class="switch-link">
          Уже есть аккаунт?
          <router-link to="/login">Войти</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RegisterPage',
  data() {
    return {
      form: {
        email: '',
        surname: '',
        name: '',
        patronymic: '',
        role: 'Студент',
        group: '',
        university: '',
        password: ''
      },
      showPassword: false,
      errorMessage: '',
      successMessage: ''
    }
  },
  methods: {
    async submitForm() {
      this.errorMessage = ''
      this.successMessage = ''

      const roleMap = {
        'Студент': 'student',
        'Преподаватель': 'teacher'
      }

      console.log('Отправляем:', this.form)

      if (!this.form.surname || !this.form.name || !this.form.email || !this.form.password) {
        this.errorMessage = 'Заполните все обязательные поля'
        return
      }

      try {
        const response = await fetch('http://localhost:8000/register.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            surname: this.form.surname.trim(),
            name: this.form.name.trim(),
            patronymic: this.form.patronymic.trim(),
            email: this.form.email.trim(),
            password: this.form.password,
            role: roleMap[this.form.role],
            group: this.form.role === 'Студент' ? this.form.group.trim() : null,
            university: this.form.university.trim()
          })
        })

        const result = await response.json()

        if (response.ok) {
          this.successMessage = 'Аккаунт создан!'
          this.$root.$toast.success('✅ Аккаунт успешно создан!')
          setTimeout(() => this.$router.push('/login'), 1500)
        } else {
          const errorMsg = result.error || 'Ошибка регистрации'
          this.errorMessage = errorMsg
          this.$root.$toast.error(errorMsg)
        }
      } catch (error) {
        this.errorMessage = 'Ошибка соединения с сервером'
        console.error(error)
        this.$root.$toast.error(this.errorMessage)
      }
    }
  }
}
</script>

<style scoped>
html, body {
  margin: 0;
  padding: 0;
  overflow: hidden;
  height: 100%;
}

.register-page {
  min-height: 100vh;
  width: 100vw;
  background-color: #4880ff;
  background-image: url('@/assets/login-bg.svg');
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;

  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0;
  overflow: hidden;
}

.form-wrapper {
  background: white;
  border-radius: 16px;
  padding: 40px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

h2 {
  text-align: center;
  margin-bottom: 8px;
}

.subtitle {
  text-align: center;
  margin-bottom: 24px;
  color: #6b7280;
  font-size: 14px;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  margin-top: 10px;
  margin-bottom: 4px;
  font-weight: 500;
  font-size: 14px;
}

input,
select {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  width: 100%;
  box-sizing: border-box;
}

button {
  margin-top: 20px;
  background-color: #6366f1;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.2s;
}

button:hover {
  background-color: #4f46e5;
}

.show-password {
  margin-top: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #4b5563;
  cursor: pointer;
}

.checkbox-label input[type='checkbox'] {
  width: 16px;
  height: 16px;
  accent-color: #6366f1;
  cursor: pointer;
}

.switch-link {
  text-align: center;
  font-size: 14px;
  margin-top: 16px;
}

.switch-link a {
  color: #6366f1;
  font-weight: 500;
  text-decoration: none;
}

.role-group-wrapper {
  display: flex;
  gap: 12px;
  margin-bottom: 0;
  flex-wrap: wrap;
}

.input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}
</style>
