<template>
    <div class="not-found-page">
      <div class="card">
        <img src="@/assets/404.svg" alt="404" class="illustration" />
        <h2>Ничего не нашлось :(</h2>
        <router-link to="/" class="back-button">Вернуться на главную</router-link>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'NotFoundPage',
  }
  </script>
  
  <style scoped>
  .not-found-page {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
  }
  
  .card {
    background: white;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 420px;
    width: 100%;
    text-align: center;
  }
  
  .illustration {
    width: 200px;
    margin: 0 auto 20px;
  }
  
  h2 {
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 24px;
    color: #111827;
  }
  
  .back-button {
    background-color: #6366f1;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    display: inline-block;
    transition: background 0.2s;
  }
  
  .back-button:hover {
    background-color: #4f46e5;
  }
  </style>
  