<template>
  <div class="login-wrapper">
    <div class="login-container">
      <h1 class="login-title">Вход в аккаунт</h1>
      <p class="login-subtitle">
        Пожалуйста, введите свой адрес<br />
        электронной почты и пароль, чтобы продолжить
      </p>

      <form @submit.prevent="login">
        <label>Email:</label>
        <input type="email" v-model="email" required placeholder="ivan_ivanov@gmail.com" />

        <div class="password-block">
          <label>Пароль</label>
          <a href="#" class="forgot">Забыли пароль?</a>
        </div>

        <div class="password-input">
          <input :type="showPassword ? 'text' : 'password'" v-model="password" required />
        </div>

        <label class="checkbox">
          <input type="checkbox" v-model="showPassword" />
          Показать пароль
        </label>

        <button type="submit" class="login-button" :disabled="loading">
          {{ loading ? 'Вход...' : 'Войти' }}
        </button>
      </form>

      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

      <p class="register-text">
        Нет аккаунта?
        <router-link to="/register">Создать аккаунт</router-link>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      showPassword: false,
      errorMessage: '',
      loading: false
    }
  },
  methods: {
    async login() {
      this.errorMessage = ''
      this.loading = true

      try {
        const response = await fetch('http://localhost:8000/login.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email: this.email,
            password: this.password
          })
        })

        const responseText = await response.text()
        const result = responseText ? JSON.parse(responseText) : {}

        if (!response.ok) {
          const errorMsg = (result && (result.error || result.message)) ||
                           'Ошибка входа. Пожалуйста, попробуйте снова'
          throw new Error(errorMsg)
        }

        if (!result.token) {
          throw new Error('Не удалось получить токен авторизации')
        }

        localStorage.setItem('token', result.token)
        this.$toast.success('Добро пожаловать! 🎉')

        setTimeout(() => {
          this.$router.push('/')
        }, 1500)
      } catch (error) {
        this.errorMessage = error.message
        this.$toast.error(this.errorMessage)
        console.error('Ошибка входа:', error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
}

.login-container {
  background: white;
  padding: 40px;
  border-radius: 16px;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.login-title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 10px;
}

.login-subtitle {
  font-size: 14px;
  color: #4b5563;
  margin-bottom: 30px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  text-align: left;
}

label {
  font-size: 14px;
  font-weight: 500;
  color: #1f2937;
}

input[type='email'],
input[type='password'],
input[type='text'] {
  width: 100%;
  padding: 10px 12px;
  font-size: 14px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-sizing: border-box;
}

.password-block {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot {
  font-size: 12px;
  color: #6b7280;
  text-decoration: none;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #4b5563;
  margin-top: -5px;
}

.login-button {
  background: #6366f1;
  color: white;
  padding: 10px;
  font-weight: bold;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin-top: 10px;
}

.login-button:hover {
  background: #4f46e5;
}

.login-button:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.register-text {
  margin-top: 20px;
  font-size: 14px;
  color: #6b7280;
}

.register-text a {
  color: #6366f1;
  font-weight: 500;
  text-decoration: underline;
}

.error-message {
  color: red;
  margin-top: 10px;
  font-size: 14px;
}
</style>
