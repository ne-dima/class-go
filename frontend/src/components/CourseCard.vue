<template>
  <div class="card">
    <div class="card-image" :style="{ backgroundImage: `url(${image})` }"></div>
    <div class="card-content">
      <h3 class="card-title">{{ title }}</h3>
      <p class="card-teacher">{{ teacher }}</p>
      <div class="progress-bar">
        <div class="progress-bar-fill" :style="{ width: progress + '%', backgroundColor: color }"></div>
      </div>
      <div class="progress-text">{{ progress }}%</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CourseCard',
  props: {
    title: String,
    teacher: String,
    progress: Number,
    color: String,
    image: String
  }
}
</script>

<style scoped>
.card {
  background: white;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.card-image {
  height: 140px;
  background-size: cover;
  background-position: center;
}

.card-content {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.card-title {
  font-size: 16px;
  font-weight: bold;
  color: #111827;
}

.card-teacher {
  font-size: 13px;
  color: #6b7280;
}

.progress-bar {
  height: 6px;
  background-color: #e5e7eb;
  border-radius: 6px;
  margin-top: 8px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  border-radius: 6px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 12px;
  color: #6b7280;
  text-align: right;
  margin-top: 4px;
}
</style>
