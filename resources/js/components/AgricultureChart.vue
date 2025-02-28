<script setup lang="ts">
import { defineProps, computed, onMounted } from "vue";
import { LineChart } from "vue-chart-3"; // ← Kasuta "LineChart"
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

// Registreeri Chart.js moodulid
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
  agricultureData: Array<{ region: string; land_type: string; value: number }>;
}>();

// Logi andmed konsooli (debugging)
onMounted(() => {
  console.log("📊 AgricultureChart.vue: saadud andmed →", props.agricultureData);
});

// Töötle andmed graafiku jaoks
const chartData = computed(() => {
  if (!props.agricultureData || props.agricultureData.length === 0) {
    console.warn("⚠️ Tühjad andmed! Kontrolli API vastust.");
    return { labels: [], datasets: [] };
  }

  const regions = [...new Set(props.agricultureData.map((d) => d.region))]; // Unikaalsed maakonnad
  const labels = regions;
  const datasets = [];

  const landTypes = [...new Set(props.agricultureData.map((d) => d.land_type))];

  const colors = ["#FF6384", "#36A2EB", "#FFCE56"];

  landTypes.forEach((type, index) => {
    datasets.push({
      label: type,
      data: regions.map((region) => {
        const entry = props.agricultureData.find((d) => d.region === region && d.land_type === type);
        return entry ? entry.value : 0;
      }),
      borderColor: colors[index % colors.length],
      borderWidth: 2,
      fill: false,
    });
  });

  return { labels, datasets };
});

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: "top" },
    title: { display: true, text: "Põllumajandusandmete joondiagramm" },
  },
};
</script>

<template>
  <div class="p-4 bg-white shadow rounded-lg">
    <h3 class="text-lg font-semibold mb-2">Põllumajandusandmed</h3>
    <LineChart v-if="chartData.labels.length" :chartData="chartData" :chartOptions="chartOptions" />
    <p v-else class="text-red-500">⚠️ Andmeid ei leitud! Kontrolli API vastust.</p>
  </div>
</template>
