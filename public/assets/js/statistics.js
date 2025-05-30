document.addEventListener('DOMContentLoaded', function () {

    // --- Données Simulées (Mock Data) ---
    // Ces données remplaceront les appels API ou PHP/MySQL plus tard
    const mockData = {
        kpis: {
            avgTreatmentTime: "2h 30m",
            globalSuccessRate: "95%",
            manualFailedWO: 15,
            woToday: 8,
            woWeek: 45,
            woMonth: 180
        },
        evolutionWo: {
            day: { labels: ['01', '02', '03', '04', '05', '06', '07'], series: [{ name: 'WO', data: [12, 15, 10, 18, 20, 16, 25] }] },
            week: { labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'], series: [{ name: 'WO', data: [80, 95, 70, 120] }] },
            month: { labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'], series: [{ name: 'WO', data: [300, 350, 280, 400, 420, 380] }] }
        },
        configSuccessRate: {
             day: { labels: ['01', '02', '03', '04', '05', '06', '07'], series: [{ name: 'Taux Succès', data: [90, 92, 88, 95, 96, 91, 98] }] },
             week: { labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'], series: [{ name: 'Taux Succès', data: [85, 90, 88, 93] }] },
             month: { labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'], series: [{ name: 'Taux Succès', data: [88, 89, 87, 91, 92, 90] }] }
        },
         avgTreatmentTime: {
             day: { labels: ['01', '02', '03', '04', '05', '06', '07'], series: [{ name: 'Temps Moyen (min)', data: [150, 160, 140, 180, 170, 155, 190] }] },
             week: { labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'], series: [{ name: 'Temps Moyen (min)', data: [170, 165, 180, 155] }] },
             month: { labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'], series: [{ name: 'Temps Moyen (min)', data: [160, 158, 175, 162, 150, 168] }] }
         },
        techDistribution: { labels: ['FTTH', 'FO', '4G', 'VSAT', 'DSL'], series: [44, 55, 13, 43, 22] },
        equipmentType: { 
             series: [{
                 name: 'Configurés',
                 data: [44, 55, 41, 17, 15]
             }, {
                 name: 'En Stock',
                 data: [13, 23, 20, 8, 13]
             }],
            categories: ['Routeur', 'Switch', 'Firewall', 'AP', 'Serveur']
         },
        userClientTeamDistribution: {
             series: [{
                 name: 'Work Orders',
                 data: [150, 120, 90, 80, 70] // Exemple pour 5 entités (utilisateurs/clients/équipes)
             }],
             categories: ['Utilisateur A', 'Client X', 'Équipe 1', 'Utilisateur B', 'Client Y']
        },
        topUsers: { 
             series: [{
                 name: 'WO Traités',
                 data: [50, 45, 40, 35, 30] // Top 5
             }],
             categories: ['User1', 'User2', 'User3', 'User4', 'User5']
         },
         userActivity: {
              day: { labels: ['01', '02', '03', '04', '05', '06', '07'], series: [{
                  name: 'User1', data: [5, 4, 6, 7, 5, 8, 9] 
              }, { 
                  name: 'User2', data: [3, 5, 4, 6, 7, 4, 5]
              }, {
                  name: 'User3', data: [2, 3, 4, 3, 5, 6, 4]
              }]},
               week: { labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'], series: [{
                  name: 'User1', data: [25, 22, 28, 30] 
              }, { 
                  name: 'User2', data: [18, 20, 15, 22]
              }, {
                  name: 'User3', data: [10, 12, 14, 11]
              }]},
               month: { labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'], series: [{
                  name: 'User1', data: [100, 90, 110, 120, 105, 115] 
              }, { 
                  name: 'User2', data: [70, 75, 65, 80, 78, 72]
              }, {
                  name: 'User3', data: [40, 45, 38, 50, 48, 42]
              }]}
         },
        accessLogs: [
            { dateTime: '2023-10-27 10:00', user: 'admin', type: 'Connexion', ip: '192.168.1.100' },
            { dateTime: '2023-10-27 10:05', user: 'user1', type: 'Connexion', ip: '192.168.1.101' },
            { dateTime: '2023-10-27 10:10', user: 'user_inconnu', type: 'Tentative échouée', ip: '203.0.113.5' },
            { dateTime: '2023-10-27 10:15', user: 'admin', type: 'Déconnexion', ip: '192.168.1.100' },
             { dateTime: '2023-10-27 10:20', user: 'user2', type: 'Connexion', ip: '192.168.1.105' },
              { dateTime: '2023-10-27 10:25', user: 'admin', type: 'Tentative échouée', ip: '203.0.113.6' },
               { dateTime: '2023-10-27 10:30', user: 'user1', type: 'Déconnexion', ip: '192.168.1.101' },
        ]
    };

    // --- Remplir les KPIs ---
    document.getElementById('avg-treatment-time').textContent = mockData.kpis.avgTreatmentTime;
    document.getElementById('global-success-rate').textContent = mockData.kpis.globalSuccessRate;
    document.getElementById('manual-failed-wo').textContent = mockData.kpis.manualFailedWO;
     document.getElementById('wo-today').textContent = mockData.kpis.woToday;
     document.getElementById('wo-week').textContent = mockData.kpis.woWeek;
     document.getElementById('wo-month').textContent = mockData.kpis.woMonth;

    // --- Fonction pour rendre les graphiques Évolution ---
    function renderEvolutionCharts(period) {
        // Graphique Évolution Work-Orders
        const woEvolutionOptions = {
            series: mockData.evolutionWo[period].series,
            chart: {
                type: 'line',
                height: 250,
                toolbar: { show: false }
            },
            xaxis: {
                categories: mockData.evolutionWo[period].labels
            },
            title: { text: 'Évolution Nombre de Work-Orders', align: 'center' }
        };
        // Supprimer l'ancien graphique s'il existe
        const woEvolutionChartElement = document.getElementById('wo-evolution-chart');
         if (woEvolutionChartElement.chart) { woEvolutionChartElement.chart.destroy(); }
        woEvolutionChartElement.chart = new ApexCharts(woEvolutionChartElement, woEvolutionOptions);
        woEvolutionChartElement.chart.render();

        // Graphique Taux de Succès Configurations
         const configSuccessOptions = {
             series: mockData.configSuccessRate[period].series,
             chart: {
                 type: 'line',
                 height: 250,
                 toolbar: { show: false }
             },
             xaxis: {
                 categories: mockData.configSuccessRate[period].labels
             },
              yaxis: { labels: { formatter: function (value) { return value + '%'; } } },
             title: { text: 'Taux de Succès des Configurations', align: 'center' }
         };
          const configSuccessChartElement = document.getElementById('config-success-rate-chart');
          if (configSuccessChartElement.chart) { configSuccessChartElement.chart.destroy(); }
          configSuccessChartElement.chart = new ApexCharts(configSuccessChartElement, configSuccessOptions);
          configSuccessChartElement.chart.render();

         // Graphique Temps de Traitement Moyen
         const avgTimeOptions = {
              series: mockData.avgTreatmentTime[period].series,
              chart: {
                  type: 'line',
                  height: 250,
                  toolbar: { show: false }
              },
              xaxis: {
                  categories: mockData.avgTreatmentTime[period].labels
              },
               yaxis: { labels: { formatter: function (value) { return value + ' min'; } } },
              title: { text: 'Temps de Traitement Moyen', align: 'center' }
          };
           const avgTimeChartElement = document.getElementById('avg-treatment-time-chart');
           if (avgTimeChartElement.chart) { avgTimeChartElement.chart.destroy(); }
           avgTimeChartElement.chart = new ApexCharts(avgTimeChartElement, avgTimeOptions);
           avgTimeChartElement.chart.render();

            // Graphique Activité Utilisateur (adapté par période)
           const userActivityOptions = {
                series: mockData.userActivity[period].series,
                chart: {
                    type: 'line',
                    height: 250,
                    toolbar: { show: false }
                },
                xaxis: {
                    categories: mockData.userActivity[period].labels
                },
                title: { text: 'Activité Utilisateur', align: 'center' }
           };
             const userActivityChartElement = document.getElementById('user-activity-chart');
             if (userActivityChartElement.chart) { userActivityChartElement.chart.destroy(); }
             userActivityChartElement.chart = new ApexCharts(userActivityChartElement, userActivityOptions);
             userActivityChartElement.chart.render();
    }

    // --- Rendre les graphiques Répartition (indépendants de la période dans cet exemple) ---

    // Graphique Répartition par Technologie (Pie Chart)
    const techDistributionOptions = {
        series: mockData.techDistribution.series,
        chart: {
            type: 'pie',
            height: 300,
        },
        labels: mockData.techDistribution.labels,
         title: { text: 'Répartition par Technologie', align: 'center' }
    };
    const techDistributionChart = new ApexCharts(document.getElementById('tech-distribution-chart'), techDistributionOptions);
    techDistributionChart.render();

    // Graphique Types d'Équipements Configurés (Barres empilées)
     const equipmentTypeOptions = {
          series: mockData.equipmentType.series,
          chart: {
              type: 'bar',
              height: 300,
              stacked: true,
          },
          xaxis: {
              categories: mockData.equipmentType.categories,
          },
           title: { text: 'Types d\'Équipements Configurés vs Stock', align: 'center' }
     };
     const equipmentTypeChart = new ApexCharts(document.getElementById('equipment-type-chart'), equipmentTypeOptions);
     equipmentTypeChart.render();

     // Graphique Répartition par Utilisateur/Client/Équipe (Barres simples)
      const userClientTeamOptions = {
           series: mockData.userClientTeamDistribution.series,
           chart: {
               type: 'bar',
               height: 300,
           },
           xaxis: {
               categories: mockData.userClientTeamDistribution.categories,
           },
            title: { text: 'Répartition par Utilisateur/Client/Équipe', align: 'center' }
      };
      const userClientTeamChart = new ApexCharts(document.getElementById('user-client-team-distribution-chart'), userClientTeamOptions);
      userClientTeamChart.render();

     // Graphique Top 5 Utilisateurs (Barres simples)
      const topUsersOptions = {
           series: mockData.topUsers.series,
           chart: {
               type: 'bar',
               height: 300,
           },
           xaxis: {
               categories: mockData.topUsers.categories,
           },
            title: { text: 'Top 5 Utilisateurs Actifs', align: 'center' }
      };
      const topUsersChart = new ApexCharts(document.getElementById('top-users-chart'), topUsersOptions);
      topUsersChart.render();


    // --- Remplir le tableau des Logs d'Accès ---
    const accessLogsTableBody = document.querySelector('#access-logs-table tbody');
    mockData.accessLogs.forEach(log => {
        const row = accessLogsTableBody.insertRow();
        row.insertCell(0).textContent = log.dateTime;
        row.insertCell(1).textContent = log.user;
        row.insertCell(2).textContent = log.type;
        row.insertCell(3).textContent = log.ip;
    });

    // --- Gestion du Sélecteur de Période ---
    const periodSelector = document.getElementById('period-selector');
    periodSelector.addEventListener('change', function() {
        renderEvolutionCharts(this.value);
    });

    // Rendre les graphiques d'évolution pour la période par défaut (Jour)
    renderEvolutionCharts('day');

}); 