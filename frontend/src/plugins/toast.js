export default {
    install(app) {
      let toastRef = null
  
      app.config.globalProperties.$setToastRef = (ref) => {
        toastRef = ref
      }
  
      app.config.globalProperties.$toast = {
        success: (msg) => toastRef?.success?.(msg),
        error: (msg) => toastRef?.error?.(msg),
        info: (msg) => toastRef?.info?.(msg)
      }
    }
  }
  