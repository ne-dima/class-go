<template>
    <div class="donut">
      <svg viewBox="0 0 36 36" class="donut-chart">
        <path
          class="circle-bg"
          d="M18 2.0845
             a 15.9155 15.9155 0 0 1 0 31.831
             a 15.9155 15.9155 0 0 1 0 -31.831"
        />
        <path
          class="circle"
          :stroke="color"
          :stroke-dasharray="`${progress}, 100`"
          d="M18 2.0845
             a 15.9155 15.9155 0 0 1 0 31.831
             a 15.9155 15.9155 0 0 1 0 -31.831"
        />
        <text x="18" y="20.35" class="percentage">{{ progress }}%</text>
      </svg>
      <div class="title">{{ title }}</div>
    </div>
  </template>
  
  <script>
  export default {
    props: ['progress', 'title', 'color']
  }
  </script>
  
  <style scoped>
  .donut {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 120px;
  }
  .donut-chart {
    width: 100px;
    height: 100px;
  }
  .circle-bg {
    fill: none;
    stroke: #eee;
    stroke-width: 3.8;
  }
  .circle {
    fill: none;
    stroke-width: 3.8;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: center;
    transition: stroke-dasharray 0.3s ease;
  }
  .percentage {
    font-size: 0.5em;
    text-anchor: middle;
    fill: #111827;
  }
  .title {
    margin-top: 10px;
    font-size: 14px;
  }
  </style>
  