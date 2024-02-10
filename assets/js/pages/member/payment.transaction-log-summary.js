"use strict";

if ($("#earningComparision").length) {
  var graphGradient = document.getElementById("earningComparision").getContext('2d');
  var graphGradient2 = document.getElementById("earningComparision").getContext('2d');
  var saleGradientBg = graphGradient.createLinearGradient(5, 0, 5, 100);
  saleGradientBg.addColorStop(0, 'rgba(26, 115, 232, 0.18)');
  saleGradientBg.addColorStop(1, 'rgba(26, 115, 232, 0.02)');
  var saleGradientBg2 = graphGradient2.createLinearGradient(100, 0, 50, 150);
  saleGradientBg2.addColorStop(0, 'rgba(0, 208, 255, 0.19)');
  saleGradientBg2.addColorStop(1, 'rgba(0, 208, 255, 0.03)');
  var salesTopData = {
      labels: comparison_chart_labels,
      datasets: [{
          label: comparison_chart_year,
          data: comparison_chart_data1,
          backgroundColor: saleGradientBg,
          borderColor: [
              '#1F3BB3',
          ],
          borderWidth: 1.5,
          fill: true, // 3: no fill
          pointBorderWidth: 1,
          pointRadius: [4, 4, 4, 4, 4,4, 4, 4, 4, 4,4, 4, 4],
          pointHoverRadius: [2, 2, 2, 2, 2,2, 2, 2, 2, 2,2, 2, 2],
          pointBackgroundColor: ['#1F3BB3)', '#1F3BB3', '#1F3BB3', '#1F3BB3','#1F3BB3)', '#1F3BB3', '#1F3BB3', '#1F3BB3','#1F3BB3)', '#1F3BB3', '#1F3BB3', '#1F3BB3','#1F3BB3)'],
          pointBorderColor: ['#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff',],
      },{
        label: comparison_chart_lastyear,
        data: comparison_chart_data2,
        backgroundColor: saleGradientBg2,
        borderColor: [
            '#52CDFF',
        ],
        borderWidth: 1.5,
        fill: true, // 3: no fill
        pointBorderWidth: 1,
        pointRadius: [0, 0, 0, 4, 0],
        pointHoverRadius: [0, 0, 0, 2, 0],
        pointBackgroundColor: ['#52CDFF)', '#52CDFF', '#52CDFF', '#52CDFF','#52CDFF)', '#52CDFF', '#52CDFF', '#52CDFF','#52CDFF)', '#52CDFF', '#52CDFF', '#52CDFF','#52CDFF)'],
          pointBorderColor: ['#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff','#fff',],
    }]
  };

  var salesTopOptions = {
    responsive: true,
    maintainAspectRatio: false,
      scales: {
          yAxes: [{
              gridLines: {
                  display: true,
                  drawBorder: false,
                  color:"#F0F0F0",
                  zeroLineColor: '#F0F0F0',
              },
              ticks: {
                beginAtZero: false,
                autoSkip: true,
                maxTicksLimit: 4,
                fontSize: 10,
                color:"#6B778C"
              }
          }],
          xAxes: [{
            gridLines: {
                display: false,
                drawBorder: false,
            },
            ticks: {
              beginAtZero: false,
              autoSkip: true,
              maxTicksLimit: 7,
              fontSize: 10,
              color:"#6B778C"
            }
        }],
      },
      legend:false,
      legendCallback: function (chart) {
        var text = [];
        text.push('<div class="chartjs-legend"><ul>');
        for (var i = 0; i < chart.data.datasets.length; i++) {
          text.push('<li>');
          text.push('<span style="background-color:' + chart.data.datasets[i].borderColor + '">' + '</span>');
          text.push(chart.data.datasets[i].label);
          text.push('</li>');
        }
        text.push('</ul></div>');
        return text.join("");
      },
      
      elements: {
          line: {
              tension: 0.4,
          }
      },
      tooltips: {
          backgroundColor: 'rgba(31, 59, 179, 1)',
      }
  }
  var salesTop = new Chart(graphGradient, {
      type: 'line',
      data: salesTopData,
      options: salesTopOptions
  });
  document.getElementById('earning-comparision-legend').innerHTML = salesTop.generateLegend();
}


if ($("#userGain").length) {
  var userGainChart = document.getElementById("userGain").getContext('2d');
  var userGainData = {
      labels: user_summary_label,
      datasets: [{
          label: user_locale,
          data: user_summary_data,
          backgroundColor: "#52CDFF",
          borderColor: [
              '#52CDFF',
          ],
          borderWidth: 0,
          fill: true, // 3: no fill
          
      }]
  };

  var userGainOptions = {
    responsive: true,
    maintainAspectRatio: false,
      scales: {
          yAxes: [{
              gridLines: {
                  display: true,
                  drawBorder: false,
                  color:"rgba(255,255,255,.05)",
                  zeroLineColor: "rgba(255,255,255,.05)",
              },
              ticks: {
                beginAtZero: true,
                autoSkip: true,
                maxTicksLimit: 5,
                fontSize: 10,
                color:"#6B778C"
              }
          }],
          xAxes: [{
            barPercentage: 0.5,
            gridLines: {
                display: false,
                drawBorder: false,
            },
            ticks: {
              beginAtZero: false,
              autoSkip: true,
              maxTicksLimit: 7,
              fontSize: 10,
              color:"#6B778C"
            }
        }],
      },
      legend:false,
      
      elements: {
          line: {
              tension: 0.4,
          }
      },
      tooltips: {
          backgroundColor: 'rgba(31, 59, 179, 1)',
      }
  }
  var userGain = new Chart(userGainChart, {
      type: 'bar',
      data: userGainData,
      options: userGainOptions
  });
}