<template>
    <div class="stat-card" :style="{ borderColor: color }">
      <div class="icon" :style="{ backgroundColor: color + '22' }">{{ icon }}</div>
      <div class="label">{{ label }}</div>
      <div class="value">{{ value }}</div>
    </div>
  </template>
  
  <script>
  export default {
    props: ['label', 'value', 'icon', 'color']
  }
  </script>
  
  <style scoped>
  .stat-card {
    background: white;
    border-left: 4px solid;
    padding: 16px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .icon {
    width: 36px;
    height: 36px;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
  }
  .label {
    font-size: 14px;
    color: #6b7280;
  }
  .value {
    font-size: 18px;
    font-weight: bold;
  }
  </style>
  