<template>
  <div class="toast-container">
    <transition-group name="toast-slide" tag="div">
      <div
        v-for="(toast, index) in toasts"
        :key="toast.id"
        :class="['toast', toast.type]"
        :style="{
          zIndex: 1000 + index,
          transform: `translateY(-${index * 12}px)`
        }"
      >
        <i v-if="toast.type === 'success'" class="fas fa-check-circle icon"></i>
        <i v-if="toast.type === 'error'" class="fas fa-exclamation-circle icon"></i>
        <i v-if="toast.type === 'info'" class="fas fa-info-circle icon"></i>
        <span class="text">{{ toast.message }}</span>
        <button class="close-btn" @click="removeToast(toast.id)">✕</button>
      </div>
    </transition-group>
  </div>
</template>

<script>
let idCounter = 0;

export default {
  name: 'ToastManager',
  data() {
    return {
      toasts: []
    };
  },
  methods: {
    showToast(message, type = 'info', timeout = 4000) {
      const id = ++idCounter;
      this.toasts.push({ id, message, type });

      setTimeout(() => this.removeToast(id), timeout);
    },
    success(msg) {
      this.showToast(msg, 'success');
    },
    error(msg) {
      this.showToast(msg, 'error');
    },
    info(msg) {
      this.showToast(msg, 'info');
    },
    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    }
  }
};
</script>

<style scoped>
.toast-container {
  position: fixed;
  bottom: 70px;
  right: 30px;
  z-index: 9999;
  pointer-events: none;
}

.toast {
  position: absolute;
  right: 0;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 14px 18px;
  border-radius: 12px;
  background-color: #fff;
  color: #111827;
  font-weight: 500;
  font-size: 14px;
  min-width: 280px;
  max-width: 360px;
  box-shadow:
    0 4px 6px rgba(0, 0, 0, 0.05),
    0 6px 12px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease;
  pointer-events: auto;
}

.toast.success .icon {
  color: #10b981;
}
.toast.error .icon {
  color: #ef4444;
}
.toast.info .icon {
  color: #3b82f6;
}

.icon {
  font-size: 18px;
  margin-top: 2px;
}

.text {
  flex: 1;
  color: #111827;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  font-size: 16px;
  cursor: pointer;
  padding-left: 6px;
  transition: color 0.2s;
}
.close-btn:hover {
  color: #4b5563;
}

/* Плавная анимация появления из-за границы экрана */
.toast-slide-enter-active {
  transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
}
.toast-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}
.toast-slide-enter-from {
  opacity: 0;
  transform: translateX(100%) translateY(0) !important;
}
.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(100%) translateY(0) !important;
}
.toast-slide-move {
  transition: transform 0.5s;
}
</style>