<template>
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal">
        <h2>Создание курса</h2>
        <form @submit.prevent="createCourse">
          <label>Название курса*</label>
          <input type="text" v-model="form.title" required />
  
          <label>Группа*</label>
          <input type="text" v-model="form.group" required />
  
          <label>Описание</label>
          <textarea v-model="form.description"></textarea>
  
          <label>Полезные ссылки (через запятую)</label>
          <input type="text" v-model="form.resources" />
  
          <div class="actions">
            <button type="submit">Создать</button>
            <button type="button" @click="$emit('close')">Отмена</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'CreateCourseModal',
    data() {
      return {
        form: {
          title: '',
          group: '',
          description: '',
          resources: ''
        }
      }
    },
    methods: {
  async createCourse() {
    const title = this.form.title.trim()
    const group = this.form.group.trim()
    const description = this.form.description.trim()
    const resources = this.form.resources.trim()

    if (!title || !group) {
      this.$root.$toast.error('❌ Название и группа обязательны')
      return
    }

    const payload = {
      title,
      group,
      description,
      resources
    }

    try {
      const res = await fetch('http://localhost:8000/create_course.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        body: JSON.stringify(payload)
      })

      const result = await res.json()

      if (res.ok && result.success) {
        this.$root.$toast.success('✅ Курс успешно создан!')
        this.$emit('close') 
        this.$router.push(`/course/${result.course_id}`)
      } else {
        this.$root.$toast.error(result.error || 'Ошибка при создании курса')
      }
    } catch (e) {
      this.$root.$toast.error('❌ Ошибка соединения с сервером')
      console.error(e)
    }
  }
}

}
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
  }
  .modal {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 500px;
    width: 100%;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  input, textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
  }
  .actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
  }
  button {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }
  </style>
  