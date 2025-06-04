document.addEventListener('DOMContentLoaded', function() {
    // Les données seront disponibles dans les variables JS embarquées dans la page PHP

    // Graphique Répartition par Technologie
    if (typeof workOrdersByTechnologyData !== 'undefined') {
        const technologyOptions = {
            chart: {
                type: 'pie',
                height: 350
            },
            series: workOrdersByTechnologyData.counts,
            labels: workOrdersByTechnologyData.technologies,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        const technologyChart = new ApexCharts(document.querySelector("#technologyChart"), technologyOptions);
        technologyChart.render();
    }

    // Graphique Évolution des Work Orders
    if (typeof workOrdersEvolutionData !== 'undefined') {
        const evolutionOptions = {
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Work Orders',
                data: workOrdersEvolutionData.counts
            }],
            xaxis: {
                categories: workOrdersEvolutionData.dates.map(date => new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })).reverse(),
            },
             yaxis: {
                title: {
                    text: 'Nombre de Work Orders'
                }
            },
            colors: ['#007bff'],
            stroke: {
                curve: 'smooth'
            },
            dataLabels: {
                enabled: false
            },
             tooltip: {
                x: {
                    format: 'dd/MM'
                }
            }
        };
        const evolutionChart = new ApexCharts(document.querySelector("#evolutionChart"), evolutionOptions);
        evolutionChart.render();
    }

    // Graphique Répartition des Équipements (par Marque)
    if (typeof equipementsByMarqueData !== 'undefined') {
         const equipementsOptions = {
            chart: {
                type: 'donut',
                height: 350
            },
            series: equipementsByMarqueData.counts,
            labels: equipementsByMarqueData.marques,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
             plotOptions: {
                pie: {
                  donut: {
                    labels: {
                      show: true,
                      total: {
                        showAlways: true,
                        show: true,
                        label: 'Total',
                        formatter: function (w) {
                          return w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0)
                        }
                      }
                    }
                  }
                }
              }
        };
        const equipementsChart = new ApexCharts(document.querySelector("#equipementsChart"), equipementsOptions);
        equipementsChart.render();
    }

});