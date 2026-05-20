const ctxM = document.getElementById('congeChartMois').getContext('2d');
const ctxJ = document.getElementById('congeChartJours').getContext('2d');

const congeChartJours = new Chart(ctxJ, {
    type: 'pie',
    data: {
        labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
        datasets: [
            {
                label: 'Le jours ou les congés sont le plus pris',
                data: congeParJours,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4bc068',
                    '#9966FF'
                ],
                borderWidth: 1,
            }
        ]
    },
    options: {
        responsive: true,
        mainTainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
            }
        }
    }
})

const congeChartMois = new Chart(ctxM, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [
            {
                label: 'Nombre de congés rar mois',
                data: congeParMois,
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: '#166534',
                borderWidth: 2,
                borderRadius: 6
            }
        ]
    },
    options:{
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y:{
                beginAtZero: true,
            }
        }
    }
})