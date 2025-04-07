<template>
  <div id="app" :class="{ expanded: isSidebarExpanded }">
    <Sidebar
      v-if="!isAuthOr404Page"
      :expanded="isSidebarExpanded"
      @toggle="toggleSidebar"
    />

    <div class="page" :class="{ 'no-margin': isAuthOr404Page }">
      <Header
        v-if="!isAuthOr404Page"
        :sidebarExpanded="isSidebarExpanded"
        @toggleSidebar="toggleSidebar"
      />

      <main class="content" :class="{ 'auth-content': isAuthOr404Page }">
        <router-view />
      </main>
    </div>

    <ToastManager ref="toast" />
  </div>
</template>

<script>
import Header from '@/components/Header.vue'
import Sidebar from '@/components/Sidebar.vue'
import ToastManager from '@/components/ToastManager.vue'

export default {
  name: 'App',
  components: {
    Header,
    Sidebar,
    ToastManager
  },
  data() {
    return {
      isSidebarExpanded: false
    }
  },
  computed: {
    isAuthOr404Page() {
      const authRoutes = ['Login', 'Register', 'NotFound']
      return authRoutes.includes(this.$route.name)
    }
  },
  methods: {
    toggleSidebar() {
      this.isSidebarExpanded = !this.isSidebarExpanded
    }
  },
  mounted() {
    this.$setToastRef(this.$refs.toast)
  }
}
</script>

<style>
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
  font-family: 'Segoe UI', 'Roboto', sans-serif;
  background-color: #f9fafb;
}

#app {
  display: flex;
  height: 100vh;
  width: 100vw;
  overflow: hidden;
}

.page {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
  height: 100vh;
  transition: margin-left 0.3s ease;
  margin-left: 70px;
}

.expanded .page {
  margin-left: 220px;
}

.page.no-margin {
  margin-left: 0;
}

.content {
  flex: 1;
  overflow-y: auto;
  background-color: #f3f4f6;
  padding: 30px;
  box-sizing: border-box;
  height: 100%;
}

.auth-content {
  background-color: #4880ff;
  background-image: url('@/assets/login-bg.svg');
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  width: 100%;
}
</style>
