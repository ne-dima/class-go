<template>
  <div class="settings-page">
    <h2>Настройки аккаунта</h2>
    <div class="settings-card">
      <div class="avatar-row">
        <div class="avatar-wrapper">
          <img :src="avatarUrl" class="avatar" />
          <label class="edit-icon">
            <input type="file" hidden @change="uploadAvatar" />
            <i class="fas fa-pen"></i>
          </label>
        </div>
        <div class="name-inputs">
          <div class="input-group">
            <label>Имя</label>
            <input type="text" v-model="form.name" />
          </div>
          <div class="input-group">
            <label>Фамилия</label>
            <input type="text" v-model="form.surname" />
          </div>
          <div class="input-group">
            <label>Отчество</label>
            <input type="text" v-model="form.patronymic" />
          </div>
        </div>
      </div>

      <div class="settings-grid">
        <div class="input-group">
          <label>Email</label>
          <input type="email" v-model="form.email" disabled />
        </div>
        <div class="input-group">
          <label>Роль</label>
          <input type="text" :value="form.role === 'student' ? 'Студент' : 'Преподаватель'" disabled />
        </div>
        <div class="input-group">
          <label>Группа</label>
          <input type="text" v-model="form.group_name" />
        </div>
        <div class="input-group">
          <label>ВУЗ</label>
          <input type="text" v-model="form.university" />
        </div>
        <div class="input-group">
          <label>Профиль Вконтакте</label>
          <input type="text" v-model="form.vk" />
        </div>
        <div class="input-group">
          <label>Пароль</label>
          <button class="change-password-btn">Поменять пароль</button>
        </div>
      </div>

      <button class="save-btn" @click="saveSettings">Сохранить</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AccountSettings',
  data() {
    return {
      avatarUrl: '',
      form: {
        name: '',
        surname: '',
        patronymic: '',
        email: '',
        role: '',
        group_name: '',
        university: '',
        vk: ''
      }
    }
  },
  mounted() {
    this.loadUserData()
  },
  methods: {
    async loadUserData() {
      const res = await fetch('http://localhost:8000/user.php', {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token')
        }
      })
      if (res.ok) {
        const data = await res.json()
        this.form.name = data.name || ''
        this.form.surname = data.surname || ''
        this.form.patronymic = data.patronymic || ''
        this.form.email = data.email || ''
        this.form.role = data.role || ''
        this.form.group_name = data.group_name || ''
        this.form.university = data.university || ''
        this.form.vk = data.vk || ''
        this.avatarUrl = data.avatar_url
      } else {
        const err = await res.json()
        console.error('Ошибка загрузки данных пользователя:', err)
      }
    },
    async saveSettings() {
      const payload = {
        name: this.form.name,
        surname: this.form.surname,
        patronymic: this.form.patronymic,
        group_name: this.form.group_name,
        university: this.form.university,
        vk: this.form.vk
      }

      const res = await fetch('http://localhost:8000/update_user.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        body: JSON.stringify(payload)
      })

      if (res.ok) {
        alert('✅ Данные успешно сохранены')
      } else {
        const err = await res.json()
        console.error('❌ Ошибка при сохранении:', err)
        alert('Ошибка при сохранении')
      }
    },
    uploadAvatar(e) {
      const file = e.target.files[0]
      if (!file) return

      const reader = new FileReader()
      reader.onload = async () => {
        const base64 = reader.result
        const res = await fetch('http://localhost:8000/avatar.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + localStorage.getItem('token')
          },
          body: JSON.stringify({ avatar: base64 })
        })
        const result = await res.json()
        if (res.ok && result.avatar_url) {
          this.avatarUrl = result.avatar_url
        }
      }
      reader.readAsDataURL(file)
    }
  }
}
</script>

<style scoped>
.settings-page {
  padding: 30px;
}

.settings-page h2 {
  margin-bottom: 20px;
}

.settings-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.avatar-row {
  display: grid;
  grid-template-columns: 200px 1fr;
  align-items: center;
  gap: 40px;
  margin-bottom: 30px;
}

.avatar-wrapper {
  width: 150px;
  height: 150px;
  margin: 0 auto;
  position: relative;
}

.avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.edit-icon {
  position: absolute;
  bottom: 0;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 50%;
  padding: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.edit-icon:hover {
  background: #f3f4f6;
}

.name-inputs {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.settings-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px 40px;
}

.input-group {
  display: flex;
  flex-direction: column;
}

.input-group label {
  font-size: 14px;
  margin-bottom: 6px;
}

.input-group input {
  padding: 10px 14px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.input-group input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  outline: none;
}

.change-password-btn {
  padding: 10px 14px;
  font-size: 14px;
  background: white;
  color: #6366f1;
  border: 1px solid #c7d2fe;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.change-password-btn:hover {
  background: #eef2ff;
  color: #4f46e5;
}

.save-btn {
  margin-top: 30px;
  background-color: #6366f1;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  display: block;
  margin-left: auto;
  margin-right: auto;
  transition: background-color 0.3s;
}

.save-btn:hover {
  background-color: #4f46e5;
}


</style>
