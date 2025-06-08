<template>
    <div class="h-full w-full">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    chartData: Object
});

const chartCanvas = ref(null);
let chart = null;

// Chart constructor
const createChart = () => {
    if (!chartCanvas.value || !props.chartData) return;

    const ctx = chartCanvas.value.getContext('2d');

    // Prepare data
    const labels = [...props.chartData.labels].reverse();
    const data = [...props.chartData.data].reverse();

    // Prepare data
    const formattedLabels = labels.map(label => {
        const date = new Date(label);
        return date.toLocaleDateString('vi-VN', { day: 'numeric', month: 'numeric' });
    });

    // Create a chart
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedLabels,
            datasets: [{
                label: 'Orders',
                data: data,
                fill: {
                    target: 'origin',
                    above: 'rgba(59, 130, 246, 0.1)'
                },
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                tension: 0.3,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 10,
                    cornerRadius: 4,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(243, 244, 246, 1)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    });
};

// Update chart when data changes
watch(() => props.chartData, () => {
    if (chart) {
        chart.destroy();
    }
    createChart();
}, { deep: true });

// Initialize the chart when the component is mounted
onMounted(() => {
    createChart();

    // Responsive handling
    const handleResize = () => {
        if (chart) {
            chart.resize();
        }
    };

    window.addEventListener('resize', handleResize);

    // Cleanup khi unmount
    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize);
        if (chart) {
            chart.destroy();
        }
    });
});
</script>
