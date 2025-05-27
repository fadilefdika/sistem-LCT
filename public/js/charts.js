const colors = ["#10B981", "#3B82F6", "#F59E0B", "#EF4444", "#8B5CF6"];

// Finding Chart
let findingChart;
function renderFindingChart(labels, data) {
    const ctx = document.getElementById("findingChart").getContext("2d");
    if (findingChart) findingChart.destroy();

    findingChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Findings",
                    data: data,
                    borderColor: "#0069AA",
                    backgroundColor: "rgba(0, 105, 170, 0.1)",
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: "#0069AA",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: { display: true, text: "Date" },
                    ticks: {
                        callback: function (value, index, ticks) {
                            const date = new Date(this.getLabelForValue(value));
                            const day = date
                                .getDate()
                                .toString()
                                .padStart(2, "0");
                            const month = date.toLocaleString("default", {
                                month: "short",
                            });
                            return `${day} ${month}`;
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: "Total Findings" },
                    ticks: { precision: 0 },
                },
            },
        },
    });
}

function loadFindingData(params = {}) {
    const queryString = new URLSearchParams(params).toString();

    fetch(`/ehs/reporting/chart/findings?${queryString}`)
        .then((res) => res.json())
        .then(({ labels, data }) => {
            renderFindingChart(labels, data);
        });
}

// Panggil loadFindingData() saat halaman selesai dimuat
document.addEventListener("DOMContentLoaded", () => {
    loadFindingData();
});

// statusChart
let ctxStatus = document.getElementById("statusChart").getContext("2d");

let statusChart = new Chart(ctxStatus, {
    type: "doughnut",
    data: {
        labels: ["Open", "Closed", "In Progress", "Overdue"],
        datasets: [
            {
                data: [50, 120, 40, 30],
                backgroundColor: ["#F59E0B", "#10B981", "#3B82F6", "#EF4444"],
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            datalabels: {
                color: "#fff",
                font: {
                    weight: "bold",
                    size: 9,
                },
                formatter: (value, ctx) => {
                    let data = ctx.chart.data.datasets[0].data;
                    let total = data.reduce((a, b) => a + b, 0);
                    if (total === 0) return "0%";
                    let percentage = ((value / total) * 100).toFixed(1);
                    return percentage + "%";
                },
                anchor: "center",
                align: "center",
            },
            legend: {
                position: "bottom",
            },
        },
    },
    plugins: [ChartDataLabels],
});

function updateStatusChart() {
    const year = document.getElementById("statusYear").value;
    const month = document.getElementById("statusMonth").value;

    fetch(`/ehs/reporting/chart/status?year=${year}&month=${month || ""}`)
        .then((res) => res.json())
        .then(({ labels, data }) => {
            statusChart.data.labels = labels;
            statusChart.data.datasets[0].data = data;
            statusChart.update();
        })
        .catch((err) => {
            console.error("Failed to fetch status chart data:", err);
        });
}

const yearSelectStatus = document.getElementById("statusYear");
const monthSelectStatus = document.getElementById("statusMonth");

yearSelectStatus.addEventListener("change", () =>
    updateStatusChart(yearSelectStatus.value, monthSelectStatus.value)
);
monthSelectStatus.addEventListener("change", () =>
    updateStatusChart(yearSelectStatus.value, monthSelectStatus.value)
);

// Load initial data
updateStatusChart();

const ctxCategory = document.getElementById("categoryChart").getContext("2d");

const categoryChart = new Chart(ctxCategory, {
    type: "bar",
    data: {
        labels: [],
        datasets: [
            {
                label: "Findings",
                data: [],
                backgroundColor: [], // akan diisi dengan warna #0069AA
            },
        ],
    },
    options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                beginAtZero: true,
            },
        },
    },
});

function updateCategoryChart() {
    const year = document.getElementById("categoryYear").value;
    const month = document.getElementById("categoryMonth").value;

    fetch(`/ehs/reporting/chart/category?year=${year}&month=${month || ""}`)
        .then((res) => res.json())
        .then(({ labels, data }) => {
            categoryChart.data.labels = labels;
            categoryChart.data.datasets[0].data = data;

            // Set semua warna batang menjadi #0069AA
            categoryChart.data.datasets[0].backgroundColor = labels.map(
                () => "#0069AA"
            );

            categoryChart.update();
        })
        .catch((err) => {
            console.error("Failed to fetch category chart data:", err);
        });
}

// Event listener untuk filter
const yearSelectCategory = document.getElementById("categoryYear");
const monthSelectCategory = document.getElementById("categoryMonth");

yearSelectCategory.addEventListener("change", updateCategoryChart);
monthSelectCategory.addEventListener("change", updateCategoryChart);

// Initial render
updateCategoryChart();

const ctxArea = document.getElementById("areaChart").getContext("2d");

const areaChart = new Chart(ctxArea, {
    type: "bar",
    data: {
        labels: [],
        datasets: [
            {
                label: "Findings by Area",
                data: [],
                backgroundColor: "#0069AA",
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                ticks: {
                    font: { size: 9 },
                    maxRotation: 45,
                    minRotation: 45,
                    autoSkip: false,
                },
                maxBarThickness: 5,
                barPercentage: 0.15,
                categoryPercentage: 0.4,
            },
            y: {
                beginAtZero: true,
                ticks: { precision: 0 },
            },
        },
    },
});

function updateAreaChart() {
    const year = document.getElementById("areaYear").value;
    const month = document.getElementById("areaMonth").value;

    fetch(`/ehs/reporting/chart/area?year=${year}&month=${month || ""}`)
        .then((res) => res.json())
        .then(({ labels, data }) => {
            areaChart.data.labels = labels;
            areaChart.data.datasets[0].data = data;
            areaChart.update();
        })
        .catch((err) => {
            console.error("Failed to fetch area chart data:", err);
        });
}

document.getElementById("areaYear").addEventListener("change", updateAreaChart);
document
    .getElementById("areaMonth")
    .addEventListener("change", updateAreaChart);

// Initial render
updateAreaChart();

const ctx = document.getElementById("departmentChart").getContext("2d");

let departmentChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: [],
        datasets: [
            {
                label: "Findings",
                data: [],
                backgroundColor: "#0069AA",
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } },
    },
});

function updateChart() {
    const year = document.getElementById("departmentYear").value;
    const month = document.getElementById("departmentMonth").value;

    fetch(`/ehs/reporting/chart/department?year=${year}&month=${month || ""}`)
        .then((res) => res.json())
        .then(({ labels, data }) => {
            departmentChart.data.labels = labels;
            departmentChart.data.datasets[0].data = data;
            departmentChart.update();
        })
        .catch((err) => {
            console.error("Failed to fetch department chart data:", err);
        });
}

document
    .getElementById("departmentYear")
    .addEventListener("change", updateChart);
document
    .getElementById("departmentMonth")
    .addEventListener("change", updateChart);

// Render awal
updateChart();

// const ctxOverdue = document.getElementById("overdueChart").getContext("2d");

// let overdueChart = new Chart(ctxOverdue, {
//     type: "line",
//     data: {
//         labels: [],
//         datasets: [
//             {
//                 label: "Overdue Count",
//                 data: [],
//                 borderColor: "#EF4444",
//                 backgroundColor: "rgba(239,68,68,0.1)",
//                 fill: true,
//                 tension: 0.3,
//             },
//         ],
//     },
//     options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         scales: {
//             y: {
//                 beginAtZero: true,
//                 ticks: { precision: 0 },
//             },
//         },
//     },
// });

// function updateChart() {
//     const year = document.getElementById("overdueYear").value;
//     const month = document.getElementById("overdueMonth").value;

//     fetch(`/ehs/reporting/chart/overdue?year=${year}&month=${month || ""}`)
//         .then((res) => res.json())
//         .then(({ labelsOverdue, dataOverdue }) => {
//             overdueChart.data.labels = labelsOverdue;
//             overdueChart.data.datasets[0].data = dataOverdue;
//             overdueChart.update();
//         })
//         .catch((err) => console.error("Failed to fetch overdue data:", err));
// }

// document.getElementById("overdueYear").addEventListener("change", updateChart);
// document.getElementById("overdueMonth").addEventListener("change", updateChart);

// // Render awal
// updateChart();
